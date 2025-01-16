/*
	D Contact
	jcarloswk@gmail.com
*/

(function ( $ ){
	$.fn.dcontact = function( ){

		/*
		 *Modal Inscrição
		*/
		$( document ).on('modalform', function(){
			$('.dinscricao .dinscricaoinner').css({
				"margin-top": '20px'
			});
		});

		$( document ).on("click",".btndinsc", function(){
			var formtitle 	= $( this ).data('formtitle');
			var formid 		= $( this ).data('formid');

			$( "body" ).prepend('<div class="dinscricao"><div class="dinscricaoinner"><div class="dinscricaocontent"><img src="../modules/mod_dinscricao/assets/image/dinsloading.gif" alt="Loading..." /></div></div></div><div class="dinscricaobg"></div>');

			//add class animation
			$( ".dinscricaocontent" ).addClass("animated bounceInDown");
			$( ".dinscricaobg" ).addClass("animated fadeIn");

			$( document ).trigger('modalform');

			$( ".dinscricaocontent").load('../modules/mod_dinscricao/tmpl/default_form.php?formid='+ formid,function(){
				$(".dinscricaocontent").prepend('<span class="dinsclose">X</span>');
				$(".dinscricaocontent").prepend('<div class="dinscricaoheader"><h2>'+ formtitle +'</h2></div>');
			});

			$( document ).on("click",".dinsclose",function(){
				$( ".dinscricaocontent" ).removeClass("animated bounceInDown");
				$( ".dinscricaobg" ).removeClass("animated fadeIn");
				$( ".dinscricaocontent" ).addClass("animated bounceOutUp");
				
				setTimeout(function(){
					$( ".dinscricaobg" ).addClass("animated fadeOut");
					$( "body" ).css({
						"overflow-y":"auto"
					});
					$( ".dinscricao" ).css({
						"overflow-y":"hidden"
					});
				},600);
				setTimeout(function(){
					$( ".dinscricao" ).remove();
					$( ".dinscricaobg ").remove();
				},1200);

				$( window ).resize(function(){
					$( document ).trigger('modalform');
				});
			});

			//remov body scroll
			$( "body" ).css({
				"overflow-y":"hidden"
			});
		});
		//------------End modal

		$(document).on('submit', '.dcontact-form', function(event) {
			var $self   = $(this);
	    	var value   = $(this).serializeArray();

			$(".dstatus").html('<img src="../modules/mod_dinscricao/assets/image/gstatus.gif" alt="Loading..." />').fadeIn('slow');

			request = {
					'option' : 'com_ajax',
					'module' : 'dinscricao',
					'data'   : value,
					'format' : 'raw'
				};
			$.ajax({
				type   : 'POST',
				data   : request,
				success: function (response) {
					setTimeout(function(){
						$(".dstatus").html( response ).fadeIn('slow');
						$(".dcategoria option:selected").data("price");
				
						if($("#redirectpagseguro").length == 1)
						{	
							$(".dstatus").remove();
							$(".dinscricaocontent").append( response );
							$(".pagprice").val($(".dcategoria option:selected").data("price"));
							$( ".dinscricaocontent #dform" ).fadeOut('slow');
							$(".dinscricaoheader").fadeOut('slow');
							
							setTimeout(function(){
								$(" #dfinalizarpag ").fadeIn('slow');
							},600);
							setTimeout(function(){
								$(".dEnC").click();
							},5000);
						}
					},1200);	
				}
			});
		return false;
		});
		
	};
}( jQuery ));