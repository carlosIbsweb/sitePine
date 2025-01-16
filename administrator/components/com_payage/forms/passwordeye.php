<?php
/********************************************************************
Product    : Multiple Products
Date       : 15 March 2021
Copyright  : Les Arbres Design 2010-2021
Contact    : https://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class JFormFieldPasswordEye extends JFormField
{
protected $type = 'PasswordEye';

public function getInput()
{
	$name     = ' name="'.$this->name.'"';
	$id       = ' id="'.$this->name.'"';
    $eye_id   = $this->name.'_eye';
	$size     = !empty($this->size) ? ' size="' . $this->size . '"' : '';
	$required = $this->required ? ' required aria-required="true"' : '';
	$value    = ' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"';
	$onclick  = "if (document.getElementById('".$this->name."').type == 'text') 
			{document.getElementById('".$this->name."').type = 'password'; document.getElementById('".$eye_id."').style.color = 'black';}
		else 
			{document.getElementById('".$this->name."').type = 'text'; document.getElementById('".$eye_id."').style.color = 'blue';}";
    $eye  = ' <span id="'.$eye_id.'" class="icon-eye" onclick="'.$onclick.'" style="cursor:pointer;margin-left:3px;"></span>';

    if (empty($this->class))
        $class = '';
    else
        $class = ' class="form-control '.$this->class.'"';

	$style = ' style="display:inline-block"';
	return '<input type="password"'.$style.$class.$name.$id.$value.$size.$required.'" /> '.$eye;
}

}