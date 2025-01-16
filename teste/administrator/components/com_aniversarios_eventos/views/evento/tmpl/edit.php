<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_eventos
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2018 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_aniversarios_eventos/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'evento.cancel') {
			Joomla.submitform(task, document.getElementById('evento-form'));
		}
		else {
			
			if (task != 'evento.cancel' && document.formvalidator.isValid(document.id('evento-form'))) {
				
				Joomla.submitform(task, document.getElementById('evento-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_aniversarios_eventos&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="evento-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ANIVERSARIOS_EVENTOS_TITLE_EVENTO', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>				<?php echo $this->form->renderField('nomeresp'); ?>
				<?php echo $this->form->renderField('telefone'); ?>
				<?php echo $this->form->renderField('email'); ?>
				<?php echo $this->form->renderField('nomecria'); ?>
				<?php echo $this->form->renderField('idade'); ?>
				<?php echo $this->form->renderField('escola'); ?>
				<?php echo $this->form->renderField('diafesta'); ?>
				<?php echo $this->form->renderField('numerocria'); ?>
				<?php echo $this->form->renderField('numeroadult'); ?>
				<?php echo $this->form->renderField('opcoesfestas'); ?>
				<?php echo $this->form->renderField('tema'); ?>
				<?php echo $this->form->renderField('checkbox'); ?>


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

	</div>
</form>
