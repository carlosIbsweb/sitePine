
<?php

/**
 * @package     
 * @subpackage  mod Wk contact
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe Render Form.
 Desenvolvido por Carlos (IBS WEB)
********/

class renderFormEdit {

public static function render($fields,$wColumn,$captcha,$moduleId,&$dbName){
	
	//Vars
	parse_str(http_build_query(json_decode(json_encode($fields),true)));

	$renderFields = [];
	$renderForm = [];

	$wColumn 	= 'width: '.$wColumn;
	$inputType 	= $attrs['type'];
	$name 		= $attrs['name'];
	$format     = $attrs['format']; //Formato checkbox e rádio
	$aHeight    = $attrs['autoHeight']; //Auto Height Text Area
	$hTag       = $attrs['tag']; //Headers
	$iTText     = $attrs['input-type']; //Input Type Text
	$className  = $attrs['className'];

	$label 		= !empty($config['label']) && $tag != 'button' ? '<label for="'.$name.'" class="aw-form-label">'.$config['label'].'</label>' : null;
	$required 	= !empty($attrs['required']) ? 1 : null;


	/********************************************
	 * Validações
	********************************************/

	//valid
	if(is_array($attrs['valid']))
	{
		foreach($attrs['valid'] as $valid)
		{
			if($valid['selected'])
			{
				$valid = $valid['value'];
			}else{
				$valid = 0;
			}
		}
	}

	$valid = !empty($valid) ? 'valid="true" autocomplete="off"' : null;

	//Tipo de validação
	if(is_array($attrs['valid-type']))
	{
		foreach($attrs['valid-type'] as $validType)
		{
			if($validType['selected'])
			{
				$vType = $validType['value'];
			}
		}
	}

	$vType = !empty($vType) && $inputType == 'text' ? 'valid-type="'.$vType.'"' : null;

	/****************
	 Exibir Label
	****************/

	//Tipo de validação
	if(is_array($attrs['exLabel']))
	{
		foreach($attrs['exLabel'] as $exLabel)
		{
			if($exLabel['selected'])
			{
				$vLabel = $exLabel['value'];
			}
		}
	}

	$label = $vLabel === '1' || $vLabel == '' ? $label : null;

	unset($attrs['className'],$attrs['required'],$attrs['type'],$attrs['valid'],$attrs['valid-type'],$attrs['format'],$attrs['exLabel'],$attrs['autoHeight'],$attrs['tag'],$attrs['input-type']);
	$attr = [];

	if(is_array($attrs))
	{
		foreach($attrs as $k=> $att)
		{
			if($att != '' && !empty($att)){
				$bAttr = $k . '= "'.$att.'"';
				array_push($attr,$bAttr);
			}
		}
	}

	$attr = implode(' ',$attr);

	switch ($tag) {
		case 'input':

		//Tirando campos que não estejam no banco de dados.
		if(!in_array($name,awLogin::getDDb($dbName,$_GET['awToken']))){
			return false;
		}

			switch ($inputType) {
				case 'radio':
				case 'checkbox':
					//Tipo de validação
					if(is_array($format))
					{
						foreach($format as $form)
						{
							if($form['selected'])
							{
								$sFormat = $form['value'];
							}
						}
					}

					//Classe Formato Inline
					$classCheck = $sFormat != 'inline' ? null : 'class="'.$inputType.'-inline"';
					$checks = explode(", ",self::getItem(str_replace(['[',']'],['',''],$name),$dbName));

				    foreach($options as $option){

				    	if(in_array($option['value'], $checks))
				    	{
				    		$checked = $option['value'];
				    	}
				    	
				    	$checked = $checked == $option['value'] ? ' checked ' : null;

						$renderFields[] = $sFormat != 'inline' ? '<div class="'.$inputType.'">' : null;
						$renderFields[] = '<label '.$classCheck.'>';
				    	$renderFields[] = '<input class="form-check-input" type="'.$inputType.'"  id="gridRadios1" value="'.$option['value'].'" '.$attr.' '.$checked. $valid.'>';
				    	$renderFields[] =  $option['label'];
				    	$renderFields[] = '</label>';
				    	$renderFields[] = $sFormat != 'inline' ? '</div>' : null;
					}
					break;
				case 'text':
					//Formato Inline ou Default
					if(is_array($iTText))
					{
						foreach($iTText as $iType)
						{
							if($iType['selected'])
							{
								$iTypeS = $iType['value'];
							}
						}
					}

					$iTypeS = empty($iTypeS) || !isset($iTypeS) ? 'text' : $iTypeS;
    $renderFields[] = '<input type="'.$iTypeS.'" class="form-control '.$className.'" '.$attr. $valid. $vType.' id="'.$name.'" value="'.self::getItem($name,$dbName).'" >';

					break;
				default:
					$inputClass = $inputType == 'file' ? 'form-control-file' : 'form-control';
    				$renderFields[] = '<input type="'.$inputType.'" class="'.$inputClass.'" '.$attr. $valid. $vType.' id="'.$name.'" value="'.self::getItem($name,$dbName).'">';
					break;
			}
			break;
		case 'select':
			//Tirando campos que não estejam no banco de dados.
			if(!in_array($name,awLogin::getDDb($dbName,$_GET['awToken']))){
				return false;
			}
			$renderFields[] = '<select class="form-control" id="exampleFormControlSelect1" '.$attr. $valid.'>';
				foreach($options as $option){
					$selected = self::getItem($name,$dbName) == $option['value'] ? ' selected ' : null;
					$renderFields[] = '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
				}
    		$renderFields[] ='</select>';
    		break;
    	case 'textarea':
    		//Tirando campos que não estejam no banco de dados.
			if(!in_array($name,awLogin::getDDb($dbName,$_GET['awToken']))){
				return false;
			}
    		//Auto Height
			if(is_array($aHeight))
			{
				foreach($aHeight as $aH)
				{
					if($aH['selected'])
					{
						$aHeight = $aH['value'];
					}else{
						$aHeight = 0;
					}
				}
			}

			$aHeight = !empty($aHeight) ? 'auto-height="true"' : null;

    		$renderFields[] = ' <textarea class="form-control" id="'.$attrs['name'].'" '.$attr. $aHeight .$valid.'>';
    		$renderFields[] = self::getItem($name,$dbName);
    		$renderFields[] = '</textarea>';
    		break;
    	case 'button':

    		foreach($options[0]['type'] as $option)
    		{
    			if($option['selected']){
    				$buttonType = $option['value'];
    			}
    		}

    		foreach($options[0]['className'] as $option)
    		{
    			if($option['selected']){
    				$className = $option['value'];
    			}
    		}
  			//$renderFields[] = '<input type="text" name="moduleId" value="'.$moduleId.'" />';
  			//$renderFields[] = !empty($captcha) ? '<div class="awCaptchaRe">'.$captcha.'</div>' : null;
  			$renderFields[] = '<input type="hidden" name="'.$_SESSION['awEditToken'].'" value="'.$_GET['awToken'].'">';
  			if($buttonType == 'submit')
  			{
  				$renderFields[] = '<button type="'.$buttonType.'" class="btn btn-'.$className. ' aw-edit" '.$attr.'>Atualizar</button>';
  			}
    		
    		break;
    	case 'h1':
    		foreach($hTag as $hR)
    		{
    			if(isset($hR['selected']))
    			{
    				if($hR['selected'])
    				{
    					$hRt = $hR['value'];
    					$hRl = $hR['label'];
    				}
    			}
    		}

    		$hRt = isset($hRt) ? $hRt : 'h1';

    		$renderFields[] = '<'.$hRt.'>'.$content.'</'.$hRt.'>';
    		break;
		
		default:
			# code...
			break;
	}

	//Removendo label.
	$label = $tag != 'h1' ? $label : null;

	$renderForm[] = '<div class="form-group">';
	$renderForm[] = $label;
	$renderForm[] = !empty($valid) ? '<div class="awValidMsg">' : null;
	$renderForm[] = implode('',$renderFields);
	$renderForm[] = '</div>';
	$renderForm[] = !empty($valid) ? '</div>' : null; 

	echo implode('',$renderForm);

}

public static function getItem($name,$dbName)
    {
    	$awToken = $_GET['awToken'];
    	if(!empty($name) && !empty($dbName))
    	{
        	// Build the query for the table list.
        	$db = JFactory::getDbo();
        	$db->setQuery(
            	'SELECT '.$name
            	. ' FROM '.$dbName
            	. ' WHERE awToken = ' . $db->quote($awToken)
        	);

        	try {
        		$result = $db->loadResult();
        	} catch (Exception $e) {
        		echo $e->getMessage();
       		}
        
        return  trim($result);
        }
    
        
    }
}
