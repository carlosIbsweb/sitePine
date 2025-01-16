jQuery(function($){


  $(document).on('click','.aw-form-row .aw-btn-add button', function(event)
  {
    event.stopPropagation();

          var el   = $(this).parents('.aw-form-row'),
          awaddFid = el.find('.aw-add-items').data('id'),
          awAddModalTitle = $(this).text(),
          elItems  = el.find('.aw-add-items');
      
      if(!sessionStorage.getItem(awaddFid))
      {
        sessionStorage.setItem(awaddFid, $(this).parents('.aw-form-row').find('.aw-form-container').html());
        $(this).parents('.aw-form-row').find('.aw-form-container').remove();
      }

      var awContent = sessionStorage.getItem(awaddFid);
      var idCamp = gId(8);

      

      $(awContent).find('[name]').each(function(){

        var vn = $(this).attr('name').split('[')
            v = vn[0]+'['+idCamp+']['+vn[1];

           var repl = $('#'+awaddFid).find('[name]');

          awContent = awContent.replace('name="'+$(this).attr('name')+'"','name="'+v+'"');
      })

      addModal(awAddModalTitle,awContent,awaddFid);

      awFormValid();
  })


   $(document).on('submit','.aw-add-form',function(evt){
        evt.preventDefault();
        var awAddTitle = $(this).find('input[type="text"]').eq(0).val();
        var awAddIt    = $('.aw-add-item');
        var awAddForm  = $(this).find('.aw-form-row').clone();

          modalClose();

        /*Gerando o item*/
        var awAddItem = $(`
        <div class="aw-add-item">
          <div class="aw-add-item-title"></div>
          <div class="aw-add-item-btns">
            <a class="awaddremove"><i class="fa fa-times" aria-hidden="true"></i></a>
          </div>
          <div class="aw-add-item-form"></div>
        </div>
        `);

        awAddItem.find('.aw-add-item-title').append(awAddTitle)
        awAddItem.find('.aw-add-item-form').append(awAddForm)

        var addItem = $(this).attr('id');

        $('#awadditems-'+addItem).prepend(awAddItem)
      })

   /*$(document).on('click','.aw-add-item',function(){
       var el         = $(this).parents('.aw-form-row'),
          awaddFid = el.find('.aw-add-items').data('id'),
          elItems  = el.find('.aw-add-items');

          addModal('kkk',awaddFid);
   })*/

   $( document ).on('click','.awaddremove',function(event){
    event.preventDefault();

    $(this).parents('.aw-add-item').remove();

   })

  var addModal = function(title,content,id){

    var modTeme = `
      <div class="aw-add-modal-bg"></div>
      <div class="aw-add-modal-content">
      <div class="aw-add-modal-inner">
      <h3>`+title+`</h3>
      <form action="" class="aw-add-form" id="`+id+`" method="post" novalidate="novalidate">
        <div class="aw-form-row row">
        `+content+`
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <button type="button" class="awAddClose btn btn-danger">Cancelar</button>
      </form>
      </div>
      </div>

    `;

    if($('.aw-add-modal-bg').length == 0)
    {
      $('body').prepend(modTeme)
    }
    
    maskTel(id);
    $('#'+id).awMask();
  }

  const maskTel = function(id){
    
      $( document ).awMask();
      var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
      },
      spOptions = {
        onKeyPress: function(val, e, field, options) {
          field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
      };

    var masArr = $.makeArray($('#' +id+ ' input[type="tel"]'));
      
      $.each(masArr,function( ind, val){
        if($( this ).data('mask')){
          $( this ).mask(SPMaskBehavior, spOptions);

          $( document ).on('keyup',$(this),function(){
            $(val).attr('data-mask',$(val).val().replace(/[0-9]/g, 9))
          })
        }
      });
    }

  var modalClose = function() {
      $('.aw-add-modal-content').fadeOut('slow',function(){
        var elBg = $(this);
        $('.aw-add-modal-bg').fadeOut('slow',function(){
          $(this).remove();
          elBg.remove();
          
        })
      })
  }

  $(document).on('click','.awAddClose',function(ev){
    ev.preventDefault();
    modalClose();
  })
  

  /*********
   Form Valid
  *********/
  const awFormValid = function(){
    var form = $('.aw-add-form');
    var rules = {};
    var messages = {};
    var jvalid = form.find('[valid]');
      
    jvalid.each(function(index){
      var nEqual = $(this).attr('equalto');
      var mEqual = $(this).attr('mequal');
      if(nEqual != undefined)
      {
        var nEqual = nEqual.replace('#','');
        var mEqual = mEqual;
      }
      
      rules: rules[$(this).attr('name')] = {
          required: true,
          minlength: $(this).data('minlength')
      };

      //Valid Type
      var validType = $(this).attr('valid-type');

      if($(this).attr('valid-type') == 'email'){
        rules[$(this).attr('name')].email = true
      };

      if($(this).attr('valid-type') == 'cpf'){
        rules[$(this).attr('name')].cpf = true
      };

      //#valid type

      messages: messages[$(this).attr('name')] = {
          required:'Campo obrigatório',
          minlength: "Digite pelo menos "+$(this).data('minlength')+" caracteres",
          equalTo: mEqual
      }

      if($(this).attr('valid-type') == 'email'){
        messages[$(this).attr('name')].email = "Digite um e-mail válido"
      };

      if($(this).attr('valid-type') == 'cpf'){
        messages[$(this).attr('name')].cpf = "Digite um cpf válido"
      };

      })

      $( '.aw-add-form' ).validate({
        rules: rules,
        messages: messages,
        errorElement: "em",
        errorPlacement: function ( error, element ) {
          // Add the `help-block` class to the error element
          error.addClass( "help-block" );

          // Add `has-feedback` class to the parent div.form-group
          // in order to add icons to inputs
          element.parents( ".awValidMsg" ).addClass( "has-feedback" );

          if ( (element.prop( "type" ) != "radio") && (element.prop( "type" ) != "checkbox") ) {
            error.insertAfter( element );
          }

          // Add the span element, if doesn't exists, and apply the icon classes to it.
          
          if ( (element.prop( "type" ) != "radio") && (element.prop( "type" ) != "checkbox") ) {
              if ( !element.next( "span" )[ 0 ] ) {
              $( "<span class='glyphicon glyphicon-remove form-control-feedback'></span>" ).insertAfter( element );
              }
          }
        },
        success: function ( label, element ) {
          // Add the span element, if doesn't exists, and apply the icon classes to it.
          if ( !$( element ).next( "span" )[ 0 ] ) {
            $( "<span class='glyphicon glyphicon-ok form-control-feedback'></span>" ).insertAfter( $( element ).parents('.awValidMsg') );
          }
        },
        highlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".awValidMsg" ).addClass( "has-error" ).removeClass( "has-success" );
          $( element ).next( "span" ).addClass( "glyphicon-remove" ).removeClass( "glyphicon-ok" );
        },
        unhighlight: function ( element, errorClass, validClass ) {
          $( element ).parents( ".awValidMsg" ).addClass( "has-success" ).removeClass( "has-error" );
          $( element ).next( "span" ).addClass( "glyphicon-ok" ).removeClass( "glyphicon-remove" );
        }
    });

    /********
      Validar E-mail
    *********/
   jQuery.validator.addMethod("email",function( value, element ) {
    //Validar e-mail;
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        if(!filter.test( value )){
           return false;
        }else{
            return true;
        }
    });

    /*****
        Validar cpf
    *****/
    jQuery.validator.addMethod("cpf",function( value, element ) {

    value = value.replace(/[^0-9]/g, '');
    value = jQuery.trim(value);
    
    value = value.replace('.','');
    value = value.replace('.','');
    cpf = value.replace('-','');
    while(cpf.length < 11) cpf = "0"+ cpf;
    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
    var a = [];
    var b = new Number;
    var c = 11;
    for (i=0; i<11; i++){
        a[i] = cpf.charAt(i);
        if (i < 9) b += (a[i] * --c);
    }
    if ((x = b % 11) < 2) { a[9] = 0 } else { a[9] = 11-x }
    b = 0;
    c = 11;
    for (y=0; y<10; y++) b += (a[y] * c--);
    if ((x = b % 11) < 2) { a[10] = 0; } else { a[10] = 11-x; }
    
    var retorno = true;
    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) retorno = false;
    
    return this.optional(element) || retorno;
   
    });
  }

})

function gId(len) {
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    var string_length = 10;
    var randomstring = '';

    for (var x=0;x<string_length;x++) {

        var letterOrNumber = Math.floor(Math.random() * 2);
        if (letterOrNumber == 0) {
            var newNum = Math.floor(Math.random() * 9);
            randomstring += newNum;
        } else {
            var rnum = Math.floor(Math.random() * chars.length);
            randomstring += chars.substring(rnum,rnum+1);
        }

    }
    return randomstring;
}