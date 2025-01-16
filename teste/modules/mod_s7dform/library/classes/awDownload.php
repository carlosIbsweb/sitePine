
<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe Aw Captcha.
 Desenvolvido por Carlos (IBS WEB)
********/

class awDownload {

	public static function awFile($params)
	{
		set_time_limit(0);

		$file = $params->get('redirectDownload');
		$file = array_filter(explode('/',$file));
		$file = implode('/',$file);

		$arquivo = basename($file);
		$caminho_download = JPATH_ROOT.'/'.$file;

		// Verificação da existência do arquivo
		if (!file_exists($caminho_download)){
		   die('Arquivo não existe'.$caminho_download);
		}else
		{
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="'.$arquivo.'"');
			header('Content-Type: image/png');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($arquivo));
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Expires: 0');
			ob_end_clean(); //essas duas linhas antes do readfile
			flush();
			readfile($caminho_download);
			exit();
		}
	}

	public static function awMessages($msg,$alert)
	{
		$alert = !empty($alert) ? $alert : 'success'; 
		$message = [];
		$message[] = '<div class="alert alert-'.$alert.'">';
  		$message[] = $msg;
		$message[] = '</div>';

		return implode('',$message);
	}
	

}