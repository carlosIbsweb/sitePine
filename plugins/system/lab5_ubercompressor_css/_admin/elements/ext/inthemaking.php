<?php
/**
 * @author	:	Lab5 - Dennis Riegelsberger
 * @authorUrl	:	https://lab5.ch
 * @authorEmail	:	info@lab5.ch
 * @copyright	:	(C) Lab5 - Dennis Riegelsberger. All rights reserved.
 * @copyrightUrl	:	https://lab5.ch
 * @license	:	GNU General Public License version 2 or later;
 * @licenseUrl	:	https://www.gnu.org/licenses/gpl-2.0.html
 */
 
defined('JPATH_BASE') or die;
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);

jimport('joomla.form.formfield');

class JFormFieldInthemaking extends JFormField {
		
		/////////////////////////////////////////////////////
        protected $type = 'Inthemaking';
		/////////////////////////////////////////////////////
        protected function getInput() {
				 return  'This feature is planed to be realized.';
        }
}