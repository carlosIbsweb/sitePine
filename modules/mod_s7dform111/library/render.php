
<?php

/**
 * @package     
 * @subpackage  mod Wk contact
 **/

// No direct access.
defined('_JEXEC') or die;

require_once('render_edit.php');
require_once('awLogin.php');

/********
 Classe Render Form.
 Desenvolvido por Carlos (IBS WEB)
********/

class renderForm {
	public static function getDados($json,$captcha,$moduleId,$dbName = null,&$params = null){

		if(isset($_GET['awEdit']))
		{
			if(!renderFormEdit::getItem('awToken',$dbName))
			{	
				echo '<div class="alert alert-danger">Token inválido</div>';
				return false;
				exit();
			}

			//Bloqueio de Acesso
			if($params->get('awUpDblock'))
			{
				if(awLogin::getDado($params->get('awUpDName'),$dbName,$_GET['awToken']) === $params->get('awUpDValue'))
				{
					echo '<div class="alert alert-danger">Acesso bloqueado!</div>';
					return false;
					exit();
				}
			}
		}

	$list = new stdClass();

	$json = '['.$json.']';

	$orderRows = [];
	$list->orderColumns = [];
	foreach(json_decode($json) as $t){
		$list->id = $t->id;
		foreach(json_decode(json_encode($t->stages)) as $id => $r){
			foreach($r->rows as $l){
				foreach(json_decode(json_encode($t->rows)) as $rid => $rows){
					if($rid === $l){
						echo '<div class="aw-form-row row">';
						foreach($rows->columns as $c){
							
							$con = json_decode(json_encode($t->columns),true);
							$vColumn = $con[$c]['config'];
							echo '<div class="aw-column" style="width:'.$vColumn['width'].'">';
							foreach(json_decode(json_encode($t->columns)) as $cId => $columns) { 
								if($cId === $c)
								{
									$cfColumn = get_object_vars($columns->config);
									self::getForm($t->fields,$columns->fields,$cfColumn['width'],$captcha,$moduleId,$dbName);					
								}
							}
							echo '</div>';
						}
						echo '</div>';
					}
				}
				
			}
		}
	}


	return $list;
}

public static function getForm($dados,$colFields = array(),$wColumn,$captcha,$moduleId,$dbName = null){
	$render = [];
	foreach($colFields as $colField){
		foreach(json_decode(json_encode($dados)) as $fId => $field){
			if($fId === $colField){
				if(isset($_GET['awEdit']))
				{
					renderFormEdit::render($field,$wColumn,$captcha,$moduleId,$dbName);
				}else{
				self::render($field,$wColumn,$captcha,$moduleId);
				}
			}
		}
	}
}

public static function render($fields,$wColumn,$captcha,$moduleId){
	
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
	
	$awRequired = !empty($valid) ? '<span class="aw-required">*</span>' : null;
	$label 		= !empty($config['label']) && $tag != 'button' ? '<label for="'.$name.'" class="aw-form-label">'.$config['label'].$awRequired.'</label>' : null;
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

			switch ($inputType) {
				case 'radio':
				case 'checkbox':
					//Formato Inline ou Default
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

				    foreach($options as $option){
						$checked = $option['selected'] ? 'checked' : null;
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
    				$renderFields[] = '<input type="'.$iTypeS.'" class="form-control '.$className.'" '.$attr. $valid. $vType.' id="'.$name.'">';

					break;
				default:
					$inputClass = $inputType == 'file' ? 'form-control-file' : 'form-control';
    				$renderFields[] = '<input type="'.$inputType.'" class="'.$inputClass.'" '.$attr. $valid. $vType.' id="'.$name.'">';
					break;
			}
			break;
		case 'select':
			$renderFields[] = '<select class="form-control" id="exampleFormControlSelect1" '.$attr. $valid.'>';
				foreach($options as $option){
					$selected = $option['selected'] ? 'selected' : null;
					$renderFields[] = '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
				}
    		$renderFields[] ='</select>';
    		break;
    	case 'textarea':
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
  			$renderFields[] = !empty($captcha) ? '<div class="awCaptchaRe">'.$captcha.'</div>' : null;
    		$renderFields[] = '<button type="'.$buttonType.'" class="btn btn-'.$className.'" '.$attr.'>'.$options[0]['label'].'</button>';
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
}