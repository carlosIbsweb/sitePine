<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Aniversarios_e_eventos
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
defined('_JEXEC') or die;

JLoader::register('Aniversarios_e_eventosHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_aniversarios_e_eventos' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'aniversarios_e_eventos.php');

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Class Aniversarios_e_eventosFrontendHelper
 *
 * @since  1.6
 */
class Aniversarios_e_eventosHelpersAniversarios_e_eventos
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_aniversarios_e_eventos/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_aniversarios_e_eventos/models/' . strtolower($name) . '.php';
			$model = BaseDatabaseModel::getInstance($name, 'Aniversarios_e_eventosModel');
		}

		return $model;
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = Factory::getUser();

        if ($user->authorise('core.edit', 'com_aniversarios_e_eventos'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_aniversarios_e_eventos') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
}
