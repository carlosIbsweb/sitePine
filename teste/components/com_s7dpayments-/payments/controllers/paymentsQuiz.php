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

class paymentsQuiz extends s7dPayments
{
	public static function getQuiz($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('QuizId', 'CategoryId')));
		$query->from($db->quoteName('#__ariquizquizcategory'));
		$query->where($db->quoteName('CategoryId') . ' = '. $db->quote($id));

		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		foreach($results as $item)
		{
			$quizid = $item->QuizId;
		}

		return $quizid;

	}


	public static function getQuizList($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('QuizId', 'CategoryId')));
		$query->from($db->quoteName('#__ariquizquizcategory'));
		$query->where($db->quoteName('CategoryId') . ' = '. $db->quote($id));

		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;

	}


	public static function getQuizes($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('QuizName', 'QuizId' )));
		$query->from($db->quoteName('#__ariquiz'));
		$query->where($db->quoteName('QuizId') . ' = '. $db->quote($id));
		$query->where($db->quoteName('Status') . ' = '. $db->quote(1));

		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		foreach($results as $items)
		{
			$quizName = $items->QuizName;
		}

		return $quizName;

	}
}