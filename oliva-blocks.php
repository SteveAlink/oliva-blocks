<?php

if (!defined('VERSION')) {
    die('Direct access is not allowed.');
}

require_once __DIR__ . '/class.oliva-blocks.php';

$olivaBlocks = new OlivaBlocks($Wcms);

$Wcms->addListener('settings', [$olivaBlocks, 'handleSettings']);
$Wcms->addListener('footer', [$olivaBlocks, 'renderBlocks']);
$Wcms->addListener('css', [$olivaBlocks, 'renderCss']);
$Wcms->addListener('js', 'olivaBlocksJs');

function olivaBlocksJs()
{
    echo '<script src="' . BASE_URL . '/plugins/oliva-blocks/js/oliva-blocks.js"></script>';
}
