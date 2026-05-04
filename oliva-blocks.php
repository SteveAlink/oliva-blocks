<?php
/**
 * oliva-blocks - Blocks plugin for WonderCMS.
 * Prepared by Steve Alink for Oliva Solutions
 *
 * A block of text, image or a combination of this that can be used multiple times.
 */

if (!defined('VERSION')) {
    die('Direct access is not allowed.');
}

require_once __DIR__ . '/class.oliva-blocks.php';

$olivaBlocks = new OlivaBlocks($Wcms);

$Wcms->addListener('settings', [$olivaBlocks, 'handleSettings']);
$Wcms->addListener('page', [$olivaBlocks, 'renderBlocks']);
$Wcms->addListener('css', [$olivaBlocks, 'renderCss']);
$Wcms->addListener('js', [$olivaBlocks, 'renderJs']);
