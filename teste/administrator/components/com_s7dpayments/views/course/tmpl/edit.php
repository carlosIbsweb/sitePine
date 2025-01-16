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
$document->addStyleSheet(JUri::base(true).'/components/com_s7dpayments/assets/css/style.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {
        
    });

    Joomla.submitbutton = function (task) {
        if (task == 'course.cancel') {
            Joomla.submitform(task, document.getElementById('course-form'));
        }
        else {
            
            if (task != 'course.cancel' && document.formvalidator.isValid(document.id('course-form'))) {
                
                Joomla.submitform(task, document.getElementById('course-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_s7dpayments&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="course-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_S7DPAYMENTS_TITLE_COURSE', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">

                                    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
            </div>

            

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('ordem'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('ordem'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('dias'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('dias'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
            </div>

             <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('image'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('type'); ?></div>
            </div>

             <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('coursePackage'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('coursePackage'); ?></div>
            </div>

            <?php echo $this->form->getInput('package'); ?>
            <?php echo $this->form->getInput('categorias'); ?>
            
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('discount'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('discount'); ?></div>
            </div>
            
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('price'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('price'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
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

                <?php } ?>          <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('date'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('date'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('updated'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('updated'); ?></div>
            </div>


                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php 
            if(isset($_GET['id'])):
            echo JHtml::_('bootstrap.addTab', 'myTab', 'params', JText::_('Vídeos', true)); ?>
            <div class="row-fluid form-horizontal-desktop">
                <div class="span6 dvideos">
            
                    <div class="control-group dvdescription">
                        <div class="controls"><?php echo $this->form->getInput('svideos'); ?></div>

                        <?php echo $this->form->getInput('videos'); ?>

                    </div>
                </div>
            </div>
            <?php echo JHtml::_('bootstrap.endTab'); endif; ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'arquivos', JText::_('Arquivos', true)); ?>
        <div class="row-fluid">
            <?php echo $this->form->getInput('arquivos'); ?>
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
