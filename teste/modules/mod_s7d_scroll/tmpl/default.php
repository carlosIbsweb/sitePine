<?php

/**
 * @subpackage	mod_s7d_scroll
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// Acesso
defined('_JEXEC') or die;

$title = JFactory::getDocument()->getTitle();
$id = JRequest::getInt('id');

// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'modules/mod_s7d_scroll/assets/css/owl.carousel.css');
$doc->addStyleSheet(JUri::base(true).'modules/mod_s7d_scroll/assets/css/owl.theme.css');

?>

<div class="<?= $items->tclass;?>">
<div id="owl-demo" class="owl-carousel s7dscroll<?= $module->id; ?>">
     <?php
        switch($params->get('stylemode'))
        {
            case s1:
                require('_articles.php');
                break;
            case s2:
                require('_links.php');
                break;
            default:
                require('_articles.php');
                break;
        }
    ?>
</div>
</div>

<script src="<?=JUri::base(true).'modules/mod_s7d_scroll/assets/js/jquery-1.9.1.min.js';?>"></script> 
<script>var js = $.noConflict(true);</script>
<script src="<?= JUri::base(true).'modules/mod_s7d_scroll/assets/js/owl.carousel.js';?>"></script>

<script>
(function($) {
    $(document).ready(function() {
      $(".s7dscroll<?= $module->id; ?>").owlCarousel({
        autoPlay: 6000,
        items : <?= $params->get('qnt'); ?>,
        itemsDesktop : [1199,3],
        itemsDesktopSmall : [979,3]
      });
    });
})(js);
</script>