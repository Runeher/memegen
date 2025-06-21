<?php
function memegen_promo_show()
{
    $block = [];

    $imageUrl = XOOPS_URL . '/modules/memegen/assets/images/memegen_promo3.jpg';

    $link = XOOPS_URL . '/modules/memegen/';

    $block['image'] = $imageUrl;
    $block['link'] = $link;

    return $block;
}
