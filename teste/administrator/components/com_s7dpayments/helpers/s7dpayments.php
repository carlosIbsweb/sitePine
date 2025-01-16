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

/**
 * S7dpayments helper.
 *
 * @since  1.6
 */
class S7dpaymentsHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  string
     *
     * @return void
     */
    public static function addSubmenu($vName = '')
    {
        $payments = $_GET['list'];
        JHtmlSidebar::addEntry(
            JText::_('COM_S7DPAYMENTS_TITLE_PAYMENTS'),
            'index.php?option=com_s7dpayments&view=payments&list=payments',
            $payments == 'payments' || $payments == ''
        );

         JHtmlSidebar::addEntry(
            JText::_('Filtros'),
            'index.php?option=com_s7dpayments&view=payments&list=filter',
            $payments == 'filter'
        );

          JHtmlSidebar::addEntry(
            JText::_('COM_S7DPAYMENTS_TITLE_COURSES'),
            'index.php?option=com_s7dpayments&view=courses',
            $vName == 'courses'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_S7DPAYMENTS_TITLE_CATEGORIES'),
            'index.php?option=com_categories&extension=com_s7dpayments',
            $vName == 'categories'
        );

         JHtmlSidebar::addEntry(
            JText::_('COM_S7DPAYMENTS_TITLE_SCHOOLS'),
            'index.php?option=com_s7dpayments&view=schools',
            $vName == 'schools'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_S7DPAYMENTS_TITLE_DISCOUNTS'),
            'index.php?option=com_s7dpayments&view=discounts',
            $vName == 'discounts'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return    JObject
     *
     * @since    1.6
     */
    public static function getActions()
    {
        $user   = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_s7dpayments';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action)
        {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
}
