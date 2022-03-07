<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    */

    // Dashboard Module
        'access_backend'            => [
                'display_name' => 'dashboard',
                'category'     => 'Dashboard',
                'default'      => true,
        ],

    // reports Module
        'view_reports'               => [
                'display_name' => 'view_reports',
                'category'     => 'Reports',
                'default'      => true,
        ],

    // charts Module
        'create_sending_servers'    => [
                'display_name' => 'create_sending_servers',
                'category'     => 'Sending Servers',
                'default'      => false,
        ],

    //contacts module
        'view_contact_group'        => [
                'display_name' => 'read_contact_group',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'create_contact_group'      => [
                'display_name' => 'create_contact_group',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'update_contact_group'      => [
                'display_name' => 'update_contact_group',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'delete_contact_group'      => [
                'display_name' => 'delete_contact_group',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'view_contact'              => [
                'display_name' => 'read_contact',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'create_contact'            => [
                'display_name' => 'create_contact',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'update_contact'            => [
                'display_name' => 'update_contact',
                'category'     => 'Contacts',
                'default'      => true,
        ],
        'delete_contact'            => [
                'display_name' => 'delete_contact',
                'category'     => 'Contacts',
                'default'      => true,
        ],

    //numbers module
        'view_numbers'              => [
                'display_name' => 'read_numbers',
                'category'     => 'Phone Numbers',
                'default'      => false,
        ],
        'buy_numbers'               => [
                'display_name' => 'buy_numbers',
                'category'     => 'Phone Numbers',
                'default'      => false,
        ],
        'release_numbers'           => [
                'display_name' => 'release_numbers',
                'category'     => 'Phone Numbers',
                'default'      => false,
        ],

    //keywords module
        'view_keywords'             => [
                'display_name' => 'read_keywords',
                'category'     => 'Keywords',
                'default'      => false,
        ],
        'buy_keywords'              => [
                'display_name' => 'buy_keywords',
                'category'     => 'Keywords',
                'default'      => false,
        ],
        'update_keywords'           => [
                'display_name' => 'update_keywords',
                'category'     => 'Keywords',
                'default'      => false,
        ],
        'release_keywords'          => [
                'display_name' => 'release_keywords',
                'category'     => 'Keywords',
                'default'      => false,
        ],

    //sender id
        'view_sender_id'            => [
                'display_name' => 'read_sender_id',
                'category'     => 'Sender ID',
                'default'      => true,
        ],
        'create_sender_id'          => [
                'display_name' => 'request_sender_id',
                'category'     => 'Sender ID',
                'default'      => true,
        ],
        'delete_sender_id'          => [
                'display_name' => 'delete_sender_id',
                'category'     => 'Sender ID',
                'default'      => false,
        ],

    //blacklist
        'view_blacklist'            => [
                'display_name' => 'read_blacklist',
                'category'     => 'Blacklist',
                'default'      => true,
        ],
        'create_blacklist'          => [
                'display_name' => 'create_blacklist',
                'category'     => 'Blacklist',
                'default'      => true,
        ],
        'delete_blacklist'          => [
                'display_name' => 'delete_blacklist',
                'category'     => 'Blacklist',
                'default'      => true,
        ],

    //sms module
        'sms_campaign_builder'      => [
                'display_name' => 'campaign_builder',
                'category'     => 'SMS',
                'default'      => true,
        ],
        'sms_quick_send'            => [
                'display_name' => 'quick_send',
                'category'     => 'SMS',
                'default'      => true,
        ],
        'sms_bulk_messages'         => [
                'display_name' => 'bulk_messages',
                'category'     => 'SMS',
                'default'      => true,
        ],

    //voice module
        'voice_campaign_builder'    => [
                'display_name' => 'campaign_builder',
                'category'     => 'Voice',
                'default'      => false,
        ],
        'voice_quick_send'          => [
                'display_name' => 'quick_send',
                'category'     => 'Voice',
                'default'      => false,
        ],
        'voice_bulk_messages'       => [
                'display_name' => 'bulk_messages',
                'category'     => 'Voice',
                'default'      => false,
        ],

    //mms module
        'mms_campaign_builder'      => [
                'display_name' => 'campaign_builder',
                'category'     => 'MMS',
                'default'      => false,
        ],
        'mms_quick_send'            => [
                'display_name' => 'quick_send',
                'category'     => 'MMS',
                'default'      => false,
        ],
        'mms_bulk_messages'         => [
                'display_name' => 'bulk_messages',
                'category'     => 'MMS',
                'default'      => false,
        ],

    //whatsapp module
        'whatsapp_campaign_builder' => [
                'display_name' => 'campaign_builder',
                'category'     => 'WhatsApp',
                'default'      => false,
        ],
        'whatsapp_quick_send'       => [
                'display_name' => 'quick_send',
                'category'     => 'WhatsApp',
                'default'      => false,
        ],
        'whatsapp_bulk_messages'    => [
                'display_name' => 'bulk_messages',
                'category'     => 'WhatsApp',
                'default'      => false,
        ],

    //sms template
        'sms_template'              => [
                'display_name' => 'sms_template',
                'category'     => 'SMS Template',
                'default'      => true,
        ],

    //chat box
        'chat_box'                  => [
                'display_name' => 'chat_box',
                'category'     => 'Chat Box',
                'default'      => false,
        ],

    //knowledge bases
        'developers'                => [
                'display_name' => 'developers',
                'category'     => 'Developers',
                'default'      => true,
        ],
];
