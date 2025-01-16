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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('jquery.ui', array('core', 'sortable'));
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_s7dgallery/css/item.css');
$document->addStyleSheet(JUri::root() . 'media/com_s7dgallery/css/images.css?'.uniqid());
$document->addStyleSheet(JUri::root() . 'media/com_s7dgallery/css/line-awesome.css');
$document->addStyleSheet(JUri::root() . 'media/com_s7dgallery/css/wkanimate.css');
$document->addScript(Juri::root() . 'administrator/components/com_s7dgallery/assets/js/jquery.ui.selectable.js');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'album.cancel') {
			Joomla.submitform(task, document.getElementById('album-form'));
		}
		else {
			
			if (task != 'album.cancel' && document.formvalidator.isValid(document.id('album-form'))) {
				
				Joomla.submitform(task, document.getElementById('album-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_s7dgallery&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="album-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_S7DGALLERY_TITLE_ALBUM', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<?php echo $this->form->renderField('title'); ?>
				<?php echo $this->form->renderField('catid'); ?>
				<?php echo $this->form->renderField('alias'); ?>
				<?php echo $this->form->renderField('exdescription'); ?>
				<?php echo $this->form->renderField('exslider'); ?>
				<div class="sg-editor s7d-col-md-12"><?php echo $this->form->getInput('description'); ?></div>
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>				<?php echo $this->form->renderField('update'); ?>
				<?php echo $this->form->renderField('data'); ?>


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'sgimages', JText::_('COM_S7DGALLERY_TITLE_IMAGES', true)); ?>
			<?php echo $this->form->renderField('images'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
