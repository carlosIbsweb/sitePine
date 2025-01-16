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

jimport('joomla.application.component.controllerform');

/**
 * Course controller class.
 *
 * @since  1.6
 */
class S7dpaymentsControllerCourse extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'courses';
			if(isset($_REQUEST['dform']['arquivo']['title']) and  $_REQUEST['dform']['arquivo']['title'] != '' || $_REQUEST['delitem'] != ''):
				$this->addN(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id')),$_POST['dform']);
			endif;
		//$this->addN(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id')),$_POST['dform']);
		parent::__construct();
	}

	public function arquivo()
	{
		// ---------------------------- Uploading the file ---------------------
    	// Neccesary libraries and variables
    	jimport( 'joomla.filesystem.folder' );
    	jimport('joomla.filesystem.file');

   		$input = JFactory::getApplication()->input;
   		$titlepdf = $_REQUEST['titlepdf'];
   		$files = $input->files->get('file');

   		//Pasta dos arquivos
   		$paste = JPATH_SITE . '/components/com_s7dpayments/assets/files_dpFsdf05b55d133c4162c4953fc97eebb093/course'.JRequest::getInt('id').'/';

   		$lista = array();
		foreach($files as $k=> $file):
			$ext = strtolower(strrchr($file['name'],'.'));
			$title = $this->clear(str_replace(strrchr($file['name'],'.'),'',$file['name']));


			$lista[$titlepdf] = $title;
			if(strstr('.pdf', $ext)):
	    		$src = $file['tmp_name'];
	    		$dest = $paste.$title.$ext;
	    	endif;
	    	if($_REQUEST['delitem'] == ''):
				$upok = JFile::upload($src, $dest);
			endif;
   	 	endforeach;

   	 	//Pegando os dados para edição
   	 	$listb = array();
		foreach(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id'))) as $k=> $items):
			$listb[$k] = $items;
		endforeach;

		//Deletando os arquivos
   	 	$listd = array();
		foreach(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id'))) as $k=> $items):
			if($items != $_REQUEST['delitem']):
				$listd[$k] = $items;
			else:
				unlink($paste.$items.'.pdf');
				//Excluindo a pasta do curso caso esteja vazia
				rmdir(substr($paste,0,-1));
			endif;
		endforeach;

		$fjson = $_REQUEST['delitem'] != '' ? json_encode($listd,JSON_UNESCAPED_UNICODE) : json_encode(array_merge($lista,$listb),JSON_UNESCAPED_UNICODE);

   	 	$db =& JFactory::getDBO();
		$query = "UPDATE #__s7dpayments_courses
             SET arquivos = '".$fjson."'
              WHERE id = '".JRequest::getInt('id')."'
             ";
		$db->setQuery($query);

		if($upok || $_REQUEST['delitem'] != ''):
			$db->query();
		endif;
 
	}

	public function getItems($table,$name,$id)
	{
		$cdb = JFactory::getDbo();

        $cdb->setQuery("SELECT #__$table.$name FROM #__$table where id = ".$id);

        return $cdb->loadResult();
	}

	public function clear($string)
	{
    	// matriz de entrada
    	$entrada = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','X','W','Y','Z','ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç','_','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );

    	// matriz de saída
    	$saida   = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','x','w','y','z','a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-' );

    	// devolver a string
    	$mystring = str_replace($entrada, $saida, $string);

    	$mystringl = implode("-",array_unique(array_filter(explode(" ",$mystring))));

    	return implode("-",array_unique(array_filter(explode("-",$mystringl))));
	}

	public function ExcluiDir($Dir)
	{
    
    if ($dd = opendir($Dir)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $Path = "$Dir/$Arq";
                if(is_dir($Path)){
                    ExcluiDir($Path);
                }elseif(is_file($Path)){
                    unlink($Path);
                }
            }
        }
        closedir($dd);
    }
    rmdir($Dir);
	}

	public function addN($busca,$post)
	{
		
		$input = JFactory::getApplication()->input;
		jimport( 'joomla.filesystem.folder' );
    	jimport('joomla.filesystem.file');
		$file = $input->files->get('file');
		
		//Pasta dos arquivos
   		$paste = JPATH_SITE . '/components/com_s7dpayments/assets/files_dpFsdf05b55d133c4162c4953fc97eebb093/course'.JRequest::getInt('id').'/';

   		$lista = array();
		foreach($file as $k=> $file):
			$ext = strtolower(strrchr($file['name'],'.'));
			$title = $this->clear(str_replace(strrchr($file['name'],'.'),'',$file['name']));


			$lista[$titlepdf] = $title;
			if(strstr('.pdf', $ext)):
	    		$src = $file['tmp_name'];
	    		$dest = $paste.$title.$ext;
	    	endif;
	    	if($_REQUEST['delitem'] == ''):
				$upok = JFile::upload($src, $dest);
			endif;
   	 	endforeach;

		$i = 1;
		$list = array();
		foreach(json_decode($busca) as $k=> $is):
			$list["arquivo".$i] = $is;
			$i++;
		endforeach;

		$count = count($list);

		$lists = array();
		foreach($post as $kc=> $ic):
			foreach($ic as $kar => $iar):
				if($kar != 'linkpdf'):
					$lists["arquivo".($count+1)][$kar] = $iar;
				else:
					$lists["arquivo".($count+1)][$kar] = $title;
				endif;
			endforeach;
		endforeach;

		//Pegando os dados para edição
   	 	$listb = array();
		foreach(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id'))) as $k=> $items):
			$listb[$k] = $items;
		endforeach;

		//Deletando os arquivos
   	 	$listd = array();
		foreach(json_decode(self::getItems('s7dpayments_courses','arquivos',JRequest::getInt('id'))) as $k=> $items):
				if($k != $_REQUEST['delitem']):
					$listd[$k] = $items;
				else:
					unlink($paste.$items->linkpdf.'.pdf');
					//Excluindo a pasta do curso caso esteja vazia
					rmdir(substr($paste,0,-1));
				endif;
		endforeach;

		$fjson = $_REQUEST['delitem'] != '' ? json_encode($listd,JSON_UNESCAPED_UNICODE) : json_encode(array_merge($lists,$list),JSON_UNESCAPED_UNICODE);

		$db =& JFactory::getDBO();
		$query = "UPDATE #__s7dpayments_courses
             SET arquivos = '".$fjson."'
              WHERE id = '".JRequest::getInt('id')."'
             ";
		$db->setQuery($query);

		if($upok || $_REQUEST['delitem'] != ''):
			$db->query();
		endif;

		return true;

	}
}
