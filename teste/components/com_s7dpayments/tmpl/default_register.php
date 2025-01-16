<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/

// No direct access.
defined('_JEXEC') or die;

if(isset($_POST['Cadastrar']))
{
	paymentsUser::register(['name','username','telefone','endereco','cpf','telefone2','name2']);
}

$doc->addScript(JUri::base(true).'/components/com_s7dpayments/assets/js/formValid/dist/jquery.validate.js');
$doc->addScript(JUri::base(true).'/components/com_s7dpayments/assets/js/s7dpaymentsUser.js');



JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>

<div class="row col-md-8">

<form id="payments-register" action="" method="post" class="form-validate" enctype="multipart/form-data">
	<div class="row">
	<div class="form-group col-md-6">
	 	<label for="name" class="form-label">Nome do responsável</label>
	 	<div class="spayment">
	 		<input type="text" class="form-control spayRequire" name="name" id="nam" value="<?= $_POST['name'];?>" autocomplete="off">
	 	</div>
	</div>
	<div class="form-group col-md-6">
	 	<label for="name2" class="form-label">Nome do responsável 2</label>
	 	<div class="spayment">
	 		<input type="text" class="form-control spayRequire" name="name2" id="nam" value="<?= $_POST['name2'];?>" autocomplete="off">
	 	</div>
	</div>
</div>
	<div class="row">
	<div class="form-group col-md-6">
	 	<label for="username" class="col-form-label hasPopover">E-mail (Será o seu usuário)</label>
	 	<div class="spayment">
	 	<input type="text" class="form-control" name="username" id="username" value="<?= $_POST['username'];?>" autocomplete="off">
	 	</div>
	</div>
	<div class="form-group col-md-6">
	 	<label for="username1" class="col-form-label spayRequire">Confirmar E-mail</label>
	 	<div class="spayment">
	 	<input type="text" class="form-control" name="confirm_username" id="username1" autocomplete="off">
	 	</div>
	</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
	 		<label for="cpf" class="form-label">CPF</label>
	 		<div class="spayment">
	 			<input type="text" class="form-control" name="cpf" value="<?= $_POST['cpf'];?>" id="cpf" data-mask="999.999.999-99" autocomplete="off">
	 		</div>	
		</div>
		<div class="form-group col-md-6">
			<div class="spayment">
		 		<label for="cep" class="col-form-label">CEP</label>
		 		<input type="text" data-mask="99999-999" class="form-control spayRequire" name="cep" id="cep" value="<?= $_POST['cep'];?>" autocomplete="off">
		 	</div>
		</div>
	</div>

	<div class="row">
		<div class="form-group col-md-12">
			<div class="spayment">
		 		<label for="endereco" class="col-form-label">Endereço</label>
		 		<input type="text" class="form-control spayRequire" name="endereco" id="endereco" value="<?= $_POST['endereco'];?>" autocomplete="off">
		 	</div>
		</div>
	</div>

	<div class="row">
	
	<div class="form-group col-md-6">
		<div class="spayment">
	 	<label for="telefone" class="col-form-label">Telefone</label>
	 	<input type="text" class="form-control spayRequire" data-mask="9" value="<?= $_POST['telefone'];?>" name="telefone" id="telefone" autocomplete="off">
	 	</div>
	</div>
	<div class="form-group col-md-6">
		<div class="spayment">
	 	<label for="telefone2" class="col-form-label">Telefone (Responsável 2)</label>
	 	<input type="text" class="form-control spayRequire" data-mask="9" value="<?= $_POST['telefone2'];?>" name="telefone2" id="telefone2" autocomplete="off">
	 	</div>
	</div>
	</div>

	<input type="submit" value="Cadastrar" name="Cadastrar" class="pyCadastrar btn btn-primary">
</form>
</div>
<div class="row">
	<div class="col-md-12 alert alert-primary">
		Ao se cadastrar será direcionado a página anterior, onde deverá inserir o seu e-mail como usuário.
	</div>
</div>

<script type="text/javascript" src="modules/mod_wkcontact/assets/js/jquery.mask.min.js"></script>

<script type="text/javascript">
	/***********
 Mascara Telefone
***********/
jQuery(function($){
	var SPMaskBehavior = function (val) {

    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';

  },

  spOptions = {

    onKeyPress: function(val, e, field, options) {

      field.mask(SPMaskBehavior.apply({}, arguments), options);

    }

  };

  $( "#telefone" ).mask(SPMaskBehavior, spOptions)
  $( "#telefone2" ).mask(SPMaskBehavior, spOptions)
	
});

</script>

<script type="text/javascript">
	jQuery(function($){
		$( document ).ready(function(){
			$( "#payments-register" ).validate( {
				rules: {
					name: {
						required: true,
						minlength: 4 
					},
					lastname1: "required",
					username1: {
						required: true,
						minlength: 2
					},
					username: {
						required: true,
						email: true,
					},
					confirm_username: {
						required: true,
						equalTo: "#username"
					},
					cpf: {
						cpf: true, 
						required: true,
						equalTo: "#cpf"
					},
					telefone: {
						required: true,
						equalTo: "#telefone",
						minlength: 14
					},
					endereco: {
						required: true,
						equalTo: "#endereco",
						minlength: 10
					}
				},
				messages: {
					name: {
						required: "Por favor digite seu Nome",
						minlength: "Digite pelo menos 4 caracteres"
					},
					username: {
						required: "Por favor digite um e-mail",
						email: "Por favor digite um e-mail válido",
					},
					confirm_username: {
						required: "Confirmar endereço de email",
						equalTo: "Por favor digite o mesmo endereço de e-mail acima"
					},
					cpf: {
						required: "Por favor digite seu CPF",
						cpf: "Por favor digite um CPF válido"
					},
					telefone: {
						required: "Por favor digite seu Telefone",
						minlength: "Por favor digite um Telefone válido"
					},
					endereco: {
						required: "Por favor digite seu Endereco",
						minlength: "Digite pelo menos 10 caracteres"
					},
				},
				errorElement: "em",
				errorPlacement: function ( error, element ) {
					// Add the `help-block` class to the error element
					error.addClass( "help-block" );

					// Add `has-feedback` class to the parent div.form-group
					// in order to add icons to inputs
					element.parents( ".spayment" ).addClass( "has-feedback" );

					if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.parent( "label" ) );
					} else {
						error.insertAfter( element );
					}

					// Add the span element, if doesn't exists, and apply the icon classes to it.
					if ( !element.next( "span" )[ 0 ] ) {
						$( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
					}
				},
				success: function ( label, element ) {
					// Add the span element, if doesn't exists, and apply the icon classes to it.
					if ( !$( element ).next( "span" )[ 0 ] ) {
						$( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ) );
					}
				},
				highlight: function ( element, errorClass, validClass ) {
					$( element ).parents( ".spayment" ).addClass( "has-error" ).removeClass( "has-success" );
					$( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
				},
				unhighlight: function ( element, errorClass, validClass ) {
					$( element ).parents( ".spayment" ).addClass( "has-success" ).removeClass( "has-error" );
					$( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
				}
			} );
		})

	})
</script>
