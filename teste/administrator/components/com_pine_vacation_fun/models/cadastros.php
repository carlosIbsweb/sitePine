<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Pine_vacation_fun
 * @author     Equipe IBS <carlos@ibsweb.com.br>
 * @copyright  2020 Equipe IBS
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Pine_vacation_fun records.
 *
 * @since  1.6
 */
class Pine_vacation_funModelCadastros extends \Joomla\CMS\MVC\Model\ListModel
{
    
        
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'created_by', 'a.`created_by`',
				'modified_by', 'a.`modified_by`',
				'nome_resp', 'a.`nome_resp`',
				'telefone', 'a.`telefone`',
				'email', 'a.`email`',
				'visita', 'a.`visita`',
				'numerocriancas', 'a.`numerocriancas`',
				'cardapio', 'a.`cardapio`',
				'nome_crianca1', 'a.`nome_crianca1`',
				'idade_crianca1', 'a.`idade_crianca1`',
				'nome_crianca2', 'a.`nome_crianca2`',
				'idade_crianca2', 'a.`idade_crianca2`',
				'nome_crianca3', 'a.`nome_crianca3`',
				'idade_crianca3', 'a.`idade_crianca3`',
				'nome_crianca4', 'a.`nome_crianca4`',
				'idade_crianca4', 'a.`idade_crianca4`',
				'nome_crianca_add1', 'a.`nome_crianca_add1`',
				'idade_crianca_add1', 'a.`idade_crianca_add1`',
				'nome_crianca_add2', 'a.`nome_crianca_add2`',
				'idade_crianca_add2', 'a.`idade_crianca_add2`',
				'date', 'a.`date`',
				'cpf', 'a.`cpf`',
			);
		}

		parent::__construct($config);
	}

    
        
    
        

        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
        // List state information.
        parent::populateState('numerocriancas', 'ASC');

        $context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $context);

        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

                
                    return parent::getStoreId($id);
                
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__pine_vacation_fun` AS a');
                
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
                

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.nome_resp LIKE ' . $search . '  OR  a.telefone LIKE ' . $search . '  OR  a.email LIKE ' . $search . '  OR  a.visita LIKE ' . $search . '  OR  a.numerocriancas LIKE ' . $search . '  OR  a.cardapio LIKE ' . $search . '  OR  a.nome_crianca1 LIKE ' . $search . '  OR  a.idade_crianca1 LIKE ' . $search . '  OR  a.nome_crianca2 LIKE ' . $search . '  OR  a.idade_crianca2 LIKE ' . $search . '  OR  a.nome_crianca3 LIKE ' . $search . '  OR  a.idade_crianca3 LIKE ' . $search . '  OR  a.nome_crianca4 LIKE ' . $search . '  OR  a.idade_crianca4 LIKE ' . $search . '  OR  a.nome_crianca_add1 LIKE ' . $search . '  OR  a.idade_crianca_add1 LIKE ' . $search . '  OR  a.nome_crianca_add2 LIKE ' . $search . '  OR  a.idade_crianca_add2 LIKE ' . $search . '  OR  a.date LIKE ' . $search . '  OR  a.cpf LIKE ' . $search . ' )');
			}
		}
                
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'numerocriancas');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
                

		return $items;
	}
}
