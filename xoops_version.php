<?php

$moduleDirName      = basename(__DIR__);

$modversion['version'] = '1.1.0';
$modversion['name'] = _MI_MEMEGEN_NAME;
$modversion['description'] = _MI_MEMEGEN_DESC; //'Create memes with user-uploaded images and self-destruct after 20 minutes.';
$modversion['author'] = 'Runeher';
$modversion['credits'] = 'XOOPS Community';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'assets/images/memegen_icon.png';
$modversion['dirname'] = $moduleDirName;

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = '';

// Main menu
$modversion['hasMain'] = 1;

// Templates
$modversion['templates'] = [
    [
        'file' => 'memegen_index.tpl',
        'description' => 'Main meme creation page'
    ],
    
];


// ------------------- Blocks ------------------- //
$modversion['blocks'][] = [
    'file'        => 'memegen_promo.php',
    'name'        => _MI_MEMEGEN_BLOCK_PROMO,
    'description' => '_MI_MEMEGEN_BLOCK_PROMO_DESC',
    'show_func'   => 'memegen_promo_show',
    'template'    => 'memegen_promo.tpl',
];



$modversion['config'][] = [
    'name'        => 'watermark_text',
    'title'       => '_MI_MEMEGEN_WATERMARK_TEXT',
    'description' => 'Text to use as watermark',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => ''
];

$modversion['config'][] = [
    'name' => 'enable_watermark', // må være nøyaktig dette
    'title' => '_MI_MEMEGEN_ENABLE_WATERMARK',
    'description' => '_MI_MEMEGEN_ENABLE_WATERMARK_DESC',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 1,
];


// for future Admin Preferences
$modversion['config'][] = [
    'name'        => 'url_support_enabled',
    'title'       => '_MI_MEMEGEN_URL_SUPPORT',
    'description' => '_MI_MEMEGEN_URL_SUPPORT_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1
];

$modversion['config'][] = [
    'name'        => 'allowed_image_hosts',
    'title'       => '_MI_MEMEGEN_ALLOWED_HOSTS',
    'description' => '_MI_MEMEGEN_ALLOWED_HOSTS_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'array',
    'default'     => "imgur.com\ni.imgur.com\npexels.com"
];
