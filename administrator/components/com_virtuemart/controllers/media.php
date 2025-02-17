<?php
/**
*
* Media controller
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers
* @link https://virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: media.php 10585 2022-02-07 13:50:28Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Product Controller
 *
 * @package    VirtueMart
 * @author Max Milbers
 */
class VirtuemartControllerMedia extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */
	function __construct() {
		vmLanguage::loadJLang('com_virtuemart_media');
		parent::__construct('virtuemart_media_id');

	}

	function save($data = 0){

		$fileModel = VmModel::getModel('media');

		//Now we try to determine to which this media should be long to
		$data = array_merge(vRequest::getRequest(),vRequest::get('media'));

		if(!empty($data['file_description'])){
			$data['file_description'] = JComponentHelper::filterText($data['file_description']); //vRequest::filter(); vRequest::getHtml('file_description','');
		}

		/*$data['media_action'] = vRequest::getCmd('media[media_action]');
		$data['media_attributes'] = vRequest::getCmd('media[media_attributes]');
		$data['file_type'] = vRequest::getCmd('media[file_type]');*/
		if(empty($data['file_type'])){
			$data['file_type'] = $data['media_attributes'];
		}

		if ($id = $fileModel->store($data)) {
			vmInfo('COM_VIRTUEMART_FILE_SAVED_SUCCESS');
		}

		$cmd = vRequest::getCmd('task');
		if($cmd == 'apply'){
			$redirection = 'index.php?option=com_virtuemart&view=media&task=edit&virtuemart_media_id='.$id;
		} else {
			$redirection = 'index.php?option=com_virtuemart&view=media';
		}

		$this->setRedirect($redirection);
	}

	function synchronizeMedia(){

		if(vmAccess::manager('media')){

			$configPaths = array('assets_general_path','media_category_path','media_product_path','media_manufacturer_path','media_vendor_path');
			foreach($configPaths as $path){
				$this -> renameFileExtension(VMPATH_ROOT .'/'. VmConfig::get($path) );
			}

			$migrator = new Migrator();
			$result = $migrator->portMedia();
			vmInfo($result);

			$this->setRedirect($this->redirectPath);
		} else {
			vmWarn ('Forget IT');
			$this->setRedirect('index.php?option=com_virtuemart');
		}

	}

	function renameFileExtension($path){

		$results = array();
		$path = vRequest::filterPath($path);
		$handler = opendir($path);

		// open directory and walk through the filenames
		while ($file = readdir($handler)) {
			// if file isn't this directory or its parent, add it to the results
			if ($file != "." && $file != "..") {
				if(preg_match('/JPEG$/', $file)) {
					$results['jpeg'][] = $file;
				} else if(preg_match('/JPG$/', $file)) {
					$results['jpg'][] = $file;
				} else if(preg_match('/PNG$/', $file)) {
					$results['png'][] = $file;
				} else if(preg_match('/GIF$/', $file)) {
					$results['gif'][] = $file;
				}
			}
		}

		foreach($results as $filetype => $files){
			foreach($files as $file){
				$new = JFile::stripExt($file);
				if(!JFile::exists($file)){
					$succ = rename ($path.$file,$path.$new.'.'.$filetype);
				}
			}
		}

	}

	function deleteFiles(){

		vRequest::vmCheckToken();

		$ids = vRequest::getVar($this->_cidName, vRequest::getInt('cid', array() ));

		$type = 'notice';
		if(count($ids) < 1) {
			vmInfo('COM_VIRTUEMART_SELECT_ITEM_TO_DELETE');

		} else {
			$model = $this->getModel($this->_cname);
			$ret = $model->removeFiles($ids);

			if($ret==false) {
				vmWarn('COM_VIRTUEMART_STRING_COULD_NOT_BE_DELETED',$this->mainLangKey);
			} else {
				vmInfo('COM_VIRTUEMART_STRING_DELETED',$this->mainLangKey);
			}
		}

		$this->setRedirect($this->redirectPath);
	}


}
// pure php no closing tag
