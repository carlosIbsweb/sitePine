<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2016 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

jimport('joomla.form.formfield');

class JFormFieldGaleria extends JFormField
{
	protected $type = 'Galeria';

	protected function getInput() {
		echo '<div class="gal"></div>';
	}
}
