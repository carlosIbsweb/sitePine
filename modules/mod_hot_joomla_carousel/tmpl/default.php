<?php
/*------------------------------------------------------------------------
# "Hot Joomla Carousel" Joomla module
# Copyright (C) 2018 HotThemes. All Rights Reserved.
# License: http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 only
# Author: HotThemes
# Website: https://www.hotjoomlatemplates.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); // no direct access

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

// get the document object
$doc = Factory::getDocument();

// load jQuery
HTMLHelper::_('jquery.framework');

// load scripts
$doc->addScript($mosConfig_live_site.'/modules/mod_hot_joomla_carousel/js/flickity.pkgd.min.js');

// add your stylesheet
$doc->addStyleSheet( 'modules/mod_hot_joomla_carousel/tmpl/style.css' );

// style declaration
$doc->addStyleDeclaration( '

    .hot_joomla_carousel_slides'.$uniqueid.' .gallery-cell img {
        max-width: 99999px;
        width: calc(100% - 2 * '.$slideMargin.');
        margin: '.$slideMargin.';
        padding: '.$slidePaddingVertical.' '.$slidePaddingHorizontal.';
        background: '.$backgroundColor.';
        border:'.$borderWidth.' '.$borderStyle.' '.$borderColor.';
        box-sizing: border-box;
    }

    .hot_joomla_carousel_slides'.$uniqueid.' .flickity-prev-next-button.previous {
        left: '.$navArrowsPosition.';
    }

    .hot_joomla_carousel_slides'.$uniqueid.' .flickity-prev-next-button.next {
        right: '.$navArrowsPosition.';
    }

    .hot_joomla_carousel_slides'.$uniqueid.' .flickity-page-dots {
        bottom: '.$navDotsPosition.';
    }

    @media (max-width:'.$responsiveWidth.') {
        .hot_joomla_carousel_slides'.$uniqueid.' .gallery-cell {
            width: 100% !important;
        }
    }

' );

if ($slideMargin == 0) {

    $doc->addStyleDeclaration( '

        .hot_joomla_carousel_slides'.$uniqueid.' .gallery-cell img {
            width: 100%;
        }

    ' );

}

if ($fullWidth) {

    $doc->addStyleDeclaration( '

    .hot_joomla_carousel_slides'.$uniqueid.' .gallery-cell {
        width: 100%;
    }

    ' );

} else {

    $doc->addStyleDeclaration( '

    .hot_joomla_carousel_slides'.$uniqueid.' .gallery-cell {
        width: '.$slideWidth.';
    }

    ' );

}

?>

<div class="hot_joomla_carousel_slides<?php echo $uniqueid; ?>">

        <?php  
            $carouselPath = $_SERVER['SCRIPT_FILENAME'];
            $carouselRealPath = substr_replace($carouselPath ,"",-9);
            $carouselFullPath = $carouselRealPath.$carouselImagesPath;

            if ($handle = opendir($carouselFullPath)) {
                $infinite_pics_number = 0;
                $infinite_list = "";
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && strstr($file, ".")) {
                        if($infinite_list !== "")
                        { $infinite_list = $infinite_list."||"."$file";}
                        else
                        { $infinite_list = "$file"; }
                        $infinite_pics_number = $infinite_pics_number + 1;
                    }
                }
                closedir($handle);
                $infinite_pic = explode("||", $infinite_list);
                sort($infinite_pic);
                for ($loop = 0; $loop < $infinite_pics_number; $loop += 1) {
                    $pic_type = explode(".", $infinite_pic[$loop]);
                    if($pic_type[1]) {
                        if ($pic_type[1]=="jpg" || $pic_type[1]=="gif" || $pic_type[1]=="jpeg" || $pic_type[1]=="png") {
                            echo '<div class="gallery-cell"><img src="'.$carouselImagesPath.'/'.$infinite_pic[$loop].'" alt="" /></div>';
                        } elseif (  $pic_type[1]=="JPG" || $pic_type[1]=="GIF" || $pic_type[1]=="JPEG" || $pic_type[1]=="PNG") {
                            echo '<div class="gallery-cell"><img src="'.$carouselImagesPath.'/'.$infinite_pic[$loop].'" alt="" /></div>';                   
                        }
                    }
                }
            }
            ?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('.hot_joomla_carousel_slides<?php echo $uniqueid; ?>').flickity({
            // options
            cellAlign: 'left',
            contain: true,
            freeScroll: false,
            wrapAround: true,
            prevNextButtons: <?php if($navArrows) { echo "true"; }else{ echo "false"; } ?>,
            pageDots: <?php if($navDots) { echo "true"; }else{ echo "false"; } ?>,
            autoPlay: <?php echo $pauseTime; ?>,
            imagesLoaded: true<?php if ($fullWidth == 0) { ?>,
            "percentPosition": false<?php } ?>
        });
    });
</script>