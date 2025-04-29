<?php
$strBasePath = "/";
$strAppPath = $strBasePath . 'App/';
$strPublicPath = $strBasePath . 'public/';
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
        'basePath' => '/var/www/html/',
        'baseUrl' => 'http://localhost/',
        'baseUri' => '/',
        'appPath' => $strAppPath,
        'viewPath' => 'App/Views/',
        'publicPath' => $strBasePath . 'public/',
        'templatePath' => 'App/Views/Templates/',
        'assetUri' => 'public/assets/',
        'baseTemplate' => '../App/Views/Templates/template.base.html'
    ],"Template" =>[
        "assets" => [
            'css/' => [
                'style.css',
                'bootstrap.min.css'
            ],
            'js/' => [
                'app.js',
                'jquery.min.js'
            ],
            'img/' => [
                'logo.png',
                'vendor/images/logo.png'
            ],
            'fonts/' => [
                'OpenSans-Regular.ttf',
                'vendor/fonts/OpenSans-Bold.ttf'
            ]
            ]
    ]
];