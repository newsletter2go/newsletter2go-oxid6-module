<?php

/**
 * @category      module
 * @package       newsletter2go
 * @author        Newsletter2Go
 * @link          https://www.newsletter2go.de
 * @copyright (C) OXID e-Sales, 2003-2018
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id' => 'newsletter2go',
    'title' => 'Newsletter2Go',
    'description' => [
        'de' => 'Erstellen und versenden Sie mühelos professionelle Newsletter und anspruchsvolle automatisierte Kampagnen, mit denen Sie bei Ihren Empfängern gut ankommen und mehr verkaufen.',
        'en' => 'Create professional emails without the hassle. Launch automated email campaigns that boost customer engagement and drive sales.',
    ],
    'thumbnail' => 'picture.png',
    'version' => '4.0.1',
    'lang' => 'en',
    'author' => 'Newsletter2Go',
    'url' => 'https://www.newsletter2go.de',
    'email' => 'support@newsletter2go.de',
    'extend' => [
        \OxidEsales\Eshop\Application\Controller\Admin\ModuleConfiguration::class => \Newsletter2Go\Newsletter2Go\Core\Nl2GoSettings::class,
        \OxidEsales\Eshop\Application\Controller\ThankYouController::class => \Newsletter2Go\Newsletter2Go\Controller\ThankYouController::class,
    ],
    'controllers' => [
        'nl2go_base' => \Newsletter2Go\Newsletter2Go\Controller\BaseController::class,
        'nl2go_customer' => \Newsletter2Go\Newsletter2Go\Controller\CustomerController::class,
        'nl2go_product' => \Newsletter2Go\Newsletter2Go\Controller\ProductController::class,
        'nl2go_callback' => \Newsletter2Go\Newsletter2Go\Controller\CallbackController::class,
    ],
    'events' => [],
    'templates' => [],
    'blocks' => [
        ['template' => 'module_config.tpl', 'block'=>'admin_module_config_form', 'file'=>'views/admin/block/module_config.tpl'],
        ['template' => 'page/checkout/thankyou.tpl', 'block' => 'checkout_thankyou_info', 'file' => 'views/blocks/page/checkout/checkout_thankyou_info.tpl'],
    ],
    'settings' => [
        [
            'group' => 'nl2go_credentials',
            'name'  => 'nl2goUserName',
            'type'  => 'str',
            'value' => 'newsletter2goApiUser',
        ],
        [
            'group' => 'nl2go_credentials',
            'name'  => 'nl2goApiKey',
            'type'  => 'str',
            'value' => substr(str_shuffle(str_repeat($nl2goCharset='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(40/strlen($nl2goCharset)) )),1,40),
        ],
        [
            'group' => 'nl2go_credentials',
            'name'  => 'nl2goTracking',
            'type'  => 'bool',
            'value' => false,
        ],
    ],
];