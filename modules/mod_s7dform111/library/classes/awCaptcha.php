
<?php

/**
 * @package     
 * @subpackage  mod AwForm
 **/

// No direct access.
defined('_JEXEC') or die;

/********
 Classe Aw Captcha.
 Desenvolvido por Carlos (IBS WEB)
********/

class awCaptcha {

	public function __construct($g = null,$jResult = true,$idModule = '',$gValue = null,$moduleId = array()){
		return self::getAwCaptcha($g = null,$jResult = true,$idModule = '',$gValue = null,$moduleId = array());
	}

	public static function getAwCaptcha($g = null,$jResult = true,$idModule = '',$gValue = null,$moduleId,&$params)
	{	
		$aritim = explode(',',$params->get('aritim'));
		shuffle($aritim);
		$aritim = $aritim[0];

		$numeric = array();
		$variation = $params->get('vAdd');
		$variation = $aritim == '*' ? $params->get('vMul') : (
			$aritim == '-' ? $params->get('vSub') : $variation
		);

		$variation = !empty($variation) && is_numeric($variation) && $variation > 1 ? $variation : false;

		if($variation === false){
			echo self::awMessages('<strong>Error Captcha!</strong> Variações não podem ser menores que 2','danger');
			return false;
			exit();
		}

		$cAlign = $params->get('cAlign') == 'right' ? 'style="float:right"' : ($params->get('cAlign') == 'center' ? 'style="margin:0 auto"' : 'style="float:left"');

		$awRes = '<input type="text" name="awCaptcha-'.$moduleId.'" autocomplete="off">';
		$output = [];
		$ofResult = [];
		$diffResult = [];

		for($i=1;$i<= $variation; $i++)
		{
			array_push($numeric,$i);
		}

		$calc = array_rand($numeric,2);
		
		if($aritim == '-')
		{
			$keyMax = max(array_values($calc));
			$keyMin = min(array_values($calc));
			$n1 = '<span class="aw-res">'.$numeric[$keyMax].'</span>';
			$n2 = '<span class="aw-res">'.$numeric[$keyMin].'</span>';

			$sum = $numeric[$keyMax].$aritim.$numeric[$keyMin];
			$aritim = '<span class="aw-aritim">'.$aritim.'</span>';
			$output[0] = $n1.$aritim.$awRes;
			$output[1] = $awRes.$aritim.$n2;
			$output[2] = $n1.$aritim.$n2.' <span class="aw-sres">=</span> '.$awRes;
		}else{
			$n1 = '<span class="aw-res">'.$numeric[$calc[0]].'</span>';
			$n2 = '<span class="aw-res">'.$numeric[$calc[1]].'</span>';
		
			$sum = $numeric[$calc[0]].$aritim.$numeric[$calc[1]];
			$aritim = str_replace("*","x",'<span class="aw-aritim">'.$aritim.'</span>');
			$output[0] = $n2.$aritim.$awRes;
			$output[1] = $awRes.$aritim.$n1;
			$output[2] = $n1.$aritim.$n2.' <span class="aw-sres">=</span> '.$awRes;
		}
		
		/********
		 * Resultado gerado
		********/
		eval("\$result = ".$sum.";");
		$rResult = array("result"=> $result);

		$rResultDiff = array(
			$calc[0] => $numeric[$calc[0]],
			$calc[1] => $numeric[$calc[1]],
			'result' => $result
		); 

		$outRand = array_rand($output);
		array_push($ofResult,array($calc[0],$calc[1],'result'));

		if($aritim == '-')
		{
			switch ($outRand) {
				case 0:
					array_push($diffResult,array($keyMax,'result'));
					break;
				case 1:
					array_push($diffResult,array($keyMin,'result'));
					break;
				case 2:
					array_push($diffResult,array($keyMax,$keyMin));
					break;
			}
		}
		else
		{
			switch ($outRand)
			{
				case 0:
					array_push($diffResult,array($calc[1],'result'));
					break;
				case 1:
					array_push($diffResult,array($calc[0],'result'));
					break;
				case 2:
					array_push($diffResult,array($calc[0],$calc[1]));
					break;
			}
		}

		$rDiff = array_diff($ofResult[0],$diffResult[0]);
		$rDiff = array_values($rDiff);
		$rDiff = $rResultDiff[$rDiff[0]];

		/********
		 * Captcha Gerado.
		********/
		$output = $outRand == 2 ? $output[$outRand] : $output[$outRand] .' <span class="aw-sres">=</span> '.'<span class="aw-result">'.$result.'</span>';

		$report = '<div class="aw-form-row row">';
		$report = '<label><b>Resolva os cálculos do captcha</b></label>';
		$report .= '<div id="aw-captcha" '.$cAlign.'>';
		$report .= $output;
		$report .= '<i class="aw-refresh fa fa-refresh" style="font-size:24px"></i>';
		$report .= '</div>';
		$report .= '<input type="hidden" name="awCaptchaRest" value="1">';
		$report .= '</div>';

		if($g == $_SESSION['awCaptcha-'.$moduleId] && isset($_SESSION['awCaptcha-'.$moduleId]) && $g != '' && $g != null)
		{
			return true;
			unset($_SESSION['awCaptcha-'.$moduleId]);
			unset($_SESSION['report-'.$moduleId]);
		}
		else
		{
			if($g != $_SESSION['awCaptcha-'.$moduleId] and $g != '' && isset($_SESSION['awCaptcha-'.$moduleId]))
			{
				echo self::awMessages('<strong>Captcha</strong> inválido','danger');
				return false;
				return $_SESSION['report-'.$moduleId];
			}
			else
			{
				if(empty($g) && $g == '' && isset($_SESSION['awCaptcha-'.$moduleId]) && $gValue != null)
				{	
					echo self::awMessages('Solução do <strong>Captcha</strong> está vazio','warning');
					return false;
					return $_SESSION['report-'.$moduleId];
				}
				elseif(!isset($_SESSION['awCaptcha-'.$moduleId]) && $gValue != null)
				{
					echo self::awMessages('Desculpe, a sessão do <strong>Captcha</strong> expirou','info');
					return false;
					$_SESSION['awCaptcha-'.$moduleId] = $rDiff;
					$_SESSION['report-'.$moduleId] = $report;
					return $_SESSION['report-'.$moduleId];
				}
				else
				{
					$_SESSION['awCaptcha-'.$moduleId] = $rDiff;
					$_SESSION['report-'.$moduleId] = $report;
					
					if($jResult){
						return $_SESSION['report-'.$moduleId];
						return false;
					}
					else
					{
						return false;
					}	
				}
				return false;
			}
			return false;
		}
		return false;
	}

	public static function awMessages($msg,$alert)
	{
		$alert = !empty($alert) ? $alert : 'success'; 
		$message = [];
		$message[] = '<div class="alert alert-'.$alert.'">';
  		$message[] = $msg;
		$message[] = '</div>';

		return implode('',$message);
	}
	
}