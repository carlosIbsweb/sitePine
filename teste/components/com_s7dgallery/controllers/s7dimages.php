<?php

/**
 * @version    CVS: 2.0.0
 * @package    Com_S7dgallery
 * @author     carlos <carlosnaluta@gmail.com>
 * @copyright  2018 carlos
 * @license    GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

// Get the current user object.
$user = JFactory::getUser();
$userId = $user->id;

/**
 * S7d Images controller class.
 *
 * @since  1.6
 */
class S7dgalleryControllerS7dimages extends S7dgalleryController
{
	public function nada()
	{
		$input  	= JFactory::getApplication()->input;
		$itemId 	= $input->post->get('itemId', '', 'int');
		$start 	= $input->post->get('countStart', '', 'int');
		$limit 	= $input->post->get('countLoader', '', 'int');
		$report 	= [];
		$output     = [];

		$folder 	= JUri::base(true).'/images/s7dgallery/gal-'.$itemId.'/';
		$fSmall 	= $folder.'small/';
		$fMedium 	= $folder.'medium/';
		$fLarge		= $folder.'large/';

		$model  	= $this->getModel('S7dimages');
		$images 	= array_slice(json_decode($model->getImages($itemId)),$start,$limit);
		$count = count(json_decode($model->getImages($itemId)));

		foreach($images as $img)
		{
    		$access = $img->access != 1 ? true : (!empty($userId) ? true : false);
    		if($access):
    			$output[] = '<a href="'.JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$itemId.'&imgId='.$img->id.'&path=large" class="test-popup-link" data-source="'.JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$itemId.'&imgId='.$img->id.'&path=large" title="'.$img->title.'">';
        		$output[] = '<img alt="'.$img->alt.'" src="'.JUri::root().'components/com_s7dgallery/image/image.php?itemId='.$itemId.'&imgId='.$img->id.'&path=large"/>';
    			$output[] = '</a>';
  			endif;
		}

		$report['image'] = implode('',$output);
		$report['start'] = $start + $limit;
		$report['count'] = $count - $start;
		$report['limit'] = $limit;

		echo json_encode($report);
		exit();
	}
}
