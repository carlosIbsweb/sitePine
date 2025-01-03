<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_e_eventos
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
$document->addStyleSheet(Uri::root() . 'media/com_aniversarios_e_eventos/css/form.css');
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
	action="<?php echo JRoute::_('index.php?option=com_aniversarios_e_eventos&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="cadastro-form" class="form-validate form-horizontal">

	
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'sub')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'sub', JText::_('COM_ANIVERSARIOS_E_EVENTOS_TAB_SUB', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ANIVERSARIOS_E_EVENTOS_FIELDSET_SUB'); ?></legend>	
					<div class="dResponsavel">
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('nome'))); ?></div>
							<div class="controls"><?php echo $this->item->nome; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('telefone'))); ?></div>
							<div class="controls"><?php echo $this->item->telefone; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('email'))); ?></div>
							<div class="controls"><?php echo $this->item->email; ?></div>
						</div>
					</div>
					<div class="dCrianca">
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('nome_cria'))); ?></div>
							<div class="controls"><?php echo $this->item->nome_cria; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('idade'))); ?></div>
							<div class="controls"><?php echo $this->item->idade; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('escola'))); ?></div>
							<div class="controls"><?php echo $this->item->escola; ?></div>
						</div>
					</div>
					<div class="dEvento">
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('festa'))); ?></div>
							<div class="controls"><?php echo $this->item->festa; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('numerocriancas'))); ?></div>
							<div class="controls"><?php echo $this->item->numerocriancas; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('adultos'))); ?></div>
							<div class="controls"><?php echo $this->item->adultos; ?></div>
						</div>
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('tema'))); ?></div>
							<div class="controls"><?php echo $this->item->tema; ?></div>
						</div>
					</div>
					<div class="dOpcoes">
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('opcoes'))); ?></div>
							<div class="controls"><?php echo $this->item->opcoes; ?></div>
						</div>
					</div>
					<div class="dOpcional">
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('opcional'))); ?></div>
							<div class="controls"><?php echo $this->item->opcional; ?></div>
						</div>
					</div>
					<div class="dDataC">
						<div class="control-group anive_eventos">
							<div class="control-label anive_eventos"><?php echo implode(':</label>',explode('</label>',$this->form->getLabel('date'))); ?></div>
							<div class="controls"><?php echo $this->item->date; ?></div>
						</div>
					</div>
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
