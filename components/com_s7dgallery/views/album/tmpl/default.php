<?php
/**
 * @version    2.0
 * @package    Com_S7dlv
 * @author     carlos <carlos@ibsweb.com.br>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;

// Load the modal behavior script.
JHtml::_('behavior.framework', true);

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Get the current user object.
$user = JFactory::getUser();
$userId = $user->id;

$this->acessos($this->item->id);

// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/media/com_s7dgallery/css/s7dcolumns.css');
$doc->addStyleSheet(JUri::base(true).'/media/com_s7dgallery/css/line-awesome.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/style.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/owl.carousel.min.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/owl.theme.default.min.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/magnific-popup.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/justifiedGallery.css');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/jquery.magnific-popup.js');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/s7dGallery.js');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/justifiedGallery.js');
$doc->addScript(JUri::base(true).'/components/com_s7dgallery/assets/js/owl.carousel.min.js');
$productId  = $this->item->id;

?>


<?php if($this->params->get('exgal')): ?>
<script>
jQuery(function($){
  $(document).ready(function(){
    $("#s7dGallery").justifiedGallery({
      margins: <?= $this->params->get('s7dgallery-margins');?>,
      captions: false,
      rowHeight: <?= $this->params->get('s7dgallery-height');?>,
      maxRowHeight: <?= $this->params->get('s7dgallery-maxHeight');?>
    });
    
        $('#s7dGallery').sgPopup({
          delegate: 'a',
          type: 'image',
          closeOnContentClick: false,
          closeBtnInside: false,
          mainClass: 'mfp-img-mobile',
          image: {
            verticalFit: true,
            titleSrc: function(item) {
              return item.el.attr('title') + ' &middot; <a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank"><i class="la la-external-link"></i></a>';
            }
          },
          gallery: {
            enabled: true
          },
          zoom: {
            enabled: true,
            duration: 300,
            easing: 'ease-in-out', // don't foget to change the duration also in CSS
            opener: function(element) {
              return element.find('img');
            }
          }
          
        });
      });

      $(document).on("click",".sg-loadMore",function( event ){
      
      event.preventDefault();
      $(this).paginationImages({
        id : $(this).data('id'),
        count : $(this).data('count'),
        start : $(this).data('start'),
        imgLoader: "<?= Juri::base(true).'/components/com_s7dgallery/assets/images/load.gif';?>"
      });
      event.stopPropagation();
      
    });

       $(document).ready(function() {
              var owl = $('.owl-carousel');
              owl.owlCarousel({
                margin: 10,
                nav: true,
                loop: true,
                responsive: {
                  0: {
                    items: 1
                  },
                  600: {
                    items: 1
                  },
                  1000: {
                    items: 1
                  }
                }
              })
            })
   
});


  
</script>
<?php endif ?>

<?php if($this->item->id): ?>

<?php

$limit      = $this->params->get('s7dgallery-start');
$perPage    = $this->params->get('s7dgallery-perPage');
$excap    = $this->params->get('excap');
$start      = json_decode($this->item->images);
$images     = array_slice(json_decode($this->item->images),0,$limit);
$folder     = JUri::base(true).'/images/s7dgallery/gal-'.$productId.'/';
$fSmall     = $folder.'small/';
$fMedium    = $folder.'medium/';
$fLarge     = $folder.'large/';

$doc = JFactory::getDocument();
$doc->addCustomTag("<meta property='og:title' content='".$this->item->title."'/>");
$doc->addCustomTag("<meta property='og:description' content='".$this->item->description."'/>");

foreach($images as $img):?>
  <?php 
    $access = $img->access != 1 ? true : (!empty($userId) ? true : false);
    $exCap = !$excap ? $img->cover != 1 : $img->cover != 1 || $img->cover == 1;
  
    if($img->cover){ 
      $imgOg = JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$_REQUEST["id"].'&imgId='.$img->id.'&path=medium';
      $doc->addCustomTag("<meta property='og:image' content='".$imgOg."'/>");
    }
  endforeach;
?>

<div id="sg-top" class="s7d-row">
  <div class="sg-header s7d-col-md-12">
  <?= $this->getModule('top1'); ?>
  <h1><?= $this->item->title;?></h1>
  <?= $this->getModule('top2'); ?>
</div>
<div class="sg-description s7d-col-md-12">
  <?= $this->item->description;?>
</div>
<div class="data" style="display: table;margin: 30px auto;">
  <?php if($this->params->get('eDate')): ?>
    <?php
        $date = $this->item->data;
        $dateFinal = explode(" ", $date);
        $dateFinal = explode("-", $dateFinal[0]);
        $dateFinal = $dateFinal[2].'/'.$dateFinal[1].'/'.$dateFinal[0];
        echo '<p class="date"><i class="fa fa-calendar" aria-hidden="true"></i> Publicado em ' . $dateFinal.'</p>';
    ?>
  <?php endif ?>
</div>
</div>
<?= $this->getModule('bottom1'); ?>

<?php if ($this->item->exslider){ ?>
  
              <div class="owl-carousel owl-theme s7dSlider">
<?php

 foreach($images as $img):?>
  <?php 
    $access = $img->access != 1 ? true : (!empty($userId) ? true : false);
    $exCap = !$excap ? $img->cover != 1 : $img->cover != 1 || $img->cover == 1;
  
    if($img->cover){ 
      $imgOg = JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$_REQUEST["id"].'&imgId='.$img->id.'&path=medium';
      $doc->addCustomTag("<meta property='og:image' content='".$imgOg."'/>");
    }

  ?>

  <?php if ($access && $exCap): ?>

    <div class="item">
        <div class="sgCitem" style="background: url(<?= JUri::root();?>components/com_s7dgallery/image/image.php?itemId=<?= $_REQUEST['id'];?>&imgId=<?= $img->id;?>&path=large); height: 500px">

      <h3><?= $img->title;?></h3>
          
        </div>
    </div>
  <?php endif ?>
<?php endforeach; ?>
</div>
<?php } else { ?>
<?php if($this->params->get('exgal')): ?>
<div id="s7dGallery" >
<?php

 foreach($images as $img):?>
  <?php 
    $access = $img->access != 1 ? true : (!empty($userId) ? true : false);
    $exCap = !$excap ? $img->cover != 1 : $img->cover != 1 || $img->cover == 1;
  
    if($img->cover){ 
      $imgOg = JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$_REQUEST["id"].'&imgId='.$img->id.'&path=medium';
      $doc->addCustomTag("<meta property='og:image' content='".$imgOg."'/>");
    }

  ?>

  <?php if ($access && $exCap): ?>

    <a href="<?= JUri::root();?>components/com_s7dgallery/image/image.php?itemId=<?= $_REQUEST['id'];?>&imgId=<?= $img->id;?>&path=large" class="test-popup-link" 
      data-source="<?= JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$_REQUEST['id'].'&imgId='.$img->id.'&path=large';?>" title="<?= $img->title;?>">
        <img alt="<?= $img->alt;?>" src="<?= JUri::root();?>components/com_s7dgallery/image/image.php?itemId=<?= $_REQUEST['id'];?>&imgId=<?= $img->id;?>&path=medium"/>
    </a>
  <?php endif ?>
<?php endforeach; ?>
</div>
<?php endif ?>

<?php } ?>

<?= $this->getModule('bottom2'); ?>

<?php if(count($start) >= $limit ): ?>
<div class="s7d-col" id="sg-loadMore">
  <span class="sg-loadMore btn btn-primary" data-start="<?= $limit;?>" data-count="<?= $perPage;?>" data-id="<?= $this->item->id;?>"><?= JText::_('COM_S7DGALLERY_LOAD_MORE');?></span>
</div>


<?php endif;?>
<?php else: ?>
  <div class="sg-err"><?= JText::_('COM_S7DGALLERY_ERR_ALBUM');?></div>
<?php endif; ?>
