<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#ajax_form').submit(function(){
			var dados = jQuery( this ).serialize();

			jQuery.ajax({
				type: "POST",
				url: "http://depaula.ibsweb.webfactional.com/dren",
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

<form method="post" action="" id="ajax_form" name="videoid" style="display:none">
	<label><input type="hidden" name="idvideo" value="<?= $vVideo;?>" /></label>
	<label><input type="hidden" name="idvideoN" value="<?= $vDvideo;?>" /></label>
	<label><input type="submit" name="enviar" value="Enviar" class="enviar" /></label>
</form>