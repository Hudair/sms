<?php

return [
    'custom' => [
        'mainLayoutType' => 'vertical', // Options[String]: vertical(default), horizontal
        'theme' => 'light', // options[String]: 'light'(default), 'dark', 'semi-dark' (warning:semi-dark option not applies to the horizontal theme)
        'sidebarCollapsed' => false, // options[Boolean]: true, false(default) (warning:this option only applies to the vertical theme.)
        'navbarColor' => 'bg-primary', // options[String]: bg-primary, bg-info, bg-warning, bg-success, bg-danger, bg-dark (default: '' for #fff)
        'horizontalMenuType' => 'sticky', // options[String]: floating(default) / static /sticky (Warning:this option only applies to the Horizontal theme.)
        'verticalMenuNavbarType' => 'sticky', // options[String]: floating(default) / static / sticky / hidden (Warning:this option only applies to the vertical theme)
        'footerType' => 'sticky', // options[String]: static(default) / sticky / hidden
        'bodyClass' => '', // add custom class
        'pageHeader' => true, // options[Boolean]: true(default), false (Page Header for Breadcrumbs)
        'contentLayout' => 'default', // options[String]: default, content-left-sidebar, content-right-sidebar, content-detached-left-sidebar, content-detached-right-sidebar (warning:use this option if your whole project with sidenav Otherwise override this option as page level )
        'blankPage' => false, // options[Boolean]: true, false(default) (warning:only make true if your whole project without navabr and sidebar otherwise override option page wise)
        'direction' => env('MIX_CONTENT_DIRECTION', 'ltr'), // Options[String]: ltr(default), rtl
    ],
];
