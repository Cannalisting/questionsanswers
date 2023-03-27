<?php
if (!defined('FW'))
    die('Forbidden');

$manifest = array();
$manifest['name'] = esc_html__('Questions and Answers', 'cannalisting_core');
$manifest['uri'] = 'cannalisting-theme-uri';
$manifest['description'] = esc_html__('This extension will enable users to post question and answers at provider detail page.', 'cannalisting_core');
$manifest['version'] = '1.0';
$manifest['author'] = 'cannalisting-theme-author';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['author_uri'] = 'cannalisting-theme-uri';
$manifest['github_repo'] = 'https://github.com/Cannalisting/questionsanswers';
$manifest['github_update'] = 'Cannalisting/questionsanswers';
$manifest['requirements'] = array(
    'wordpress' => array(
        'min_version' => '4.0',
    )
);

$manifest['thumbnail'] = '/static/img/thumbnails/questions.png';
