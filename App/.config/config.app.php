<?php
return[
    'name' => 'MyApp',
    'version' => '1.0.0',
    'base_url' => 'http://localhost/public',
    'debug' => true,
    'logoPath' => '/assets/logo.png',
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'myapp',
    ],
    'navLinks' => [
        'home' => [
            'label' => 'Home',
            'url' => '/',
            'active' => true,
        ],
        'about' => [
            'label' => 'About',
            'url' => '/about',
            'active' => false,
        ],
        'contact' => [
            'label' => 'Contact',
            'url' => '/contact',
            'active' => false,
        ],
    ],
    'footerLinks' => [
        'privacy' => [
            'label' => 'Privacy Policy',
            'url' => '/privacy',
        ],
        'terms' => [
            'label' => 'Terms of Service',
            'url' => '/terms',
        ],
    ],
    'socialLinks' => [
        'facebook' => [
            'label' => 'Facebook',
            'url' => 'https://www.facebook.com',
            'icon' => 'fa-facebook',
        ],
        'twitter' => [
            'label' => 'Twitter',
            'url' => 'https://www.twitter.com',
            'icon' => 'fa-twitter',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'url' => 'https://www.instagram.com',
            'icon' => 'fa-instagram',
        ],
    ],
    'paths' => [
        'base' => '/var/www/html/',
        'appPath' => 'App/',
        'viewPath' => 'Views/',
        'publicPath' => 'public/',
        'templatePath' => 'Views/Templates/',
        'assetPath' => 'assets/',
        'jsPaths' => [
            'js/',
            'vendor/js/' 
        ],
        'cssPaths' => [
            'css/',
            'vendor/css/'
        ],
        'imgPaths' => [
            'images/',
            'vendor/images/'
        ],
        'fontsPaths' => [
            'fonts/',
            'vendor/fonts/'
        ],
        'baseTemplatePath' => 'Views/Templates/template.base.html'
    ],"Template" =>["asset_flags" => "css,js,img,fonts",
        "asset_paths" => [
            'css' => [
                'style.css',
                'vendor/css/bootstrap.min.css'
            ],
            'js' => [
                'app.js',
                'vendor/js/jquery.min.js'
            ],
            'img' => [
                'logo.png',
                'vendor/images/logo.png'
            ],
            'fonts' => [
                'OpenSans-Regular.ttf',
                'vendor/fonts/OpenSans-Bold.ttf'
            ]
        ]
    ];