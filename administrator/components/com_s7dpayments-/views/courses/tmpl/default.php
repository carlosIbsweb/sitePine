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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JPATH_ROOT . 'media/com_s7dpayments/css/list.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_s7dpayments');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_s7dpayments&task=courses.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'courseList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    };

    jQuery(document).ready(function () {
        jQuery('#clear-search-button').on('click', function () {
            jQuery('#filter_search').val('');
            jQuery('#adminForm').submit();
        });
    });
</script>

<?php

// Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
    $this->sidebar .= $this->extra_sidebar;
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_s7dpayments&view=courses'); ?>" method="post"
      name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
            <?php endif; ?>

             <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
            <div class="clearfix"></div>
            <table class="table table-striped" id="courseList">
                <thead>
                <tr>
                    <?php if (isset($this->items[0]->ordering)): ?>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                        </th>
                    <?php endif; ?>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value=""
                               title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <?php if (isset($this->items[0]->state)): ?>
                        <th width="1%" class="nowrap center">
    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
</th>
                    <?php endif; ?>

                                    <th class='left'>
                <?php echo JHtml::_('grid.sort',  'COM_S7DPAYMENTS_COURSES_TITLE', 'a.`title`', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                <?php echo JHtml::_('grid.sort',  'Categoria', 'a.`catid`', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                <?php echo JHtml::_('grid.sort',  'COM_S7DPAYMENTS_COURSES_DATE', 'a.`date`', $listDirn, $listOrder); ?>
                </th>


                    <?php if (isset($this->items[0]->id)): ?>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.`id`', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
                <?php foreach ($this->items as $i => $item) :
                    $ordering   = ($listOrder == 'a.ordering');
                    $canCreate  = $user->authorise('core.create', 'com_s7dpayments');
                    $canEdit    = $user->authorise('core.edit', 'com_s7dpayments');
                    $canCheckin = $user->authorise('core.manage', 'com_s7dpayments');
                    $canChange  = $user->authorise('core.edit.state', 'com_s7dpayments');
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">

                        <?php if (isset($this->items[0]->ordering)) : ?>
                            <td class="order nowrap center hidden-phone">
                                <?php if ($canChange) :
                                    $disableClassName = '';
                                    $disabledLabel    = '';

                                    if (!$saveOrder) :
                                        $disabledLabel    = JText::_('JORDERINGDISABLED');
                                        $disableClassName = 'inactive tip-top';
                                    endif; ?>
                                    <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
                                          title="<?php echo $disabledLabel ?>">
                            <i class="icon-menu"></i>
                        </span>
                                    <input type="text" style="display:none" name="order[]" size="5"
                                           value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
                                <?php else : ?>
                                    <span class="sortable-handler inactive">
                            <i class="icon-menu"></i>
                        </span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td class="hidden-phone">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <?php if (isset($this->items[0]->state)): ?>
                            <td class="center">
    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'courses.', $canChange, 'cb'); ?>
</td>
                        <?php endif; ?>

                                        <td>
                <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'courses.', $canCheckin); ?>
                <?php endif; ?>
                <?php if ($canEdit) : ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_s7dpayments&task=course.edit&id='.(int) $item->id); ?>">
                    <?php echo $this->escape($item->title); ?></a>
                <?php else : ?>
                    <?php echo $this->escape($item->title); ?>
                <?php endif; ?>
                </td>
                <td>

                    <?php echo $this->getCat('title','id',$item->catid); ?>
                </td>
                <td>

                    <?php echo $item->date; ?>
                </td>


                        <?php if (isset($this->items[0]->id)): ?>
                            <td class="center hidden-phone">
                                <?php echo (int) $item->id; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </div>
</form>        
