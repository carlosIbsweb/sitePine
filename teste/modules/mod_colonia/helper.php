<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_feed
 *
 * @since  1.5
 */
class ModColoniaHelper
{	
	//Buscando as categorias
	public static function getCats($id,$level = 2,$order = 'lft'){

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$pquery = $db->getQuery(true);

		//Nivel de categoria
		$nlevel = $level - 1;

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$pquery->select(array('*'));
		$pquery->from($db->quoteName('#__categories'));
		$pquery->where($db->quoteName('extension') . ' = ' . $db->quote('com_s7dpayments'));
		$pquery->where($db->quoteName('level') . ' = ' . $nlevel);
		$pquery->where($db->quoteName('published') . ' = ' . 1);
		$pquery->where($db->quoteName('parent_id') . ' = ' . $id);
		$pquery->order($order.' ASC');

		$db->setQuery($pquery);
		$buscar = implode(',',array_column($db->loadObjectList(), 'id'));

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select(array('*'));
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('extension') . ' = ' . $db->quote('com_s7dpayments'));
		$query->where($db->quoteName('level') . ' = ' . $level);
		$query->where($db->quoteName('published') . ' = ' . 1);

		//Verificar se level for == 1
		if($level >= 1){
			$pquery->where($db->quoteName('parent_id') . ' = ' . 1);
		}else{
			$query->where($db->quoteName('parent_id') . ' IN('.$buscar.')');
		}

		$query->order($order.' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}


	public static function lastCat($select = '#__categories',$cond = false,$condVal='level'){

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select(array('*'));
		$query->from($db->quoteName($select));

		if($cond)
		{
			$query->where($db->quoteName($condVal) . ' = ' . $db->quote($cond));
		}

		$query->order('id DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;
	}

	/*Atualização em Massa para colônia pelo id pai*/
	public static function update($ids) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Atualizando o desconto em todos os items da categoria pai.
		$fields = array(
		    $db->quoteName('discount') . ' = ' . 10
		);

		// Conditions for which records should be updated.
		$conditions = array(
		    $db->quoteName('catid') . ' IN('.$ids.')'
		);

		$query->update($db->quoteName('#__s7dpayments_courses'))->set($fields)->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return true;
	}

	public static function insertCat($item,$level = 2,$parentId,$path,$lft,$rgt){

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array('title', 'alias', 'parent_id', 'asset_id','level','path','extension','published','params','metadata','created_user_id','version','lft','rgt','access');

		$params = '{"category_layout":"","image":"","image_alt":"","textPayment":"","diarias":"'.$item->diarias.'","tabela":'.json_encode($item->tabela).'}';

		// Insert values.
		$values = array(
			$db->quote($item->title),
			$db->quote(self::awStr($item->title)),
			$db->quote($parentId),
			$db->quote(0),
			$db->quote($level),
			$db->quote($path),
			$db->quote('com_s7dpayments'),
			$db->quote(1),
			$db->quote($params),
			$db->quote('{"author":"","robots":""}'),
			$db->quote(677),
			$db->quote(1),
			$db->quote($lft),
			$db->quote($rgt),
			$db->quote(1)
		);

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__categories'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();

		$new_row_id = $db->insertid();

		return $new_row_id;
	}

	public static function insertItems($item,$catid,$image){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$description = $item->description ? $item->description : 
		'<p>Pacote semanal inclui:</p>
			<ul>
			<li>Hidratação plena com água, dindin de frutas e sucos naturais;</li>
			<li>Lanche da tarde com frutas e uma fonte de proteínas e carboidratos;</li>
			<li>Material pedagógico.</li>
			</ul>';

		// Insert columns.
		$columns = array('title', 'alias', 'catid','type','description','price','discount','state','ordem','image');

		// Insert values.
		$values = array(
			$db->quote($item->title),
			$db->quote(self::awStr($item->title)),
			$db->quote($catid),
			$db->quote(0),
			$db->quote($description),
			$db->quote($item->price),
			$db->quote($item->discount),
			$db->quote(1),
			$db->quote($item->ordem),
			$db->quote($image)
		);

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__s7dpayments_courses'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();

		$new_row_id = $db->insertid();

		return $new_row_id;
	}

	/*Gerar String Limpa*/
	public static function awStr($string)
	{
		$string = strtolower($string);

		$str  = 'á,à,â,ã,å,ä,é,è,ê,ë,í,î,ì,ï,ó,ô,ò,ø,õ,ö,ú,û,ù,ü,ç,ñ,ý';
		$repl = 'a,a,a,a,a,a,e,e,e,e,i,i,i,i,o,o,o,o,o,o,u,u,u,u,c,n,y';
		$repl = explode(',',$repl);
		$str  = explode(',',$str);
		$str  = str_replace($str,$repl,$string);
		$str  = preg_replace('/(\W+)/','-',$str);

		return $str;
	}

	public static function setMenus($item,$catid,$path,$parentId,$level,$lft,$rgt){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array('menutype', 'title', 'alias', 'path', 'link','type','published','parent_id','level','component_id','params','lft','rgt','access');

		// Insert values.
		$values = array(
			$db->quote('hidden'),
			$db->quote($item->title),
			$db->quote(self::awStr($item->title)),
			$db->quote($path),
			$db->quote('index.php?option=com_s7dpayments&view=payments&catid='.$catid),
			$db->quote('component'),
			$db->quote(1),
			$db->quote($parentId),
			$db->quote($level),
			$db->quote(10032),
			$db->quote('{"feccotinho":"ordem ASC","menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0,"helixultimatemenulayout":"","helixultimate_enable_page_title":"0","helixultimate_page_title_alt":"","helixultimate_page_subtitle":"","helixultimate_page_title_heading":"h2","helixultimate_page_title_bg_color":"","helixultimate_page_title_bg_image":"","dropdown_position":"right","showmenutitle":"1","icon":"","class":"","enable_page_title":"0","page_title_alt":"","page_subtitle":"","page_title_bg_color":"","page_title_bg_image":""}'),
			$db->quote($lft),
			$db->quote($rgt),
			$db->quote(1),
		);

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__menu'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	/*Inserir no banco*/
	public static function insertCats($json)
	{
		$json = json_decode($json);


		foreach($json as $kp => $item)
		{
			$parentId = implode('',array_column(self::getCats($item->id,1),'id'));

			$lft = array_column(self::lastCat(),'lft')[0]+1;
			$rgt = array_column(self::lastCat(),'rgt')[0]+1;

			$rgtMenu = array_column(self::lastCat('#__menu'),'rgt')[0]+1;
			$lftMenu = array_column(self::lastCat('#__menu'),'lft')[0]+1;

			//Verificando se existe o meu
			$menuExists = array_column(self::lastCat('#__menu',self::awStr($item->title),'alias'),'alias');

			if(count($menuExists)){

				echo 'Já existe um item com esse mesmo nome';
				return false;
			}

			$path =  implode('',array_column(self::getCats($item->id,1),'alias')).'/'.self::awStr($item->title);

			//Inserindo Categoria
			$insertId = self::insertCat($item,2,$item->id,$path,$lft,$rgt);

			//Inserindo Menu
			self::setMenus($item,$insertId,$path,$item->menuid,2,$lftMenu,$rgtMenu);

			foreach($item->items as $k=> $cat)
			{
				$lid = array_column(self::lastCat('#__categories',2),'id')[0];
				$path = array_column(self::lastCat('#__categories',2),'path')[0].'/'.self::awStr($cat->title);

				$lft = array_column(self::lastCat(),'lft')[0]+1;
				$rgt = array_column(self::lastCat(),'rgt')[0]+1;

				$insertId = self::insertCat($cat,3,$lid,$path,$lft,$rgt);

				//inserindo items
				foreach($cat->products as $km=> $product)
				{
					$imSeq = $item->imageSequence;
					$im = $imSeq+(($k+1)+$km+$k);
					self::insertItems($product,$insertId,'/images/imagesColonia/colonia'.$im.'.jpg');
				}
			}
		}
	}
}
