<?php
return [
        'vertical' => [
                'mainLayoutType'         => 'vertical', // Options[String]: vertical(default), horizontal
                'theme'                  => env('THEME_SKIN', 'light'), // options[String]: 'light'(default), 'dark', 'bordered', 'semi-dark'
                'sidebarCollapsed'       => env('THEME_MENU_COLLAPSED', false), // options[Boolean]: true, false(default) (warning:this option only applies to the vertical theme.)
                'navbarColor'            => env('THEME_NAVBAR_COLOR','bg-primary'), // options[String]: bg-primary, bg-info, bg-warning, bg-success, bg-danger, bg-dark (default: '' for #fff)
                'horizontalMenuType'     => env('THEME_NAVBAR_TYPE','sticky'), // options[String]: floating(default) / static /sticky (Warning:this option only applies to the Horizontal theme.)
                'verticalMenuNavbarType' => env('THEME_NAVBAR_TYPE','sticky'), // options[String]: floating(default) / static / sticky / hidden (Warning:this option only applies to the vertical theme)
                'footerType'             => env('THEME_FOOTER_TYPE','sticky'), // options[String]: static(default) / sticky / hidden
                'layoutWidth'            => env('THEME_LAYOUT_WIDTH','full'), // options[String]: full / boxed(default),
                'showMenu'               => true, // options[Boolean]: true(default), false //show / hide main menu (Warning: if set to false it will hide the main menu)
                'bodyClass'              => '', // add custom class
                'pageHeader'             => env('THEME_BREADCRUMBS', true), // options[Boolean]: true(default), false (Page Header for Breadcrumbs)
                'contentLayout'          => 'default', // options[String]: default, content-left-sidebar, content-right-sidebar, content-detached-left-sidebar, content-detached-right-sidebar (warning:use this option if your whole project with sidenav Otherwise override this option as page level )
                'defaultLanguage'        => 'en',    //en(default)/de/pt/fr here are four optional language provided in theme
                'blankPage'              => false, // options[Boolean]: true, false(default) (warning:only make true if your whole project without navabr and sidebar otherwise override option page wise)
                'direction'              => env('APP_DIRECTION', 'ltr'), // Options[String]: ltr(default), rtl
        ],

        'horizontal' => [
                'mainLayoutType'         => 'horizontal', // Options[String]: vertical(default), horizontal
                'theme'                  => env('THEME_SKIN', 'light'), // options[String]: 'light'(default), 'dark', 'bordered', 'semi-dark'
                'sidebarCollapsed'       => env('THEME_MENU_COLLAPSED', false), // options[Boolean]: true, false(default) (warning:this option only applies to the vertical theme.)
                'navbarColor'            => env('THEME_NAVBAR_COLOR','bg-primary'), // options[String]: bg-primary, bg-info, bg-warning, bg-success, bg-danger, bg-dark (default: '' for #fff)
                'horizontalMenuType'     => env('THEME_NAVBAR_TYPE','sticky'), // options[String]: floating(default) / static /sticky (Warning:this option only applies to the Horizontal theme.)
                'verticalMenuNavbarType' => env('THEME_NAVBAR_TYPE','sticky'), // options[String]: floating(default) / static / sticky / hidden (Warning:this option only applies to the vertical theme)
                'footerType'             => env('THEME_FOOTER_TYPE','sticky'), // options[String]: static(default) / sticky / hidden
                'layoutWidth'            => env('THEME_LAYOUT_WIDTH','full'), // options[String]: full / boxed(default),
                'showMenu'               => true, // options[Boolean]: true(default), false //show / hide main menu (Warning: if set to false it will hide the main menu)
                'bodyClass'              => '', // add custom class
                'pageHeader'             => env('THEME_BREADCRUMBS', true), // options[Boolean]: true(default), false (Page Header for Breadcrumbs)
                'contentLayout'          => 'default', // options[String]: default, content-left-sidebar, content-right-sidebar, content-detached-left-sidebar, content-detached-right-sidebar (warning:use this option if your whole project with sidenav Otherwise override this option as page level )
                'defaultLanguage'        => 'en',    //en(default)/de/pt/fr here are four optional language provided in theme
                'blankPage'              => false, // options[Boolean]: true, false(default) (warning:only make true if your whole project without navabr and sidebar otherwise override option page wise)
                'direction'              => env('APP_DIRECTION', 'ltr'), // Options[String]: ltr(default), rtl
        ],
];
