<?php return [
    'plugin'     => [
        'name'        => 'Mighty SEO',
        'description' => 'Инструменты для управления SEO',
    ],
    'field'      => [
        'seo_title_prefix'             => 'Префикс для SEO заголовка',
        'seo_title_prefix_description' => 'Префикс для SEO заголовка будет автоматически подставляться на всех страницах',
        'seo_title_suffix'             => 'Суфикс для SEO заголовка',
        'seo_title_suffix_description' => 'Суфикс для SEO заголовка будет автоматически подставляться на всех страницах',
        'head_begin'                   => 'Код для вставки после тега <head>',
        'head_end'                     => 'Код для вставки перед тегом </head>',
        'body_begin'                   => 'Код для вставки после тега <body>',
        'body_end'                     => 'Код для вставки перед тегом </body>',
        'seo_title'                    => 'SEO заголовок',
        'seo_description'              => 'SEO description',
        'seo_keywords'                 => 'SEO keywords',
        'page_id'                      => 'Страница',
        'page_h1'                      => 'Заголовок H1 страницы',
        'page_description'             => 'Описание страницы',
        'robots_follow'                => 'Robots Follow',
        'robots_index'                 => 'Robots Index',
        'robots_index_addition'        => 'Дополнительные параметры для Robots Index',
        'canonical_url'                => 'Canonical URL',
    ],
    'tab'        => [
        'seo_settings' => 'SEO',
        'mighty_seo'   => 'Управление настройками SEO',
        'seo_template' => 'SEO шаблоны',
        'seo_params'   => 'SEO параметры',
    ],
    'menu'       => [
        'main'                 => 'SEO',
        'seo_template'         => 'SEO шаблоны',
        'seo_page'             => 'SEO для страниц',
        'seo_model'            => 'SEO для элементов',
        'settings'             => 'Настройки SEO',
        'settings_description' => 'Общие настройки SEO',
    ],
    'component'  => [
        'seo_toolbox_name'        => 'SEO Toolbox',
        'seo_toolbox_description' => '',
    ],
    'permission' => [
        'mighty_seo' => 'Управление SEO',
    ],
    'template'   => [
        'name'       => 'шаблона',
        'list_title' => 'Список шаблонов',
    ],
    'param'      => [
        'name'       => 'SEO настроек для страницы',
        'list_title' => 'Список SEO настроек для страниц',
    ],
    'seo_param'  => [
        'name'       => 'SEO параметров',
        'list_title' => 'Список SEO параметров',
    ],
];