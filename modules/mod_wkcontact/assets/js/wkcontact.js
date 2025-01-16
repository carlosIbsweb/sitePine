/*
	D Contact
	jcarloswk@gmail.com
*/
(function ( $ ){
	$.fn.wkcontact = function( opts ){
		var wkContactDefault = $.extend({
            contactId       : "",
            loadImage: ""
        },opts);
		/*
		 *Modal Inscrição
		*/
		$( document ).on('modalform', function(){
			$('.wkcontact .wkcontactinner').css({
				"margin-top": '20px'
			});
		});
		 $( document ).on("wkcontact"+opts.contactId,function(evt,formtitle,formid){
			$( "body" ).prepend('<div class="wkcontact"><div class="wkcontactbg wkclose"></div><div class="wkcontactinner"><div class="wkcontactcontent"><span class="wkclose"><img src="'+opts.urlImage+'" alt="Close" /></span><div class="wk-container"><img src="../modules/mod_wkcontact/assets/images/wkloading.gif" alt="Loading..." /></div></div></div></div>');
			//add class animation
			$( ".wkcontactcontent" ).addClass("animated bounceInDown");
			$( ".wkcontactbg" ).addClass("animated fadeIn");
			$( document ).trigger('modalform');
			$( ".wkcontactcontent .wk-container").load(opts.url,function(){
				true;
			});
			$( document ).on("click",".wkclose",function(){
				$( ".wkcontactcontent" ).removeClass("animated bounceInDown");
				$( ".wkcontactbg" ).removeClass("animated fadeIn");
				$( ".wkcontactcontent" ).addClass("animated bounceOutUp");
				
				setTimeout(function(){
					$( ".wkcontactbg" ).addClass("animated fadeOut");
					$( "body" ).css({
						"overflow-y":"auto"
					});
					$( ".wkcontact" ).css({
						"overflow-y":"hidden"
					});
				},600);
				setTimeout(function(){
					$( ".wkcontact" ).remove();
					$( ".wkcontactbg ").remove();
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
		//Veficar se o captcha está ativo.
		var activCaptcha = $("#wkContact-"+opts.contactId).data('recaptcha');
		
		//Selecionando o tipo de contato.
		var type = $('#wkContact-'+opts.contactId).data('type');
		if(type == 'modal'){
			$( document ).on("click",".wkcontactbtnmodal", function(){
				var formtitle 	= $( this ).data('formtitle');
				var formid 		= $( this ).data('formid');
				$( document ).trigger('wkcontact'+opts.contactId,[formtitle,formid]);
				return false;
				});
		}
		if(type == 'fixed'){
			var formid =  $( "#wkContact-"+opts.contactId ).data('formid');
			$("#wkContact-"+opts.contactId+" .wk-content").html('<img src="'+opts.loadImage+'" alt="Loading..." />').fadeIn('slow');
			$( "#wkContact-"+opts.contactId+" .wk-content").load(opts.url,function(){});
		}
		//------------End modal
		$(document).on("wkcontact-form-"+opts.contactId, function(evt,idform) {
			var form = $("#wkform-"+idform),
			formData = new FormData()
        	formParams = form.serializeArray();

		    $.each(form.find('input[type="file"]'), function(i, tag) {
		      $.each($(tag)[0].files, function(i, file) {
		        formData.append(tag.name, file);
		      });
		    });

		    $.each(formParams, function(i, val) {
		      formData.append(val.name, val.value);
		    });

			$("#wkform-"+idform+" .wkstatus").html('<img src="../modules/mod_wkcontact/assets/images/loadstatus.gif" alt="Loading..." />').fadeIn('slow');
			
			$.ajax({
				url   : '/index.php?option=com_ajax&module=wkcontact&method=get&format=raw',
				type   : 'POST',
				data: formData,
				contentType: false,
        		cache: false,
        		processData:false,
				success: function (response) {
					setTimeout(function(){
						$("#wkform-"+idform+" .wkstatus").html( response ).fadeIn('slow');
						succ = $('.isWkFormEmptySucess').length

						if(succ == 1)
						{
							form.find('.wkFormContent').remove();
							form.find('.wk-submit-button').remove();
							form.find('.wkCaptcha').remove();
							animeScroll('#wkform-'+idform,1000,200)
						}

						if(activCaptcha == '1')
						{
							grecaptcha.reset();
						}
					},1200);
	
				}
			});

		});

		const animeScroll = function(el,tmp,offTop){
		  var $doc = $('html, body');
		      $doc.animate({
		        scrollTop: $(el).offset().top - offTop
		      }, tmp);
		}

		$( document ).on("submit","#wkform-"+opts.contactId,function(event){
			event.preventDefault();
			var idform = $( this ).data('formid');
			//---Required input
	        var inEmpty = $(this).find("[data-requiredwk]").filter(function() {
	            return !this.value;
	        }).get();
	        if (inEmpty.length) {
	            $(this).find("[data-requiredwk]").addClass('isEmpty');
	            $(inEmpty).addClass('invalid');
	            
	            if($(this).find(".isWkFormEmpty").length == 0 ){
	            	$(this).find(".wkstatus").html("<div class='isWkFormEmpty tada animated'>"+$('.wkrequired').data('wkrequiremen')	+"</div>");
	            }
	           
	            return false;
	        } else {
	        	$(this).find(".isWkFormEmpty").removeClass("tada").addClass("bounceOut");
	        	$(this).find(".isWkFormEmptyError").removeClass("tada").addClass("bounceOut");
	        	$(this).find(".isWkFormEmptySucess").removeClass("bouceIn").addClass("bounceOut");
	        	if($(this).find(".isWkFormEmpty").length != 0 || $(this).find(".isWkFormEmptyError").length != 0 || $(this).find(".isWkFormEmptySucess").length != 0 ){
	            	setTimeout(function(){
	        			$( document ).trigger("wkcontact-form-"+idform,[idform]);
	        		},800);
	            }else{
	            	$( document ).trigger("wkcontact-form-"+idform,[idform]);
	            }
	            	
	        }
		});
		$( document ).on("keyup",".isEmpty",function(){
			if( $( this ).val().length != 0){
				$( this ).removeClass('invalid');
			}else{
				$( this ).addClass('invalid');
			}
	        
	    });
	};
}( jQuery ));