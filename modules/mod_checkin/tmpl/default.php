<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$document = jFactory::getDocument();
$document->addStyleSheet(Juri::base(true).'/modules/mod_checkin/assets/css/checkin.css?'.uniqid());
$document->addStyleSheet(Juri::base(true).'/modules/mod_checkin/assets/css/styles.css?'.uniqid());

$document->addScript('https://rawgit.com/schmich/instascan-builds/master/instascan.min.js');
$document->addScript(Juri::base(true).'/modules/mod_checkin/assets/js/scripts.js?'.uniqid());
$document->addScript(Juri::base(true).'/modules/mod_checkin/assets/js/checkin.js?'.uniqid());
?>

<div id="pine-checkin">
    <div class="check"></div>
    <div class="start-checkin">
<h2>Escaneie o QR Code</h2>
    <video id="preview"></video>
    <button id="startScan">Iniciar Leitura</button>
    <p id="loading">⏳ Processando...</p>
</div>
</div>