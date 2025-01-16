<?php

/**
 * @subpackage	mod_s7d_scroll
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Acesso
defined('_JEXEC') or die;

class modS7dScroll
{
	public static function getScroll(&$params)
	{
		$items = new stdClass();
		$items->items = self::getArticles($params->get('caton'),$params->get('catid'),$params->get('qntArticles'));
		$items->tclass = $params->get('tclass');
		$items->links = explode("\n",$params->get('links'));

		//Style
		self::getStyle($params);

		return $items;
	
	}

	public static function getArticles($caton = null, $catid = null, $qnt)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'title','alias','catid','images')));
		$query->from($db->quoteName('#__content'));
		$query->where($db->quoteName('state') . ' = '. $db->quote('1'));
		$query->where($db->quoteName('id') . ' != '. $db->quote(JRequest::getInt('id')));

		if($catid):
			//Categories
			foreach($catid as $cats):
				$categories .= $cats.',';
			endforeach;
			
			$query->where($db->quoteName('catid').'IN('.substr($categories,0,-1).')');
		else:
			$query->where($db->quoteName('title') . ' LIKE '. $db->quote('%'.explode(" ",JFactory::getDocument()->getTitle())[0].'%'));
		endif;

		$query->order('id DESC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$qnt);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results; 
	}

	public static function getCategory($id)
	{
		$db = &JFactory::getDBO(); 
	    $db->setQuery('SELECT #__categories.title FROM #__categories WHERE  #__categories.id = '.$id); 
	    $categoryid = $db->loadResult();
	    
	    return $categoryid;
	}

	public static function getStyle($params)
	{
		
		// Get the document object.
		$doc = JFactory::getDocument();

		//Border Hover
		$sbhover = $params->get('sbhover') == 1 ? 'box-shadow: 0 0 0 2px '.$params->get('sbcolor') : null;

		// Style
		$style = 
		'
		    #owl-demo .item{
		        margin: '.str_replace("px", "px", $params->get('smscroll')).';
		    }
		    #owl-demo .item img{
		        display: block;
		        width: 100%;
		        background:'.$params->get('sbackimg').';
		        height: auto;
		        padding: '.str_replace("px","px",$params->get('spimg')).';
		        border-radius: '.str_replace("px","px",$params->get('sbradius')).';
		    }
		    #owl-demo .item p {
		        text-align: '.$params->get('spalign').'
		    }
		    #owl-demo .item:hover img {
				'.$sbhover.'
			}
		';

		$doc->addStyleDeclaration($style);

		return false;
	}
	
}
