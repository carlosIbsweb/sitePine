/*
	D Contact
	jcarloswk@gmail.com
*/

(function ( $ ){
	$.fn.dcontact = function( mod ){
		if ( mod === "email" ){
			$('.dmail').css({
				"display": "block"
			});
			$('.dfone').css({
				"display":"none"
			});

			$('#dfone').val('').removeAttr("required");
			$('#dmail').attr("required","true");
		}

		if ( mod === "telefone" ){
			$('.dfone').css({
				"display": "block"
			});
			$('.dmail').css({
				"display":"none"
			});
			$('#dmail').val('').removeAttr("required");
			$('#dfone').attr("required", "true");
		}
	}
}( jQuery ));

jQuery(function($) {
	$(document).on('submit', '.dcontact-form', function(event) {
		var $self   = $(this);
    	var value   = $(this).serializeArray();

		$(".dstatus").html('<img src="../modules/mod_dcontact/assets/image/dicon-loading.gif" alt="Loading..." />').fadeIn('slow');

		request = {
				'option' : 'com_ajax',
				'module' : 'dcontact',
				'data'   : value,
				'format' : 'raw'
			};
		$.ajax({
			type   : 'POST',
			data   : request,
			success: function (response) {
				setTimeout(function(){
					$(".dstatus").html( response ).fadeIn('slow');
				},1200);
				
			}
		});
		return false;
	});
});
