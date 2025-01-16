jQuery(function($){
	$( document ).ready(function(){
		
		$(document).on('change','#jform_params_columns',function(){
			var format = $('#jform_params_format').find('option:selected').val();
			if(format == 'galeria'){
			var sel = $(this).find('option:selected').val();
			$('.mas').html('');
			for(i=1;i<=sel;i++){	
				$('.wkgal').append('<input type="text" name="gal'+i+'" value="">');
			}
			}
		})

		$(document).on('change','#jform_params_format',function(){ 
			sel = $(this).find('option:selected').val();

			if(sel != 'galeria'){
				$('.wkgal').remove();
			}else{
				$('.gal').append('<div class="wkgal"></div>');
			}
		})
		

		$(document).on('mouseout','.mas input',function(){
			
			var arr = {};
			var inputs = $('.mas :input').serializeArray();
			inputs.each(function(index){
				var ti = index.name;
				var v = index.value;
				arr[ti] = v;
			})
			alert(JSON.stringify(arr));
		})

		$('.wkinfo').parents('.control-label').addClass('wkinfo-label');
		
	})

})