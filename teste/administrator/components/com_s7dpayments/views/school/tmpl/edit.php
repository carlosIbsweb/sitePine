<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_S7dpayments
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2021 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_s7dpayments/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'school.cancel') {
			Joomla.submitform(task, document.getElementById('school-form'));
		}
		else {
			
			if (task != 'school.cancel' && document.formvalidator.isValid(document.id('school-form'))) {
				
				Joomla.submitform(task, document.getElementById('school-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_s7dpayments&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="school-form" class="form-validate form-horizontal">

	
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'school')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'school', JText::_('COM_S7DPAYMENTS_TAB_SCHOOL', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_S7DPAYMENTS_FIELDSET_SCHOOL'); ?></legend>
				<?php echo $this->form->renderField('nome'); ?>
				<?php echo $this->form->renderField('criado'); ?>
			</fieldset>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

	

	<?php $this->ignore_fieldsets = array('general', 'info', 'detail', 'jmetadata', 'item_associations', 'accesscontrol'); ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>
	
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
