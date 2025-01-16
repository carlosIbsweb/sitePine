<?php
/**
 * @version    CVS: 2.0.0
 * @package    Com_S7dgallery
 * @author     carlos <carlosnaluta@gmail.com>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_s7dgallery') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'albumform.xml');
$canEdit    = $user->authorise('core.edit', 'com_s7dgallery') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'albumform.xml');
$canCheckin = $user->authorise('core.manage', 'com_s7dgallery');
$canChange  = $user->authorise('core.edit.state', 'com_s7dgallery');
$canDelete  = $user->authorise('core.delete', 'com_s7dgallery');


// Get the document object.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base(true).'/media/com_s7dgallery/css/s7dcolumns.css');
$doc->addStyleSheet(JUri::base(true).'/media/com_s7dgallery/css/line-awesome.css');
$doc->addStyleSheet(JUri::base(true).'/components/com_s7dgallery/assets/css/style.css?1');

$folder     = JUri::base(true).'/images/s7dgallery/';

$catColunm = 12 / $this->params->get('sg-columns');
$catColunm = $catColunm == '2.4' ? '15' : $catColunm;

$imgWidth = preg_replace("/[^0-9]/", "", $this->params->get('sg-wimage')).'%';
$titleTag = $this->params->get('title_tag');
//Style
$sgStyle = $this->params->get('sg-style') == 'list' ? 'sg-listItems' : '';
$sgStyleImgWidth = $sgStyle == 'sg-listItems' ? 'style="width:'.$imgWidth.'"' : null;
$sgStyleInfo = $sgStyle == 'sg-listItems' ? 'style="float: right; width: calc(100% - '.$imgWidth.');"' : null;

//Pagination Align
$pagAlign = $this->params->get('sg-pagAlign') ? 'text-align: '.$this->params->get('sg-pagAlign') : 'text-align:left';
$pagView  = $this->params->get('sg-pagAlign') == 'right' ? 'float:left !important' : null;

?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm">
<div id="s7dGalleryList">
  
    <?php foreach (array_chunk($this->items,$this->params->get('sg-columns')) as $key => $arrows): ?>
      <div class="s7d-row sg-row">
      <?php $im = 0; foreach ($arrows as $k => $item): ?>
    	<div class="s7d-col-sm-<?= $catColunm;?> sg-items">
      <?php foreach(json_decode($item->images) as $img):?>
        <?php if($img->cover == 1): $im = 1; ?>
      		<a href="<?php echo JRoute::_('index.php?option=com_s7dgallery&view=album&id='.(int) $item->id); ?>">
            <span class="sg-items-img-inner <?= $sgStyle;?>" <?= $sgStyleImgWidth;?> >
						    <img alt="<?= $img->alt;?>" src="<?= JUri::root();?>components/com_s7dgallery/image/image.php?itemId=<?= $item->id;?>&imgId=<?= $img->id;?>&path=thumbs"/>
            </span>
            </a>
          <?php endif ?>
          <?php endforeach ?>

            <div class="sg-listInfo <?= $im ? $sgStyle : null;?>" <?= $im ? $sgStyleInfo : null;?>>
            <a href="<?php echo JRoute::_('index.php?option=com_s7dgallery&view=album&id='.(int) $item->id); ?>">
              <<?= $titleTag;?> class="sg-title-header"><?= $this->escape($item->title);?></<?= $titleTag;?>>
            </a>
            <?php if($this->params->get('sg-limittext')): ?>
              <div class="sg-desc">
                <p><?= $this->lText($item->description,$this->params->get('sg-limittext')); ?></p>
              </div>
            <?php endif ?>
              <div class="data" style="display: table;margin: 0px auto;">
                <?php if($this->params->get('exDate')): ?>
                  <?php echo '<p class="date"><i class="fa fa-calendar" aria-hidden="true"></i> Publicado em '. $this->diffDates($item->data).'</p>'; ?>
                <?php endif ?>
              </div>
            </div>
         
      </div>
          <?php endforeach ?>
          </div>
    <?php endforeach ?>

    <?php if($this->params->get('exPagination')): ?>
    <div class="sg-galListFooter" style="<?= $pagAlign;?>">
      <?php echo $this->pagination->getPagesLinks(); ?>
      <?php if($this->params->get('exPageCount') && $this->params->get('exPagination')): ?>
        <span class="sg-pageCount" style="<?= $pagView;?>">
          <?php echo $this->pagination->getPagesCounter(); ?>
        </span>

      <?php endif ?>
      
    </div>
  <?php endif ?>
    
</div>

</form>