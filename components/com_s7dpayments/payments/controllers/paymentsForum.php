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

class paymentsForum extends s7dPayments
{
	public static function setTopic()
	{

		// Get the current user object.
		$user = JFactory::getUser();
		$userId = $user->id;

		//Id do curso
		$courseId = "'".$_GET['courseId']."'";

        //Dados do fórum
        foreach($_POST as $fName => $fItems):
        	if($fName == 'title' || $fName == 'description'):
        		$fItemN .= "`".$fName."`,";
        		$fItemV .= "'".$fItems."',";
        		$fItemNC = substr($fItemN,0,-1);
        		$fItemVC = substr($fItemV,0,-1);
        	endif;
        endforeach;

        //date
        $date = "'".date('Y/m/d H:i:s')."'";

        //Inserindo os dados do usúario;
        $db =& JFactory::getDBO();

        $query = "INSERT INTO `#__s7dpayments_forum` ($fItemNC,`discussions`,`date`,`userId`,`courseId`)
        VALUES ($fItemVC,'[]',$date,$userId,$courseId);";
        $db->setQuery( $query );

		if($db->query())
		{
			$output  = '<div class="alert alert-success alert-dismissable">';
  			$output .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
  			$output .= 'Tópico cadastrado com sucesso!';
			$output .= '</div>';
		}
       	
       	echo $output;
		return true;
	}

	public static function getTopic()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		 
		// Create a new query object.
		$query = $db->getQuery(true);
		 
		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','title','courseId','userId','description','discussions','date')));
		$query->from($db->quoteName('#__s7dpayments_forum'));
		if(isset($_GET['topic']) && isset($_GET['itemId'])):
			$query->where($db->quoteName('id') .'='. $db->quote($_GET['itemId']));
		endif;
		$query->order('date DESC');
		 
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		 
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		return $results;

	}

	public static function updateTopic()
	{
		$db = JFactory::getDbo();

		// Get the current user object.
		$user = JFactory::getUser();
		$userId = $user->id;

		$date = date('Y/m/d H:i:s');

		//Complementos para a discursão
		$supplements = array
		(
			"userId" => $userId,
			"datePost" => $date
		);

		foreach($supplements as $ck=> $suppls):
			$arraySupp[$ck] = $suppls;
        endforeach;

        
        //Buscando os dados da discursão
        $listT = array();
        foreach(self::getTopic() as $k=> $items):

        	//Quantidade de discursões
       		$count = count(json_decode($items->discussions,true));

        	foreach(json_decode($items->discussions) as $dk=> $dkitems):
        		$listT[$dk] = $dkitems;
        	endforeach;
        endforeach;

        //Adicionando mais 1
        $fMais = $count +1;

		//Capturando os dados
        $list = array();


        foreach($_POST['topic'] as $k=> $pts):
        	if($k == 'discussions' and ($pts['message'])):
        		$list[$k.$fMais] = array_merge($arraySupp,$pts);
        	else:
        		null;
        	endif;
        endforeach;

        //Criando o primeiro discurso
        $discussions = strip_tags(addslashes(json_encode(array_merge($listT,$list), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)));

    	//Id do Tópico
    	$itemId = $_GET['itemId'];

        $query = "UPDATE #__s7dpayments_forum
        SET discussions = '".$discussions."'
        WHERE id ='".$itemId."'
        ";
        $db->setQuery($query);
		
		$db->query();
	
		return true;

	}


	public static function getTopicName($dado,$name,$id)
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery('SELECT #__'.$dado.'.'.$name.' FROM #__'.$dado.' WHERE  id = '.$id);

        return $cdb->loadResult();
	}

	public static function executTopic()
	{
		if(isset($_POST['tenviar']) && isset($_GET['topic'])):
			self::updateTopic();
		elseif(isset($_POST['tenviar']) && isset($_GET['addtopic'])):
			self::setTopic();
		endif;
	}

}
