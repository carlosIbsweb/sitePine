
<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe Aw Valid.
 Desenvolvido por Carlos (IBS WEB)
********/

class awFileUploader {
		//Upload
		public static function upload($files,$modId)
		{

			/*
				Usando o Json
			$a = self::upload($iFiles);

			foreach(json_decode($a,true) as $k=> $fs){
				foreach($fs as $k=> $foto)
				{
					echo $k;
					foreach($foto as $fi)
					{
						echo $fi;
					}
				}
			}*/
			$fJson = array();
			$i = 0;


			//print_r($files);
			foreach($files as $k=> $file)
			{	
				$path = JPATH_ROOT.'/s7dforms/'.$modId.'/'.$k;
				$folder = $path.'/';
				if(!is_dir($path)){
	      			mkdir($path,0777, true);
	      			chmod($path, 0777);
	    		}

				$fNames = array();
				for($if = 0;$if <= count($file['name']); $if++)
				{	
					$fn = $file['name'][$if];
					$fid = uniqid();
					$ex = strtolower(pathinfo($fn,PATHINFO_EXTENSION));
					$fileName = $fid.'.'.$ex;

					if(JFile::upload($file['tmp_name'][$if],$folder.$fileName)){
						array_push($fNames, $fileName);
					}
				}
				
				array_push($fJson, [$k=>$fNames]);
			
				$i++;
			}

			return json_encode($fJson);
		}

		public static function uploadFail($files,$exPer)
		{
			//Extensions
			$exPer = $exPer != '' ? explode(',',$exPer) : ['jpg','png'];
			foreach($files as $k=> $file){
				for($if = 0;$if < count($file['name']); $if++)
				{
					$fn = $file['name'][$if];
					$ex = strtolower(pathinfo($fn,PATHINFO_EXTENSION));

					$fileName = $fid.'.'.$ex;

					if(!in_array($ex, $exPer))
					{
						echo awUtilitario::awMessages('Upload de arquivos não permitidos. Só são permitidos arquivos <strong>'.implode(',',$exPer).'</strong>','danger');
						return false;
					}	
				}
			}
			
			return true;
		}
}