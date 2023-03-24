<?php

if (!defined('FW')) {
    die('Forbidden');
}

class FW_Extension_QuestionsAnswers extends FW_Extension {

    /**
     * @internal
     */
    public function _init() {
        add_action('init',array(&$this,'register_post_type'));
		add_filter( 'manage_edit-sp_answers_columns', array(&$this,'cpt_answer_columns') );
		add_action( 'manage_sp_answers_posts_custom_column', array(&$this,'cpt_answer_row_actions'), 10, 2 );
		
		add_filter( 'manage_edit-sp_questions_columns', array(&$this,'cpt_questions_columns') );
		add_action( 'manage_sp_questions_posts_custom_column', array(&$this,'cpt_questions_row_actions'), 10, 2 );
    }

	/**
	 * Answer CPT columns.
	 *
	 * @param  array $columns Columns.
	 */
	public function cpt_answer_columns( $columns ) {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'answer_content'    => esc_html__( 'Content', 'cannalisting_core' ),
			'votes'    			=> esc_html__( 'Votes', 'cannalisting_core' ),
			'date'              => esc_html__( 'Date', 'cannalisting_core' ),
		);

		return $columns;
	}
	
	/**
	 * Questions CPT columns.
	 *
	 * @param  array $columns Columns.
	 */
	public function cpt_questions_columns( $columns ) {
		unset($columns['date']);
		$votes	= array(
				'votes'  => esc_html__( 'Votes', 'cannalisting_core' ),
				'date'   => esc_html__( 'Date', 'cannalisting_core' ),
		);
		$columns	= array_merge($columns,$votes);

		return $columns;
	}
	
	/**
	 * Questions CPT columns values.
	 *
	 * @param  array $columns Columns.
	 */
	public function cpt_questions_row_actions( $column, $post_id ) {
		if ( 'votes' == $column ) {
			$total_votes = get_post_meta($post_id, 'total_votes', true);
			echo intval($total_votes);
		}
	}
	
	/**
	 * Add action links below question/answer content in wp post list.
	 *
	 * @param  string  $column  Current column name.
	 * @param  integer $post_id Current post id.
	 */
	public function cpt_answer_row_actions( $column, $post_id ) {
		global $post, $mode;
		
		if ( 'votes' == $column ) {
			$total_votes = get_post_meta($post_id, 'total_votes', true);
			echo intval($total_votes);
			return;
		}
		
		if ( 'answer_content' == $column ) {
			$content = $this->cpt_truncate_chars( esc_html( get_the_excerpt() ), 70 );
			echo '<a href="' . esc_url( get_permalink( $post->post_parent ) ) . '" class="row-title">' . $content . '</a>'; // xss okay.
		}
		
		
		
		// First set up some variables.
		$actions          = array();
		$post_type_object = get_post_type_object( $post->post_type ); // override ok.
		$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );

		// Actions to delete/trash.
		if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
			if ( 'trash' === $post->post_status ) {
				$_wpnonce           = wp_create_nonce( 'untrash-post_' . $post_id );
				$url                = admin_url( 'post.php?post=' . $post_id . '&action=untrash&_wpnonce=' . $_wpnonce );
				$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'cannalisting_core' ) ) . "' href='" . $url . "'>" . __( 'Restore', 'cannalisting_core' ) . '</a>';

			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'cannalisting_core' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', 'cannalisting_core' ) . '</a>';
			}

			if ( 'trash' === $post->post_status || ! EMPTY_TRASH_DAYS ) {
				$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'cannalisting_core' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently', 'cannalisting_core' ) . '</a>';
			}
		}

		if ( $can_edit_post ) {
			$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, '', true ) . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'cannalisting_core' ),$post->title ) ) . '" rel="permalink">' . __( 'Edit', 'cannalisting_core' ) . '</a>';
		}

		// Echo the 'actions' HTML, let WP_List_Table do the hard work.
		$WP_List_Table = new WP_List_Table(); // @codingStandardsIgnoreLine
		echo do_shortcode($WP_List_Table->row_actions( $actions ) );

	}
	
	/**
	 * Trim strings.
	 *
	 * @param string $text String.
	 * @param int    $limit Limit string to.
	 * @param string $ellipsis Ellipsis.
	 * @return string
	 */
	public function cpt_truncate_chars( $text, $limit = 40, $ellipsis = '...' ) {
		$text = str_replace( array( "\r\n", "\r", "\n", "\t" ), ' ', $text );
		if ( strlen( $text ) > $limit ) {
			$endpos = strpos( $text, ' ', (string) $limit );
			if ( false !== $endpos ) {
				$text = trim( substr( $text, 0, $endpos ) ) . $ellipsis;
			}
		}
		return $text;
	}
	
    /**
     * @Render Question Add View
     * @return type
     */
    public function render_add_questions() {
        return $this->render_view('add_question');
    }
    
    /**
     * @Render Question Add View
     * @return type
     */
    public function render_questions_view() {
        return $this->render_view('view_questions');
    }
    
    /**
     * @Render Question Add View
     * @return type
     */
    public function render_questions_add() {
        return $this->render_view('add_question');
    }
    
    /**
     * @Render Question Add View
     * @return type
     */
    public function render_answers_view() {
        return $this->render_view('view_answers');
    }

    /**
     * @access Private
     * @Register Post Type
     */
    public function register_post_type() {
		if( function_exists('cannalisting_get_theme_settings') ){
			$question_slug	= cannalisting_get_theme_settings('question_slug');
		}
		
		$question_slug	=  !empty( $question_slug ) ? $question_slug : 'question';
		
        register_post_type('sp_questions', array(
            'labels' => array(
                'name' => esc_html__('Consult Q&A', 'cannalisting_core'),
                'all_items' => esc_html__('Questions', 'cannalisting_core'),
                'singular_name' => esc_html__('Question', 'cannalisting_core'),
                'add_new' => esc_html__('New Question', 'cannalisting_core'),
                'add_new_item' => esc_html__('Add New Question', 'cannalisting_core'),
                'edit' => esc_html__('Edit', 'cannalisting_core'),
                'edit_item' => esc_html__('Edit Question', 'cannalisting_core'),
                'new_item' => esc_html__('New Question', 'cannalisting_core'),
                'view' => esc_html__('View Question', 'cannalisting_core'),
                'view_item' => esc_html__('View Question', 'cannalisting_core'),
                'search_items' => esc_html__('Search Question', 'cannalisting_core'),
                'not_found' => esc_html__('No Question found', 'cannalisting_core'),
                'not_found_in_trash' => esc_html__('No Question found in trash', 'cannalisting_core'),
                'parent' => esc_html__('Parent Question', 'cannalisting_core'),
            ),
			'capabilities' => array('create_posts' => false), //Hide add New Button
            'description' => esc_html__('This is where you can add new Questions.', 'cannalisting_core'),
            'public' => true,
            'supports' => array('title', 'editor'),
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
            'hierarchical' => true,
            'menu_position' => 10,
            'rewrite' => array('slug' => $question_slug, 'with_front' => true),
            'query_var' => true,
            'has_archive' => 'false'
        ));
        register_post_type('sp_answers', array(
            'labels' => array(
                'name' => esc_html__('Answers', 'cannalisting_core'),
                'all_items' => esc_html__('Answers', 'cannalisting_core'),
                'singular_name' => esc_html__('New Answer', 'cannalisting_core'),
                'add_new' => esc_html__('Add Answer', 'cannalisting_core'),
                'add_new_item' => esc_html__('Add New Answer', 'cannalisting_core'),
                'edit' => esc_html__('Edit', 'cannalisting_core'),
                'edit_item' => esc_html__('Edit Answer', 'cannalisting_core'),
                'new_item' => esc_html__('New Answer', 'cannalisting_core'),
                'view' => esc_html__('View Answer', 'cannalisting_core'),
                'view_item' => esc_html__('View Answer', 'cannalisting_core'),
                'search_items' => esc_html__('Search Answer', 'cannalisting_core'),
                'not_found' => esc_html__('No Answer found', 'cannalisting_core'),
                'not_found_in_trash' => esc_html__('No Answer found in trash', 'cannalisting_core'),
                'parent' => esc_html__('Parent Answer', 'cannalisting_core'),
            ),
			'capabilities' => array('create_posts' => false), //Hide add New Button
            'description' => esc_html__('This is where you can add new Answers.', 'cannalisting_core'),
            'public' => true,
            'supports' => array('editor'),
			'show_in_menu' => 'edit.php?post_type=sp_questions',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'menu_position' => 10,
            'rewrite' => array('slug' => 'answer', 'with_front' => true),
            'query_var' => false,
            'has_archive' => 'false'
        ));
    }

}
