<?php
$modversion['name'] = 'MemeGen';
$modversion['version'] = '1.0';
$modversion['description'] = 'Create memes with user-uploaded images and self-destruct after 20 minutes.';
$modversion['author'] = 'Runeher';
$modversion['credits'] = 'XOOPS Community';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'images/memegen_icon.png';
$modversion['dirname'] = 'memegen';

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