<?php

/**
 * @subpackage  mod_dcontact
 * @copyright   Copyright (C) 2017 - Web Keys.
 * @license     GNU/GPL
 */

// Acesso ao Joomla
defined('_JEXEC') or die;

?>


<form action="" id="dform" class="dcontact-form">
	<div class="form-group col-md-8">
    	<label for="dname" class="control-label">*Nome</label>
    	<input type="text" name="name" class="form-control" id="dname" placeholder="Nome" required>
 	</div>
 	<div class="form-group dselecionar col-md-4">
 	  <label for="dname" class="control-label">*Forma de contato</label>
 	  <select class="form-control seldcontact" required>
      <option value="">Selecionar</option>
  		<option value="telefone">Telefone</option>
  		<option value="email">E-mail</option>
	 </select>
	</div>
  <?php if($params->get('dbanco') == 1): ?>
   <div class="form-group dbanco col-md-12">
      <label for="dbanco" class="control-label col-md-12 row inpdbanco">*Cliente do Banco Bradesco?</label>
      <label class="radio-inline"><input type="radio" name="dbanco" required value="Sim">Sim</label>
      <label class="radio-inline"><input type="radio" name="dbanco" required value="Não">Não</label>
  </div>
  <?php endif; ?>
  <div class="form-group dmail fadeInUp sppb-animated col-md-12" style="display:none">
    <label for="dmail" class="control-label">*E-mail</label>
    <input type="email" name="email" class="form-control" id="dmail" placeholder="E-mail" value="">
  </div>
  <div class="form-group dfone fadeInUp sppb-animated col-md-12" style="display:none">
    <label for="dfone" class="control-label">*Telefone</label>
    <input type="text" name="telefone" class="form-control" id="dfone" placeholder="Telefone" value="">
  </div>

  <div class="col-md-12">
    
    <div class="g-recaptcha" data-sitekey="6LeT5hkUAAAAAIOTYZmY6z3ZmTwsLTDW2PmfGB2H"></div>
      <button type="submit" class="btn btn-primary denvia">Enviar</button>
    </div> 
	
</form>
<div class="dstatus col-md-12"></div>