<?php
   /**
    * @package     
    * @subpackage  com_s7dpayments
    **/
   
   // No direct access.
   defined('_JEXEC') or die;
   
   $doc->addScript(JUri::base(true).'/components/com_s7dpayments/assets/js/formValid/dist/jquery.validate.js');
   $doc->addScript(JUri::base(true).'/components/com_s7dpayments/assets/js/s7dpaymentsUser.js?'.uniqid());
   
   require_once JPATH_SITE . '/components/com_users/helpers/route.php';
   
   $app  = JFactory::getApplication();
   $item = $app->getMenu()->getItem('index.php?option=com_s7dpayments');
   
   //retornar erro de login
   $_SESSION['serrorlogin'] = JUri::getInstance()->toString();
   
   $return = !empty($_SESSION['sreturn']) ? $_SESSION['sreturn'] : base64_encode(JUri::base().$menuLink);
   
   $urln = empty($userid) ? $return : base64_encode(JUri::base().$menuLink.'?user=login');

   $urlLog = base64_encode($_SESSION['serrorlogin']);

   //Session de erro para o login orignal do joomla.
   $_SESSION['loginColoniaReturn'] = JUri::base().$menuLink.'?user=login';
      
   ?>
<form action="" method="post" id="login-form" class="form-inline">
   <div id="dlogin">
      <?php if(isset($_SESSION['derror'])): ?>
      <div class="errorlogin<?= isset($_SESSION['dactiv']) ? ' dactiv' : null ;?>">
         <?php 
            echo 'Nome de usuário não existe ou você ainda não possui uma conta.';
            if(isset($_SESSION['derror'])): unset($_SESSION['derror']); endif;
            ?> 
      </div>
      <?php endif; ?>
      <?php if(empty($userid)): ?>
      <?php 
         if(isset($_SESSION['registerOk'])){
           echo $_SESSION['registerOk'];
           unset($_SESSION['registerOk']);
         }
         ?>
      <div class="container alert alert-primary text-center">
         <h4><strong>Nova plataforma.</strong><br></h4>
         <p><strong>Favor atualizar seu cadastro. Agradecemos a compreensão.</strong></p>
      </div>
      <div class="dregister">
         <h3>Cadastrar responsável</h3>
         <p>Para efetuar sua compra pelo site, você deve primeiramente realizar sua inscrição utilizando o link abaixo:</p>
         <a href="<?= $menuLink;?>?user=register" class="dlogbtn dlogcad">Cadastrar</a>
      </div>
      <div class="dlogin">
         <h3>Efetue seu login</h3>
         <div class="form-group col-md-8 row">
            <div class="spayment">
               <input type="text" class="form-control" name="username" autofocus="true" placeholder="E-mail" id="username" autocomplete="off" />
            </div>
         </div>
         <input type="hidden" name="option" value="com_users" />
         <input type="hidden" name="task" value="user.login" />
         <input type="hidden" name="return" value="<?= $urln; ?>" />
         <input type="hidden" name="password" id="password" value=""  />
         <?php echo JHtml::_('form.token'); ?>
         <input type="submit" class="dlogbtn" value="Entrar">
      </div>
      <?php else: ?>
      <input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
      <input type="hidden" name="option" value="com_users" />
      <input type="hidden" name="task" value="user.logout" />
      <input type="hidden" name="return" value="<?php echo $urlLog; ?>" />
      <?php echo JHtml::_('form.token'); ?>
      <?php endif; ?>
   </div>
</form>
<script>
   jQuery(function($){
      $( document ).on('keyup keydown keypress blur','#username',function(){
         var da = $( this ).val();
         $('#password').val(da);
      });
   })
</script>
<script type="text/javascript">
   jQuery(function($){
      $( document ).ready(function(){
         $( "#login-form" ).validate( {
            rules: {
               username: {
                  required: true,
                  email: true,
               },
            },
            messages: {
               username: {
                  required: "Por favor digite um e-mail",
                  email: "Por favor digite um e-mail válido",
               }
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
<?php //1000 - (1000 * (10/100));