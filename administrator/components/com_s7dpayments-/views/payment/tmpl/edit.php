<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
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
$document->addStyleSheet(JPATH_ROOT . 'media/com_s7dpayments/css/edit.css');
$document->addStyleSheet(JUri::base(true).'/components/com_s7dpayments/assets/css/style.css?'.uniqid());

$user = JFactory::getUser($this->item->userid);
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {
        
    });

    Joomla.submitbutton = function (task) {
        if (task == 'payment.cancel') {
            Joomla.submitform(task, document.getElementById('payment-form'));
        }
        else {
            
            if (task != 'payment.cancel' && document.formvalidator.isValid(document.id('payment-form'))) {
                
                Joomla.submitform(task, document.getElementById('payment-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_s7dpayments&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="payment-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_S7DPAYMENTS_TITLE_PAYMENT', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">
                    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
                    <div class="span6">
                        <div class="control-group">
                            <div class="control-label"><label>Referência</label></div>
                            <div class="controls"><h5><?php echo $this->item->ref; ?></h5></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><label>Responsável</label></div>
                            <div class="controls"><h3><?php echo $this->item->name; ?></h3></div>
                        </div>
                         <div class="control-group">
                            <div class="control-label">Status</div>
                            <div class="controls"><?php echo $this->form->getInput('status'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">Forma de pagamento</div>
                            <div class="controls"><h5><?php echo $this->item->form; ?></h5></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">CPF</div>
                            <div class="controls"><h5><?php echo $user->cpf; ?></h5></div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="control-group">
                            <div class="control-label">Data da compra</div>
                            <div class="controls"><h5><?php echo $this->form->getInput('date'); ?></h5></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">Telefone</div>
                            <div class="controls"><h5><?php echo $user->telefone; ?></h5></div>
                        </div>
                         <div class="control-group">
                            <div class="control-label">E-mail</div>
                            <div class="controls"><h5><?php echo $user->email; ?></h5></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">Endereço</div>
                            <div class="controls"><h5><?php echo $user->endereco; ?></h5></div>
                        </div>
                    </div>
                    <div class="span10">
             <div class="control-group">
                <div class="control-label">Resumo</div>
                <?php echo $this->form->getInput('resumo'); ?>
            </div>
            </div>
           
                <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
                <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
                <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
                <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

                <?php if(empty($this->item->created_by)){ ?>
                    <input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

                <?php } 
                else{ ?>
                    <input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

                <?php } ?>          


                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php if (JFactory::getUser()->authorise('core.admin','s7dpayments')) : ?>
    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
        <?php echo $this->form->getInput('rules'); ?>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
<?php endif; ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
