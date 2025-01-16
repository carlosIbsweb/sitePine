(function ( $ ){
	$.fn.S7dPayments = function( opts ){

	var S7dPaymentsDefault = $.extend({
        contactId  : "",
        modalId    : ""
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
				 		'<option value="">Selecionar</option>'+
				 		'<option value="411 Norte">411 Norte</option>'+
						'<option value="Affinity Arts">Affinity Arts</option>'+
						'<option value="Alvacir Vite Rossi">Alvacir Vite Rossi</option>'+
						'<option value="Anjo da Guarda">Anjo da Guarda</option>'+
						'<option value="Arvense">Arvence</option>'+
						'<option value="Avidus">Avidus</option>'+
						'<option value="Benjamin Franklin Int’L School">Benjamin Franklin Int’L School</option>'+
						'<option value="Britishi School">Britishi School</option>'+
						'<option value="Caic Unesco">Caic Unesco</option>'+
						'<option value="Canarinho">Canarinho</option>'+
						'<option value="Cantinho Mágico">Cantinho Mágico</option>'+
						'<option value="Cei Assefe">Cei Assefe</option>'+
						'<option value="Centro Educacional Parque Encantado">Centro Educacional Parque Encantado</option>'+
						'<option value="Cieic">Cieic</option>'+
						'<option value="Ciman">Ciman</option>'+
						'<option value="Claretiano">Claretiano</option>'+
						'<option value="Cnec">Cnec</option>'+
						'<option value="Coc">Coc</option>'+
						'<option value="Colégio Adventista">Colégio Adventista</option>'+
						'<option value="Construção do Saber">Construção do Saber</option>'+						
						'<option value="Colégio Mauricio Salles de Mello">Colégio Mauricio Salles de Mello</option>'+
						'<option value="Colégio Moraes Rego">Colégio Moraes Rego</option>'+
						'<option value="Dom Bosco">Dom Bosco</option>'+
						'<option value="Dom Pedro II">Dom Pedro II</option>'+
						'<option value="Ec 209 Sul">Ec 209 Sul</option>'+
						'<option value="Escola Americana">Escola Americana</option>'+
						'<option value="Escola Arara Azul">Escola Arara Azul</option>'+
						'<option value="Escola Batista Asa Sul">Escola Batista Asa Sul</option>'+
						'<option value="Escola Classe 111 Sul">Escola Classe 111 Sul</option>'+
						'<option value="Escola DNA">Escola DNA</option>'+
						'<option value="Escola das Nacoes">Escola das Nacoes</option>'+
						'<option value="Escola Internacional de Genebra">Escola Internacional de Genebra</option>'+
						'<option value="Everest">Everest</option>'+
						'<option value="Fundação Cabo Frio">Fundação Cabo Frio</option>'+
						'<option value="INDI">INDI</option>'+
						'<option value="Kingdom Kids">Kingdom Kids</option>'+
						'<option value="Le Petit Galois - Asa Sul">Le Petit Galois - Asa Sul</option>'+
						'<option value="Leonardo da Vinci - Asa Norte">Leonardo da Vinci - Asa Norte</option>'+
						'<option value="Lffm">Lffm</option>'+
						'<option value="Lycée Français François Mitterrand">Lycée Français François Mitterrand</option>'+
						'<option value="Mackenzie">Mackenzie</option>'+
						'<option value="Mapple Bear">Mapple Bear</option>'+
						'<option value="Maria Imaculada">Maria Imaculada</option>'+
						'<option value="Maria Montessori">Maria Montessori</option>'+
						'<option value="Maristinha Pio Xii">Maristinha Pio Xii</option>'+
						'<option value="Master">Master</option>'+
						'<option value="Miri Piri">Miri Piri</option>'+
						'<option value="Montreal">Montreal</option>'+
						'<option value="Oasis Creche Bem Me Quer">Oasis Creche Bem Me Quer</option>'+
						'<option value="Pedacinho do Céu">Pedacinho do Céu</option>'+
						'<option value="Pia Mater">Pia Mater</option>'+
						'<option value="Santa Rosa">Santa Rosa</option>'+
						'<option value="Santo Andre">Santo Andre</option>'+
						'<option value="Serios">Serios</option>'+
						'<option value="Sibipiruna">Sibipiruna</option>'+
						'<option value="Sigma">Sigma</option>'+
						'<option value="Viraventos">Viraventos</option>'+
						'<option value="Vivendo E Aprendendo">Vivendo E Aprendendo</option>'+
						'<option value="CECAN - Candanguinho">CECAN - Candanguinho</option>'+
						'<option value="CEAV Jr">CEAV Jr</option>'+
						'<option value="La Salle">La Salle</option>'+
						'<option value="Marista João Paulo II">Marista João Paulo II</option>'+
						'<option value="Waldorf Moara">Waldorf Moara</option>'+
						'<option value="Centro Educacional Maria Auxiliadora">Centro Educacional Maria Auxiliadora</option>'+
						'<option value="Colégio Batista">Colégio Batista</option>'+
						'<option value="Colégio Corjesu">Colégio Corjesu</option>'+
						'<option value="Colégio Dromos">Colégio Dromos</option>'+
						'<option value="Colégio Notre Dame">Colégio Notre Dame</option>'+
						'<option value="Colégio Perpétuo Socorro">Colégio Perpétuo Socorro</option>'+
						'<option value="COC Lago Norte">COC Lago Norte</option>'+
						'<option value="COC Jardim Botânico">COC Jardim Botânico</option>'+
						'<option value="Colégio Sagrada Família">Colégio Sagrada Família</option>'+
						'<option value="Madre Carmen Salles">Madre Carmen Salles</option>'+
						'<option value="Montessoriana Educação Infantil">Montessoriana Educação Infantil</option>'+
						'<option value="Objetivo">Objetivo</option>'+
						'<option value="Parque Encantado">Parque Encantado</option>'+
						'<option value="Santa Rosa">Santa Rosa</option>'+
						'<option value="SIS Swiss International School">SIS Swiss International School</option>'+
						'<option value="Le Petit Galois - Águas Claras">Le Petit Galois - Águas Claras</option>'+
						'<option value="Pater Hominis">Pater Hominis</option>'+
					'</select>'+
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