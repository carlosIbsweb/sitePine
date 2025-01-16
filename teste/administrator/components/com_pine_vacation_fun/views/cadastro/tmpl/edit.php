<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Pine_vacation_fun
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
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
$document->addStyleSheet(Uri::root() . 'media/com_pine_vacation_fun/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'cadastro.cancel') {
			Joomla.submitform(task, document.getElementById('cadastro-form'));
		}
		else {
			
			if (task != 'cadastro.cancel' && document.formvalidator.isValid(document.id('cadastro-form'))) {
				
				Joomla.submitform(task, document.getElementById('cadastro-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_pine_vacation_fun&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="cadastro-form" class="form-validate form-horizontal">

	
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'sub')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'sub', JText::_('COM_PINE_VACATION_FUN_TAB_SUB', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PINE_VACATION_FUN_FIELDSET_SUB'); ?></legend>
				<?php echo $this->form->renderField('nome_resp'); ?>
				<?php echo $this->form->renderField('cpf'); ?>
				<?php echo $this->form->renderField('telefone'); ?>
				<?php echo $this->form->renderField('email'); ?>
				<?php echo $this->form->renderField('visita'); ?>
				<?php echo $this->form->renderField('numerocriancas'); ?>
				<?php echo $this->form->renderField('cardapio'); ?>
				<?php echo $this->form->renderField('nome_crianca1'); ?>
				<?php echo $this->form->renderField('idade_crianca1'); ?>
				<?php echo $this->form->renderField('nome_crianca2'); ?>
				<?php echo $this->form->renderField('idade_crianca2'); ?>
				<?php echo $this->form->renderField('nome_crianca3'); ?>
				<?php echo $this->form->renderField('idade_crianca3'); ?>
				<?php echo $this->form->renderField('nome_crianca4'); ?>
				<?php echo $this->form->renderField('idade_crianca4'); ?>
				<?php echo $this->form->renderField('nome_crianca_add1'); ?>
				<?php echo $this->form->renderField('idade_crianca_add1'); ?>
				<?php echo $this->form->renderField('nome_crianca_add2'); ?>
				<?php echo $this->form->renderField('idade_crianca_add2'); ?>
				<?php echo $this->form->renderField('date'); ?>				
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

	
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
