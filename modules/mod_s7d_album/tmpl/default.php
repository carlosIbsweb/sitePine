<?php


defined('_JEXEC') or die;

$folder     = JUri::base(true).'/images/s7dgallery/gal-'.$params->get('said').'/';

$js = "
jQuery.noConflict();
	jQuery(function(a){
		a(document).santSlide({
			folder: '".$folder."',
			json:'".$items[0]->images."',
			columns:'".$params->get('columns')."',
			random:'".$params->get('random')."',
			limit:'".$params->get('limit')."',
			slideLines:'".$params->get('limit')."',
			sliderId:'#svSlider-".$module->id."'
		})

		a(document).ready(function() {
	a('.zoom-gallery').magnificPopup({
		delegate: 'a',
		tLoading: 'Carregando...',

		type: 'image',
		closeOnContentClick: false,
		closeBtnInside: false,
		mainClass: 'mfp-with-zoom mfp-img-mobile',
		image: {
			verticalFit: true,
			titleSrc: function(item) {
				return false;
			}
		},
		gallery: {
			enabled: true,
			tCounter:'%curr% de %total%'
		},
		zoom: {
			enabled: true,
			tCounter:'a',
			duration: 300, // don't foget to change the duration also in CSS
			opener: function(element) {
				return element.find('img');
			}
		}
		
	});
	});

	
});

";
$doc->addScriptDeclaration($js);


?>


<div class="santSlide zoom-gallery" id="svSlider-<?= $module->id;?>">
	<?php if(count(json_decode($items[0]->images,true)) == 0 ):?>
		<div class="alert alert-warning" role="alert">Nenhuma imagem</div>
	<?php endif;?>
</div>


