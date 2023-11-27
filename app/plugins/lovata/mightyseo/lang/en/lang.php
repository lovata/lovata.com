<?php return [
    'plugin'     => [
        'name'        => 'Mighty SEO',
        'description' => 'Toolbox to manage SEO settings',
    ],
    'field'      => [
        'seo_title_prefix'             => 'Prefix for SEO title',
        'seo_title_prefix_description' => 'The prefix for the SEO title will be automatically populated on all pages',
        'seo_title_suffix'             => 'Suffix for SEO title',
        'seo_title_suffix_description' => 'Suffix for the SEO title will be automatically populated on all pages',
        'head_begin'                   => 'Insertion code after tag <head>',
        'head_end'                     => 'Insertion code before tag </head>',
        'body_begin'                   => 'Insertion code after tag <body>',
        'body_end'                     => 'Insertion code before tag </body>',
        'seo_title'                    => 'SEO title',
        'seo_description'              => 'SEO description',
        'seo_keywords'                 => 'SEO keywords',
        'page_id'                      => 'Page',
        'page_h1'                      => 'H1 page title',
        'page_description'             => 'Page description',
        'robots_follow'                => 'Robots Follow',
        'robots_index'                 => 'Robots Index',
        'robots_index_addition'        => 'Additional options for Robots Index',
        'canonical_url'                => 'Canonical URL',
    ],
    'tab'        => [
        'seo_settings' => 'SEO',
        'mighty_seo'   => 'Manage SEO settings',
        'seo_template' => 'SEO templates',
        'seo_params'   => 'SEO parameters',
    ],
    'menu'       => [
        'main'                 => 'SEO',
        'seo_template'         => 'SEO templates',
        'seo_page'             => 'SEO for pages',
        'seo_model'            => 'SEO for elements',
        'settings'             => 'SEO settings',
        'settings_description' => 'Common SEO settings',
    ],
    'component'  => [
        'seo_toolbox_name'        => 'SEO Toolbox',
        'seo_toolbox_description' => '',
    ],
    'permission' => [
        'mighty_seo' => 'Manage SEO',
    ],
    'template'   => [
        'name'       => 'template',
        'list_title' => 'Template list',
    ],
    'param'      => [
        'name'       => 'SEO settings for page',
        'list_title' => 'List of SEO settings for page',
    ],
    'seo_param'  => [
        'name'       => 'SEO parameters',
        'list_title' => 'List of SEO parameters',
    ],
];