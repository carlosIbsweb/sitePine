(function ( $ ){
	$.fn.S7dPayments = function( opts ){

	var S7dPaymentsDefault = $.extend({
        contactId  : "",
        modalId    : "",
        school: ""
    },opts);

	var modalForm = $('#pineAddCri .modal-body form');
				
		/********************
		 //////Adicionar Crianças\\\\\\\\\\\
		********************/
		$( document ).on('click','.bTCmais',function(){
			
		var id 	= ID(),
		defaultForm	= 	
			'<div class="formCri" id="'+id+'">'+
				'<div class="form-group buscName">'+
					'<label for="nome-crianca" class="col-form-label">Nome da Criança</label>'+
					'<input type="hidden" class="form-control" name="criancas['+id+'][nome-label]" id="label-crianca" value="Nome da Criança">'+
					'<input required type="text" class="form-control pPrima inputPaym" name="criancas['+id+'][nome]" id="nome-crianca" autocomplete="off">'+
					'<div class="payBuscCri" style="display:none"></div>'+
				'</div>'+
				'<div class="form-group">'+
				 	'<label for="data-nascimento" class="col-form-label">Data de Nascimento</label>'+
				 	'<input type="hidden" class="form-control" name="criancas['+id+'][nascimento-label]" id="label-nascimento" value="Data de Nascimento">'+
				 	'<input required type="text" class="form-control inputPaym" name="criancas['+id+'][nascimento]" data-mask="99/99/9999" id="data-nascimento" autocomplete="off">'+
				'</div>'+
				'<div class="form-group">'+
				 	'<label for="nome-escola" class="col-form-label">Nome da Escola</label>'+
				 	'<input type="hidden" class="form-control" name="criancas['+id+'][escola-label]" id="label-escola" value="Nome da Escola">'+
				 	'<select required class="form-control inputPaym" name="criancas['+id+'][escola]" id="nome-escola">'+
				 		opts.school	
					+'</select>'+
				'</div>'+
				'<div class="form-group">'+
				 	'<label for="medicamento" class="col-form-label">A criança toma algum medicamento ou apresenta alguma condição de saúde que a impeça de realizar atividades físicas apropriadas para sua faixa etária?</label>'+
				 	'<input type="hidden" class="form-control" name="criancas['+id+'][medicamento-label]" id="label-medicamento" value="A criança toma algum medicamento ou apresenta alguma condição de saúde que a impeça de realizar atividades físicas apropriadas para sua faixa etária?">'+
				 	'<input required type="text" class="form-control inputPaym" name="criancas['+id+'][medicamento]" id="medicamento" autocomplete="off">'+
				'</div>'+
				'<div class="form-group">'+
				 	'<label for="alergia" class="col-form-label">A criança apresenta alguma alergia ou intolerância a algum medicamento ou alimento?</label>'+
				 	'<input type="hidden" class="form-control" name="criancas['+id+'][alergia-label]" id="label-alergia" value="A criança apresenta alguma alergia ou intolerância a algum medicamento ou alimento?">'+
				 	'<input required type="text" class="form-control inputPaym" name="criancas['+id+'][alergia]" id="alergia" autocomplete="off">'+
				'</div>'+
				'<div class="form-group">'+
				 	'<label for="autorizada" class="col-form-label">Na eventualidade de quedas, arranhões ou mal estar, a enfermeira está autorizada a administrar os primeiros socorros e administrar os medicamentos básicos?</label>'+
				 	'<input type="hidden" class="form-control" name="criancas['+id+'][autorizada-label]" id="label-autorizada" value="Na eventualidade de quedas, arranhões ou mal estar, a enfermeira está autorizada a administrar os primeiros socorros e administrar os medicamentos básicos?">'+
				 	'<label class="radio-inline"><input required type="radio" name="criancas['+id+'][autorizada]" value="Sim" id="autorizada-sim" class="autorizada inputPaym">Sim</label>'+
					'<label class="radio-inline"><input required type="radio" name="criancas['+id+'][autorizada]" value="Não" id="autorizada-nao" class="autorizada inputPaym">Não</label>'+
				'</div>'+
				'<div class="modal-footer">'+
					'<input type="hidden" class="piPayOk" data-dismiss="modal">'+
        			'<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>'+
        			'<input type="submit" class="btn btn-primary pineSaveCri" value="Adicionar">'+
      			'</div>'+
      		'</div>'
		;

		modalForm.html(defaultForm);

		/**
		 Bottom salvar.
		**/
		$( '.pineSaveCri' ).html('Adicionar');

		$('#catid').attr('name','criancas['+id+'][catid]');

		$( document ).awMask();

		});
	
		$( document ).on('submit','#formCri',function( event ){
			event.preventDefault();
			var createdForm   = $(this).find('.formCri');
				valName 	  = createdForm.find('.pPrima').val();
				editName 	  = $('.criancasMais').find('.criLinks').find('#citem'+createdForm.attr('id'));
				createdFormId = $('.cCriHidden').find('#'+createdForm.attr('id'));
				
				createdForm.find('.piPayOk').click();
				createdForm.find('.payBuscCri').remove();

				if(editName.length == 0)
				{
					$('.criancasMais').find('ul.criLinks').append('<li id="citem'+createdForm.attr('id')+'"><span class="pRemov">x</span><a href="#" class="cCriEdit" id="cCriEdit'+createdForm.attr('id')+'" data-id="'+createdForm.attr('id')+'" data-toggle="modal" data-target="#pineAddCri">'+valName+'</a></li>');
				}
				else
				{
					editName.replaceWith('<li id="citem'+createdForm.attr('id')+'"><span class="pRemov">x</span><a href="#" class="cCriEdit" id="cCriEdit'+createdForm.attr('id')+'" data-id="'+createdForm.attr('id')+'" data-toggle="modal" data-target="#pineAddCri">'+valName+'</a><li>');
				}
				
				createdForm.appendTo('.cCriHidden');
				createdFormId.replaceWith(createdForm);

				//Valor do preço
				/*priceHol = $('.lPriceVHidden').val();*/
				qntCri 	 = $('.cCriEdit').length;

				/*vPrice = eval(priceHol.replace(",",".")) * qntCri;

				$('.lPriceV').html(number_format(vPrice, 2, ',', '.'));

				/*Bt Salvar*/
				$('.lBtnComprar').val('Salvar e Comprar');

				/*Quantidade*/
				$('.cQnt').html(qntCri);

		});

		$( document ).on('click','.cCriEdit',function(){
			var editLoadFormId = $('.cCriHidden').find('#'+$(this).data('id'));
				modalForm.html(editLoadFormId.clone());

				/**
				 Bottom salvar.
				**/
				$( '.pineSaveCri' ).val('Salvar');
		});

		//Remove
		$( document ).on('click','.pRemov',function(){
			var removeEditLink 	= $(this).parent('li'),
				removeEditId 	= removeEditLink.find('a').data('id');

			if(confirm('Tem certeza que deseja excluir?')){

				//Valor do preço
				//priceHol = $('.lPriceVHidden').val();
				//priceTo  = $('.lPriceV').text();
				

				//vPrice = priceTo.replace(".","").replace(",",".") - eval(priceHol);
				//$('.lPriceV').html(number_format(vPrice, 2, ',', '.'));
				//Remove Link
				removeEditLink.remove();
				//remove formId.
				$('.formCri#'+removeEditId).remove();

				qntCri 	 = $('.cCriEdit').length;
				/*Bt Salvar*/
				$('.lBtnComprar').val('Salvar e Comprar');
				/*Quantidade*/
				$('.cQnt').html(qntCri);
			}
			
		});

		/*Submit Form*/
			$( document ).on('submit','#dpFormStore',function(){
				var fQncri = $('.cCriEdit').length;

				if(fQncri == 0){
					alert('Ingresso deve conter pelo menos uma criança!');
					return false;
				}
			});

		/**********
		 Buscar Nomes
		**********/
		$( '#formCri' ).on('keyup','#nome-crianca',function(event){
			event.preventDefault();
			var peg = $( this ).val();
			jQuery.ajax({
			  url: '/components/com_s7dpayments/tmpl/cri.php',
			  type: 'POST',
			  data: {nomes: peg},
			  success: function( response ) {
			  	var data  = $.parseJSON(response);

			  	nLin = '';
			  	$.each(data.nomes,function(ind,item){
			  		nLin += '<a href="#" data-id="'+item.id+'">'+item.nome+'</a>';
			  		$( '.buscName' ).find('.payBuscCri').show().empty().append( nLin );
			  	});

			  	if(peg == ""){
			  		$( '.buscName' ).find('.payBuscCri').hide().empty();
			  	}else{
			  	 	$( '.buscName' ).find('.payBuscCri').show().html( nLin );
			    }
			  	
			  },
			  error: function(xhr, textStatus, errorThrown) {
			    //called when there is an error
			  }
			});
		});


		/**********
		 Inserindo dados
		**********/
		$( document ).on('click','.payBuscCri a',function( event ){
			event.preventDefault();
			var pId = $( this ).data('id');
			var form = $( this ).parents('.formCri');
			var busc = $( this ).parent('.payBuscCri');

			form.attr("id",pId);

			jQuery.ajax({
			  url: '/components/com_s7dpayments/tmpl/cri.php',
			  type: 'POST',
			  data: {dadosId: pId},

			  before: function() {
			  	busc.html('/components/com_s7dpayments/assets/images/busca-loading.gif');
			  },
			  success: function( response ) {
			  	var data  = $.parseJSON(response);

			  	$.each(data.dados,function(ind,item){
			  	   form.find('#nome-crianca').val(item.nome);
			  	   form.find('#nome-crianca').attr('name','criancas['+pId+'][nome]');
			  	   form.find('#label-crianca').attr('name','criancas['+pId+'][nome-label]');

			  	   form.find('#data-nascimento').val(item.nascimento);
			  	   form.find('#data-nascimento').attr('name','criancas['+pId+'][nascimento]');
			  	   form.find('#label-nascimento').attr('name','criancas['+pId+'][nascimento-label]');

			  	   //form.find('#nome-escola option').val(item.escola);
			  	   form.find('#nome-escola option[value="'+item.escola+'"]').attr("selected","selected");
			  	   form.find('#nome-escola').attr('name','criancas['+pId+'][escola]');
			  	   form.find('#label-escola').attr('name','criancas['+pId+'][escola-label]');

			  	   form.find('#medicamento').val(item.medicamento);
			  	   form.find('#medicamento').attr('name','criancas['+pId+'][medicamento]');
			  	   form.find('#label-medicamento').attr('name','criancas['+pId+'][medicamento-label]');

			  	   form.find('#alergia').val(item.alergia);
			  	   form.find('#alergia').attr('name','criancas['+pId+'][alergia]');
			  	   form.find('#label-alergia').attr('name','criancas['+pId+'][alergia-label]');

			  	   form.find('.autorizada').attr('name','criancas['+pId+'][autorizada]');
			  	   form.find('#label-autorizada').attr('name','criancas['+pId+'][autorizada-label]');

			  	   rad = form.find('.autorizada');

			  	   rad.each(function(index){
			  	   		if($(this).val() == item.autorizada)
			  	   		{
			  	   			$( this ).attr('checked',true);
			  	   		}	
			  	   });

			  	});
				
				busc.fadeOut('fast');

			  },
			  error: function(xhr, textStatus, errorThrown) {
			    //called when there is an error
			  }
			});
		});

		//Unic id
		var ID = function () {
  			return '_' + Math.random().toString(36).substr(2, 9);
		};


		//Number Format
		function number_format(number, decimals, dec_point, thousands_point) {

		    if (number == null || !isFinite(number)) {
		        throw new TypeError("number is not valid");
		    }

		    if (!decimals) {
		        var len = number.toString().split('.').length;
		        decimals = len > 1 ? len : 0;
		    }

		    if (!dec_point) {
		        dec_point = '.';
		    }

		    if (!thousands_point) {
		        thousands_point = ',';
		    }

		    number = parseFloat(number).toFixed(decimals);

		    number = number.replace(".", dec_point);

		    var splitNum = number.split(dec_point);
		    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
		    number = splitNum.join(dec_point);

		    return number;
		}

	};
}(jQuery));