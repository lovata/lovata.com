<?php return [
    'plugin'     => [
        'name'        => 'Feedback for CRM',
        'description' => '',
    ],
    'menu'       => [
        'amo_crm' => 'AmoCRM',
    ],
    'field'      => [
        'tags'             => 'Список тегов (через запятую)',
        'key'              => 'Ключ',
        'field_in_crm'     => 'Поле в CRM',
        'custom_fields'    => 'Настраиваемые поля',
        'responsible_user' => 'Пользователь ответственный за сделку',
        'pipeline'         => 'Воронка',
        'feedback_form'    => 'Форма обратной связи',
        'client_id'        => 'Client ID',
        'client_secret'    => 'Client secret',
        'code'             => 'Code',
        'redirect_uri'     => 'Redirect uri',
        'domain_name'      => 'Domain name',
    ],
    'component'  => [],
    'tab'        => [
        'feedback_form' => 'Форма обратной связи',
    ],
    'permission' => [
        'feedback_for_crm' => 'Форма обрятной связи для CRM',
    ],
];
