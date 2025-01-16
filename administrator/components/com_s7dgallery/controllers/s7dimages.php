<?php

/**
 * @version     1.0.0
 * @package     com_s7dlv
 * @copyright   Copyright (C) 2018. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport( 'joomla.application.component.helper' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.image.image');
jimport('joomla.filter.output');
require_once(JPATH_ADMINISTRATOR.'/components/com_s7dgallery/lib/s7dcrop/WideImage.php');

/**
 * Lvimages controller class.
 *
 * @since  1.6
 */
class S7dgalleryControllerS7dimages extends JControllerForm
{
  /***************--------------------------------
   *Upload
  ---------------------------------***************/ 
	public function s7dUpload()
	{
  		
      $input        = JFactory::getApplication()->input;
      $filesName    = $_FILES['file']['name'];
  		$filesTmp     = $_FILES['file']['tmp_name'];
      $itemId       = $input->post->get('itemId', '', 'int');
      $mitem         = $input->post->get('mitem','','string');

      $model        = $this->getModel();

      $inputImages  = array();
      $images       = array();
      $report       = array();

      $folder       = JPATH_ROOT.'/images/s7dgallery/gal-'.$itemId.'/';
      $folderRoot   = JUri::root(true).'/images/s7dgallery/gal-'.$itemId.'/';
      $fThumbs      = 'thumbs/';
      $fMedium      = 'medium/';
      $fLarge       = 'large/';

      #Formato suportado.
      $formats = ['jpg', 'jpeg', 'png'];

      $fileName   = strtolower(pathinfo($filesName,PATHINFO_FILENAME));
      $extension  = strtolower(pathinfo($filesName,PATHINFO_EXTENSION));
      $isImage    = $fileName.'.jpg';

      if(in_array($extension,$formats))
      {
        #Incrementando imagens repetidas
        if(file_exists($folder.$fThumbs.$isImage))
        {
          $in       = self::incrementArr($folder.$fThumbs,$formats,$fileName,'_');
          $image    = $fileName.$in.'.jpg';
          $fileName = $fileName.$in;
        }
        else
        {
          $image = $isImage;
          $fileName = $fileName.$in;
        }

        //New File Name
        $newFileName = pathinfo($image,PATHINFO_FILENAME);

        if(JFile::upload($filesTmp,$folder.$image) &&
          //thumbs
          self::sImage($folder.$fThumbs,$folder.$image,'460x320','crop') &&
          //medium
          self::sImage($folder.$fMedium,$folder.$image,'600x300','resize') &&
          //large
          self::sImage($folder.$fLarge,$folder.$image,'1200x1000'))
        {
          
          ##Inserindo no Banco
          $imgId = uniqid();

          if(file_exists($folder.$fThumbs.$image)){
          
          //Conteudo das imagens de saída
          $report['image'] = self::getImagesContent($folderRoot.$fThumbs,$image,$imgId);
          $report['img']   = $image;
          $report['id']    = $imgId;
          $report['itemId'] = $itemId;
          $report['mitem'] = $mitem;
          $report['imgname'] = $fileName;

          $report['upload'] = true;
        }
        }
        else
        {
          $report['upload'] = false;
        }
          $report['format'] = true;
      }
      else
      {
        $report['format'] = false;
        $report['status'] = "formato não suportado";
      }

      if($report['upload'] == true){
        //Excluindo a imagem original.
        JFile::delete($folder.$image);
      }
      
        
        echo json_encode($report);
        exit();
	}

 
  /***************--------------------------------
   *Delete Image
  ---------------------------------***************/ 
  public function delete_image()
  {
    
    $input        = JFactory::getApplication()->input;
    $model        = $this->getModel();
    $deleteIds    = $input->post->get('cids','','array');
    $itemId       = $input->post->get('itemId','','int');
    $fileData     = $input->post->get('images','','string');
    $folder       = JPATH_ROOT.'/images/s7dgallery/gal-'.$itemId.'/';
    $fThumbs      = $folder.'thumbs/';
    $fSmall       = $folder.'small/';
    $fMedium      = $folder.'medium/';
    $fLarge       = $folder.'large/';
    $delDataNew   = array();
    $report       = array();

    if(is_array($deleteIds))
    {
      $report['selected'] = true;
    }
    else
    {
      $report['selected'] = false;
      $report['output'] = JText::_('COM_S7DGALLERY_IMAGE_SELECTION_EMPTY');
    }

    foreach(json_decode($fileData) as $k=> $img)
      {
        if(in_array($img->id,$deleteIds))
        {
          if(JFile::delete($folder.$img->image)) {
            //Delet Thumbs
            JFile::delete($fThumbs.$img->image);
            //Delet Medium
            JFile::delete($fMedium.$img->image);
            //Delet Large
            JFile::delete($fLarge.$img->image);

            //Delete True
            $report['status'] = true;
          }
          else{
            $report['status'] = false;
            $report['output'] = JText::_('COM_S7DGALLERY_IMAGE_DELETE_FAILED');
          }
        }
      }

      echo json_encode($report);
      exit();

  }


  /***************--------------------------------
   *Images Content
  ---------------------------------***************/ 
  public function getImagesContent($folder,$image,$imgId)
  {
    $output = [];
    $output[] = '<span class="sg-img-top">';
    $output[] = '<div class="upImage" data-imageId="'.$imgId.'"></div>';
    $output[] = '<div class="sortImage"><i class="la la-arrows"></i></div>';
    $output[] = '</span>';
    $output[] = '<img src="'.$folder.$image.'">';
    $output[] = '<span class="selectImage"></span>';
    $output[] = '<span class="sg-attributes"></span>';

    return implode("",$output);
  }

  /***************--------------------------------
   *Resize
  ---------------------------------***************/ 
  public static function sImage($path,$imageOri,$size,$type=null)
  {

    if(!is_dir($path)){
      mkdir($path,0777, true);
      chmod($path, 0777);
    }

    //Extensions
    $fileName  = strtolower(pathinfo($imageOri,PATHINFO_FILENAME));

    //Tamanho da imagem
    $inSize = explode("x",strtolower($size));
    $width  = $inSize[0];
    $height = $inSize[1];

    if(!is_array($inSize)){
      $width = '400';
      $height = '200';
    }

    // Carrega a imagem a ser manipulada
    $image = wideImage::load($imageOri);

    // Redimensiona a imagem
    switch ($type) {
      case 'crop':
        $image = $image->resize($width, $height,outside);
        $image = $image->crop('center', 'center', $width, $height);
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
    $image->saveToFile($path.$fileName.'.jpg',80);


    return true;
  }

  /***************--------------------------------
   *Order
  ---------------------------------***************/ 
  public function orderImage()
  {
    $input        = JFactory::getApplication()->input;
    $model        = $this->getModel();
    $itemId       = $input->post->get('itemId','','int');
    $order        = $input->post->get('order','','array');
    $fileData     = json_decode($model-> getImages($itemId,'imagesTmp'),true);
    $orderData    = [];
    $report       = [];

    $outData = $fileData;

    foreach($order as $k=> $ids)
    {
      $ind = (int) array_search($ids,array_column($outData,'id'));
      array_push($orderData,$outData[$ind]);
    }
    
    $model->upImage($itemId,json_encode($orderData),'imagesTmp');

    $report['or'] = $itemId;

    echo json_encode($report);
    exit();
  }

  /***************--------------------------------
   *Update
  ---------------------------------***************/ 
  public function updateImage()
  {
    $input        = JFactory::getApplication()->input;
    $itemId       = $input->post->get('itemId','','int');
    $dataSet      = $input->post->get('dataSet','','string');
    $report       = [];

    // Initialiase variables.
    $db    = JFactory::getDbo();
    $query = $db->getQuery(true);
    
    // Create the base update statement.
    $query->update($db->quoteName('#__s7dgallery_albums'))
      ->set($db->quoteName('images') . ' = ' . $db->quote($dataSet))
      ->where($db->quoteName('id') . ' = ' . $db->quote($itemId));
    
    // Set the query and execute the update.
    $db->setQuery($query);
    
    try
    {
      $db->execute();
      $report['status'] = true;
    }
    catch (RuntimeException $e)
    {
      JError::raiseWarning(500, $e->getMessage());
      $report['status'] = false;
    }

    echo json_encode($report);
    exit();
  }

  /***************--------------------------------
   *Get Image Upladte
  ---------------------------------***************/ 
  public function getImageUpdate()
  {
    $input        = JFactory::getApplication()->input;
    $model        = $this->getModel();
    $itemId       = $input->post->get('itemId','','int');
    $imageId      = $input->post->get('imageId','');
    $fileData     = json_decode($model-> getImages($itemId,'imagesTmp'));
    $output       = [];
    $report       = [];

    $folder       = JUri::root(true).'/images/s7dgallery/gal-'.$itemId.'/';
    $fMedium      = $folder.'medium/';
    
    $output[] = '<div id="sg-upImage">';
    $output[] = '<form action="" method="post" class="bounceInRight animated">';
    $output[] = '<div class="sg-upImageInner">';
    foreach($fileData as $k=> $image)
    { 
      if($image->id == $imageId)
      {
        //Cover
        $cover = $image->cover == 1 ? 'checked' : null;
        $access = $image->access == 1 ? 'checked' : null;
        $output[] = '<div class="sg-upImage-header"><h3>Opções</h3><span class="upImageClose upClose"></span></div>';
        $output[] = '<div class="sg-upImage-img"><span class="sg-img-h"></span><img src="'.$fMedium.$image->image.'"/></div>';
        $output[] = '<div class="sg-upImage-form">';
        $output[] =   '<input type="hidden" name="id" value="'.$image->id.'">';
        $output[] =   '<input type="hidden" name="image" value="'.$image->image.'">';
        $output[] =   '<div class="form-group">';
        $output[] =     '<label>Título</label>';
        $output[] =     '<input type="text" name="title" value="'.$image->title.'">';
        $output[] =   '</div>';
        $output[] =   '<div class="form-group">';
        $output[] =     '<label>Subtítulo</label>';
        $output[] =     '<input type="text" name="subtitle" value="'.$image->subtitle.'">';
        $output[] =   '</div>';
        $output[] =   '<div class="form-group">';
        $output[] =     '<label>Alt</label>';
        $output[] =     '<input type="text" name="alt" value="'.$image->alt.'">';
        $output[] =   '</div>';
        $output[] =   '<div class="form-group">';
        $output[] =     '<label>Descrição</label>';
        $output[] =     '<textarea name="description">'.$image->description.'</textarea>';
        $output[] =   '</div>';
        $output[] =   '<div class="form-check">';
        $output[] =     '<input type="checkbox" id="cover" class="sg-check" name="cover" value="1" '.$cover.'>';
        $output[] =     '<label class="form-check-label" for="cover">Capa <span class="s-checked"></span></label>';
        $output[] =   '</div>';
        $output[] =   '<div class="form-check">';
        $output[] =     '<input type="checkbox" id="access" class="sg-check" name="access" value="1" '.$access.'>';
        $output[] =     '<label class="form-check-label" for="access">Restrito <span class="s-checked"></span></label>';
        $output[] =   '</div>';
        $output[] = '</div>';
      }
      
    }
    $output[] = '</div>';
    $output[] = '</form>';
    $output[] = '</div>';
    $output[] = '<div class="sg-upImage-overlay upImageClose fadeIn animated"></div>';

    $report['output'] = implode('',$output);

    echo 'nada';
    //exit();
  }

  /***************--------------------------------
   *File List
  ---------------------------------***************/
  public function fileList($path,$extensions = array())
  {
    $fileList     = scandir($path);
    $list         = [];

    foreach($fileList as $files)
    {
      $fileName       = pathinfo($files,PATHINFO_FILENAME);
      $fileExtension  = strtolower(pathinfo($files,PATHINFO_EXTENSION));
      $file           = $fileName.'.'.$fileExtension;

      if(in_array($fileExtension,$extensions))
      {
        array_push($list,$file);
      }
    }

    return $list;
  }

   /***************--------------------------------
   *Increment image
  ---------------------------------***************/ 
  public static function incrementArr($path,$formats,$fileName,$sep = null)
  {
    $in = array();
    foreach(self::fileList($path,$formats) as $nImg)
    {
      $cFileN = pathinfo($nImg,PATHINFO_FILENAME);
      $cFileE = pathinfo($nImg,PATHINFO_EXTENSION);

      $FeX = explode($fileName,$cFileN)[0];

      if($fileName == $fileName.$FeX)
      {
        array_push($in,$nImg);
      }
    }

    $increment = count($in) == 1 ? 1 : count($in);

    return $sep.$increment;
  }

}