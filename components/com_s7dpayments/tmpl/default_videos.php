<?php
/**
 * @version     1.0.0
 * @package     com_s7dpayments
 * @copyright   Copyright (C) 2016. Todos os direitos reservados.
 * @license     GNU General Public License versão 2 ou posterior; consulte o arquivo License. txt
 * @author      Carlos <carlosnaluta@gmail.com> - http://site7dias.com.br
 */

// No direct access
defined('_JEXEC') or die;

$idVideo = $_SESSION['idVideo'] = md5(uniqid(rand(), true));

foreach(s7dPayments::getItens() as $items):
	$videoId = $items->videoslink;
endforeach;

if(is_array(json_decode($videoId,true))):
	foreach(json_decode($videoId,JSON_UNESCAPED_UNICODE) as $k => $videos):
		if($videos == $_GET['video']):
			$videoV = $videos;
		endif;
	endforeach;
endif;

echo $videoV;

$lista = array();
foreach(json_decode($videoId) as $k => $names):
	if($names == $_GET['video']):
		$vVideo = $names;
		$vDvideo = $videoV.'_dpVs'.$_SESSION['idVideo'];
		$_SESSION['exVideo'] = $videos;
	$lista[$k] = $videoV.'_dpVs'.$_SESSION['idVideo'];
	else:
	$lista[$k] = $names;
	endif;

endforeach;


$resVideo =  trim(json_encode($lista));

s7dPayments::setItens($resVideo,$_GET['courseId']);

?>

	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#ajax_form').submit(function(){
			var dados = jQuery( this ).serialize();

			jQuery.ajax({
				type: "POST",
				url: "http://depaula.ibsweb.webfactional.com/dren.php",
				data: dados,
				success: function( data )
				{
					alert( data );
				}
			});
			
			return false;
		});
	});

	$(document).ready(function(){
   	$('.enviar').click();
	});

	$(document).ready(function(){
   	$('#ajax_form').remove();
	});
	</script>
</head>

	<form method="post" action="" id="ajax_form" name="videoid" style="display:none">
		<label><input type="hidden" name="idvideo" value="<?= $vVideo;?>" /></label>
		<label><input type="hidden" name="idvideoN" value="<?= $vDvideo;?>" /></label>
		<label><input type="submit" name="enviar" value="Enviar" class="enviar" /></label>
	</form>

<iframe src="http://depaula.ibsweb.webfactional.com/?url=<?= $_SESSION['exVideo'];?>.mp4" frameborder="0" allowfullscreen=""></iframe>