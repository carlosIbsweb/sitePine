<?php

/**
 * @subpackage	mod_wknewss7d
 * @copyright	Copyright (C) 2017 - Web Keys.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

//require_once(JPATH_SITE.'/modules/mod_wknewss7d/elements/wkcrop/WideImage.php');

// Load dependent classes.
require_once JPATH_SITE . '/components/com_s7dgallery/router.php';
JLoader::registerPrefix('S7dgallery', JPATH_SITE . '/components/com_s7dgallery/');

use Joomla\Utilities\ArrayHelper;


class modWknewss7dHelper
{
	static function getItems(&$params)
	{
		
	}

	static function setStyle(&$params,$style,$mId,$sliderItems)
	{
		// Get the document object.
		$doc = JFactory::getDocument();

		//Styles.
		$styleImg = $style;
		switch ($styleImg) {     
			case 'top':
				$styles = ' 
				#wk-news-'.$mId.' .wk-news-img {float:none;}
				#wk-news-'.$mId.' .wk-news-content {margin-top: 10px; overflow: inherit}
				';
				break;
			case 'left':
				$styles = '
					#wk-news-'.$mId.' .wk-news-img {margin-right:15px; float:left}
				';
				break;
			case 'bottom':
				$styles = '
					#wk-news-'.$mId.' .wk-news-img img {width:100%}
					#wk-news-'.$mId.' .wk-news .wk-news-content {margin-bottom: 10px}
				';
				break;
			case 'right':
				$styles = '
					#wk-news-'.$mId.' .wk-news-img {float: right !important; margin-left: 15px;}
				';
				break;
		
			default:
				# code...
				break;
		}

		//Border img;
		$imgexborder = $params->get('imgexborder') == 1 ? 'display:block' : 'display:none';
		$stylesExBorderImg = ' #wk-news-'.$mId.' .wk-news-img:before {border-color:'. $params->get("imgbordercolor"). '; border-width: '.$params->get('imgborderwidth').'; '.$imgexborder.' }';

		//Count slider items
		$sliderItems = $params->get('sliderItems');
		//Responsive Slider
		$rSlider = $sliderItems > 1 ? 2 : $sliderItems;
		
		//Title style
		$titleColorSlider 	= $params->get('exstyletitle') == 1 ? 'color: '. $params->get('titlecolorslider') : null;
		$titleColorDefault 	= $params->get('exstyletitle') == 1 ? 'color: '. $params->get('titlecolor') : null;
		$titleHoverColor 	= $params->get('exstylehover') == 1 ? '#wk-news-'.$mId.' .wk-news-item:hover .wk-news-title h4 {color: '. $params->get('titlehovercolor').'}' : null;
		$titleDefault 		= ' #wk-news-'.$mId.' .wk-news-title h4 {font-size:'. str_replace("px","",$params->get("titlesize")).'px; '.$titleColorDefault. '; font-weight: '.$params->get('titleWeight').' }';
		$titleSlider  		= ' #wk-news-'.$mId.' .wk-news-title h4 {font-size:'. str_replace("px","",$params->get("titlesize")).'px; '.$titleColorSlider. ' }';
		$introTextColor 	= ' #wk-news-'.$mId.' .wk-news-content p {color: '. $params->get('introTextColor').'}';
		
		//Hat style
		$hatStyle = $params->get('hatback') == '' ? '#wk-news-'.$mId.' .wk-news-hat {padding:0}' : '#wk-news-'.$mId.' .wk-news-hat {padding: 2px 8px}';
		
		$styles .= ' #wk-news-'.$mId.' .wk-news-img {width:'. str_replace("%","",$params->get("blockImgWidth")).'% }';
		$styles .= $column;
		$styles .= ' #wk-news-'.$mId.' .wk-news-hat {font-size:'. str_replace("px","",$params->get("hatsize")).'px; color:'. $params->get("hatcolor"). ' }';
		$styles .= ' #wk-news-'.$mId.' .wk-news-hat {background-color:'. $params->get("hatback"). ' }';
		$styles .= $params->get('imgexborder') == 1 ? $stylesExBorderImg : null;  /*Style border*/
		$styles .= $params->get('format') == 'slider' ? $titleSlider : $titleDefault;  /*Style title*/
		$styles .= $params->get('exstylehover') == 1 ? $titleHoverColor : null;  /*Style titleHover*/
		$styles .= $params->get('exIntroTextColor') == 1 ? $introTextColor : null;
		$styles .= $hatStyle;
		
		$doc->addStyleDeclaration($styles);

		//Slider Styles
		$stylesScript = " 
			jQuery(function($){
				$(document).ready(function() {
	              var owl = $('.wk-news-slider-".$mId."');
	              owl.owlCarousel({
	                margin: 10,
	                nav: true,
	                items: ".$sliderItems.",
	                loop: false,
	                video:true,
	                center: false,
	                rewind: true,
	                dots: false,
	                autoplay: true,
	                autoplayHoverPause: true,

	                mouseDrag: true,
	                touchDrag: true,
	                pullDrag: true,
	                freeDrag: false,

	                stagePadding: 0,

	                merge: false,
	                mergeFit: true,
	                autoWidth: false,

	                startPosition: 0,
	                rtl: false,

	                smartSpeed: 250,
	                fluidSpeed: false,
	                dragEndSpeed: false,

	                responsive:{
				        0:{
				            items:1
				        },
				        600:{
				            items:".$rSlider."
				        },
				        992:{
				        	items:".$sliderItems.",
				        }

				    }
	              })
			    })
			});
		";

		if($params->get('format') == 'slider'):
			$doc->addScriptDeclaration($stylesScript);
		endif;
	}

	static function col(&$params) {
		$col = $params->get('columns');
		$cols = array(
			"1" => " wk-col-lg-12",
			"2" => " wk-col-lg-6",
			"3" => " wk-col-lg-4",
			"4" => " wk-col-lg-3",
			"5" => " wk-col-lg-5ths",
			"6" => " wk-col-lg-2"
		);

		$cols = $params->get('format') == 'slider' ? $cols[1] : $cols[$col];

		return $cols;
	}

	static function newsFormat(&$params,$title, $introtext = null,$id, $date = null,$link) {

		//Hat articles.
		$hatN = !empty(self::getTag($id)) && $params->get('exhat') == 1 && $params->get('format') == 'default'  ? '<span class="wk-news-hat">' .self::getTag($id). '</span>' : '';
		//Date.
		//$dPublished = $params->get('pDate') == 1 ? '<span class="wk-news-date">'.self::diffDates($date).'</span>' : '';
		$dPublished = $params->get('pDate') == 1 ? '<span class="wk-news-date">'.self::exibirData($date).'</span>' : '';

		//Introtext
		$text = $params->get('exintro') == 1 ? '<p> ' .self::lText($introtext,$params->get('introLimit')) . '</p>' : null;

		$exLink = $params->get('exLink') != 0 ? true : false;
		
		$output = array();
		$output[] = '<div class="wk-news-content">';
		$output[] = 	$hatN;
		$output[] = 	'<div class="wk-news-title">';
		$output[] = 	$exLink == true ? '<a href="'.$link.'">' : null;
		$output[] = 		'<h4>'.self::lText($title,$params->get('textLimit')).'</h4>';
		$output[] = 	$exLink == true ? '</a>' : null;
		$output[] = 	'</div>';
		$output[] = 	$dPublished;
		$output[] = $text;
		$output[] = '</div>';

		return implode('',$output);
	}

	public static function getList(&$params)
	{
		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$catid = array_filter($params->get('catid',array()));
		$catids = !empty($catid) ? $db->quoteName('catid') . " IN ( " . implode(',',$catid) . ")" : $db->quoteName('state') . ' = ' . $db->quote('1');

		$featured = $params->get('show_featured') == 2 ? $db->quoteName('featured') .' = '. $db->quote(1) : (
			$params->get('show_featured') == 0 ? $db->quoteName('featured') .' = '. $db->quote(0) : $db->quoteName('state') . ' = ' . $db->quote('1') );

		// Set ordering
		$order_map = array(
			'o' => 'ordering',
			'a_d' => 'id DESC',
			'a_a' => 'id ASC',
			'o_i' => 'ordering DESC',
			//'random' => $db->getQuery(true)->Rand(),
		);

		$ordering = $order_map[$params->get('ordering','a_d')];
		
		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__s7dgallery_albums'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'))
			->where($catids)
			->where($featured)
			->order($ordering);

		$inicio = $params->get('inicio');

		
		// Set the query and load the result.
		$db->setQuery($query,$inicio,$params->get('count'));
		
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


	/***************--------------------------------
   *Resize
  ---------------------------------***************/ 
  public static function sImage($path,$imageOri,$imageNew,$size,$type=null,$direct = null)
  {

    if(!is_dir($path)){
      mkdir($path,0777, true);
      chmod($path, 0777);
    }

    //Extensions
    $extension  = strtolower(pathinfo($imageNew,PATHINFO_EXTENSION));

	//Tamanho da imagem
    $inSize = explode("x",strtolower($size));
    $width  = $inSize[0];
    $height = $inSize[1];

    if(!is_array($inSize)){
      $width = '400';
      $height = '200';
    }

    //Direction
    $directIn = explode(",",$direct);
    list($dTop,$dLeft) = $directIn;

    if(empty($direct))
    {
    	$dTopA 	= 0;
    	$dLeftA = 0;
    }else{

    $dTopA 	= "'".$dTop."'";
    $dLeftA = "'".$dTop."'";
    }

    if(file_exists($imageNew))
    {
    	//Size Image
    	list($imgW,$imgH) = getimagesize($imageNew);
	}

    if($imgW == $width && $imgH == $height){
    	return false;
    }
    else{
	    // Carrega a imagem a ser manipulada
	    $image = wideImage::load($imageOri);

	    // Redimensiona a imagem
	    switch ($type) {
	      case 'crop':
	        $image = $image->resize($width, $height,outside);
	        $image = $image->crop($dTopA,$dLeftA, $width, $height);
	        break;

	      case 'resize':
	        $image = $image->resizeDown($width, $height,inside,down);
	        break;
	      case 'resizeOut':
	        $image = $image->resize($width, $height,outside);
	        break;

	      default:
	        $image = $image->resizeDown($width, $height,inside);
	        break;
	    }
    
    	// Salva a imagem em um arquivo (novo ou não)
	    if($extension != 'png')
	    {
	      $image->saveToFile($imageNew,90);
	    }
	    else
	    {
	      $image->saveToFile($imageNew,9,PNG_NO_FILTER);
	    }

    	return true;

	}
  }

	public static function diffDates($date1)
	{
		$datatime1 = new DateTime($date1);
		$datatime2 = new DateTime(date('Y/m/d H:i:s'));
		
		$data1  = $datatime1->format('Y-m-d H:i:s');
		$data2  = $datatime2->format('Y-m-d H:i:s');

		//Formato da data original.
		$data = $datatime1->format('d/m/Y');

		/*$diff = $datatime1->diff($datatime2);
		$horas = $diff->h + ($diff->days * 24);

		$minutos = $diff->format('%i') >= 1 ? $diff->format('%i') : null;
		
		$tdias = $horas >= 48 ? ' dias' : ' dia';
		$thoras = $diff->format('%h') >= 2 ? ' horas' : ' hora';
		$tminutos = $diff->format('%i') >= 2 ? ' minutos' : ($diff->format('%i') >= 1 ? ' minuto' : ' alguns segundos');
		
		$hormin = $diff->format('%h') == 0 ? $minutos . $tminutos : $diff->format('%h') . $thoras;
		$diahor = $horas >= 24 ? (int)($horas /24) . $tdias :  $hormin;

		$anomes = $horas >= 168 ?  $data : 'Há ' .$diahor;*/

		return $data;
	}

	public static function getTag($itemId){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
		    ->select(array('a.title', 'b.content_item_id'))
		    ->from($db->quoteName('#__tags', 'a'))
		    ->join('INNER', $db->quoteName('#__contentitem_tag_map', 'b') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.tag_id') . ')')
		    ->where($db->quoteName('b.content_item_id') . ' = '.$itemId)
		    ->order($db->quoteName('a.id') . ' DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();

		$tagTitle = array();
		foreach($results as $item){
			$tagTitle[] = $item->title;
		}

		return implode(",",$tagTitle);
	}

	public static function lText($text,$limit) {
		
		$mText = strip_tags($text);
		$count = strlen($mText);
		$TextClean = substr($mText, 0, strrpos(substr($mText, 0, $limit), ' '));
		
		$output = $count > $limit && !empty($limit) ? $TextClean.'...' : $mText;
		
		return $output;
	}

	public static function getVideo($text){
		
		preg_match("/https:\/\/www.youtube.com\/embed\/(.*?)\"/", $text, $video);

		if(empty($video[1])){
			return false;
		}

		$items = new stdClass();

		$items->img = 'http://i1.ytimg.com/vi/'.$video[1].'/0.jpg';
		$items->url = $video[1];

		return $items;
	}
	
	public function exibirData($date){
		
		$dateFinal = explode(" ", $date);
		$dateFinal = explode("-", $dateFinal[0]);
		$dateFinal = $dateFinal[2].'/'.$dateFinal[1].'/'.$dateFinal[0];
		return  $dateFinal;	
   	}	

}