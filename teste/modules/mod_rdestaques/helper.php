<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_rslider
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class ModRdestaquesHelper
{
	
	public static function getList(&$params)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$catid = array_filter($params->get('catid',array()));
		$catids = !empty($catid) ? $db->quoteName('catid') . " IN ( " . implode(',',array_filter($catid)) . ")" : $db->quoteName('state') .' = '. $db->quote(1);

		$featured = $params->get('show_featured') == '1' ? ' and '.$db->quoteName('featured') .' = '. $db->quote(1) : (
			$params->get('show_featured') == '0' ? ' and '.$db->quoteName('featured') .' = '. $db->quote(0) : false );

		// Set ordering
		$order_map = array(
			'a_r' => 'id DESC',
			'a_mr' => 'modified DESC',
			'a_rp' => 'publish_up',
			'a_ma' => 'hits DESC',
			'random' => $db->getQuery(true)->Rand(),
		);

		$ordering = $order_map[$params->get('ordering','a_r')];
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__content'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'))
			->where($catids.$featured)
			->order($ordering);

		
		// Set the query and load the result.
		$db->setQuery($query,0,$params->get('count'));
		
		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $result;
		
	}

	public static function getCat(&$params,$id,$sItem)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$catid = $params->get('catid',array());
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'));
		
		// Set the query and load the result.
		$db->setQuery($query);
		
		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		foreach($result as $item)
		{
			if($item->id == $id)
			{
				$searchItem = $sItem;
				$cattitle = $item->$searchItem;
			}
			
		}
		
		return $cattitle;
	}

	public static function rImage($imgOri,$path,$imgThum,$imgW,$imgH){

	    $image = imagecreatefromjpeg($imgOri);
	   	
	   	/***********
	   	 *Criar pasta
	   	***********/
	    if(!is_dir($path)){
  			mkdir($path,0777, true);
  			chmod($path, 0777);
  		} 

  		//Nome do arquivo thumb.
  		$filename = $imgThum;

	    $thumb_width = $imgW;
	    $thumb_height = $imgH;

	    $width = imagesx($image);
	    $height = imagesy($image);

	    $original_aspect = $width / $height;
	    $thumb_aspect = $thumb_width / $thumb_height;

	    if ( $original_aspect >= $thumb_aspect )
	    {
	       // If image is wider than thumbnail (in aspect ratio sense)
	       $new_height = $thumb_height;
	       $new_width = $width / ($height / $thumb_height);
	    }
	    else
	    {
	       // If the thumbnail is wider than the image
	       $new_width = $thumb_width;
	       $new_height = $height / ($width / $thumb_width);

	    }

	    $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

	    if(file_exists($filename)){
	    	//Verificando tamanho da imagem
			list($tw,$th) = getimagesize($filename);
	    }

	    if($imgW == $tw && $imgH == $th){
	      return false;
	    }else{
	      // Resize and crop
	      imagecopyresampled($thumb,
	                       $image,
	                       0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
	                       0 - ($new_height - $thumb_height) / 2, // Center the image vertically
	                       0, 0,
	                       $new_width, $new_height,
	                       $width, $height);
	      imagejpeg($thumb, $filename, 80);
	    }
	}

	public static function tImage($image,$folder,$id,$width,$height){
		$imgIntro = $image;
		$imgInfo = pathinfo($imgIntro);
		$imgPath = Juri::base(true).'cache/'.$folder;
		$imgName = $imgInfo['filename'];

		if(!empty($image)){
			$imgThumb = $imgPath.'/'.$id.'-'.$imgName.'.jpg';
			$imgResize = self::rImage($imgIntro,$imgPath,$imgThumb,$width,$height);
		}else{
			$imgThumb = '';
		}
		

		return $imgThumb;

	}

	public static function diffDates($date1)
	{
		$datatime1 = new DateTime($date1);
		$datatime2 = new DateTime(date('Y/m/d H:i:s'));

		$data1  = $datatime1->format('Y-m-d H:i:s');
		$data2  = $datatime2->format('Y-m-d H:i:s');

		$diff = $datatime1->diff($datatime2);
		$horas = $diff->h + ($diff->days * 24);

		$minutos = $diff->format('%i') >= 1 ? $diff->format('%i') : null;
		

		$tano = (int)($horas / 8760) >= 2 ? ' anos' : ' ano';
		$tmes = (int)($horas /730) >= 2 ? ' mêses' : ' mês';
		$tsemana = (int)($horas /168) >= 2 ? ' semanas' : ' semana';
		$tdias = $horas >= 48 ? ' dias' : ' dia';
		$thoras = $diff->format('%h') >= 2 ? ' horas' : ' hora';
		$tminutos = $diff->format('%i') >= 2 ? ' minutos' : ($diff->format('%i') >= 1 ? ' minuto' : ' alguns segundos');
		
		$hormin = $diff->format('%h') == 0 ? $minutos . $tminutos : $diff->format('%h') . $thoras;

		$diahor = $horas >= 24 ? (int)($horas /24) . $tdias :  $hormin;

		$semdia = $horas >= 168 ? (int)($horas /168) . $tsemana : $diahor;

		$mesdia = $horas >= 730 ? (int)($horas /730) . $tmes : $semdia;

		$anomes = $horas >= 8760 ? (int)($horas /8760) . $tano : $mesdia;

		return 'Há ' .$anomes;

		
	}
}
