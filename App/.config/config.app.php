<?php
defined('APP_INIT') or die('Direct access not allowed.');
$strBasePath = "/";
$strAppPath = $strBasePath . 'App/';
$strPublicPath = $strBasePath . 'public/';
return[
    'appTitle' => 'HTTPStack',
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
        'Home' => [
            'icon' => 'bi bi-house',
            'url' => '/',
            'active' => true,
        ],
        'Resume' => [
            'icon' => 'bi bi-file-earmark-text',
            'url' => '/resume',
            'active' => false,
        ],
        'Contact' => [
            'icon' => 'bi bi-envelope',
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
        'Facebook' => [
            'url' => 'https://www.facebook.com',
            'icon' => 'fa-facebook',
        ],
        'Twitter' => [
            'url' => 'https://www.twitter.com',
            'icon' => 'fa-twitter',
        ],
        'Instagram' => [
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
        'assetsPath' => 'public/assets/',
        'assetsUri' => 'assets/',
        'baseTemplate' => '../App/Views/Templates/template.base.html'
    ],
    "assets" =>[
                "vendor/js/jquery.js",
                "css/style.css",
                "vendor/css/bootstrap-icons.css",
                "js/app.js"
    ],
    "required" => [
        "vendor/js/jquery.js"
    ]
];