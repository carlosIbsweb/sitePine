(function ( $ ) 
{
  $.fn.valid = function( opt )
  {

    // This is the easiest way to have default options.
    var opt = $.extend({
        // These are the defaults.
        formId: "",
        idForm: "",
        offSetTop: 120,
        awEditId: false,
        awEdit: false,
        idEdit: '',
        modalForm: true,
        captAlign: 'float:left',
        divParent: ''
    }, opt );

    const awEl = this;
    const awStart = function(){
      var aHeight = $(opt.formId).find('[auto-height]');
      aHeight.each(function(){
        var aHeight = $(this).parents('.aw-form-row').height();
        var aL = $(this).parents('.form-group').find('label');
        var aLHeight = 0;
        if(aL.length != 0)
        {
          var aLHeight = aL.outerHeight() + 5;
        }
 
        var aHeight = (aHeight - 15) - (aLHeight);
        $(this).css({"height":aHeight+'px'})
      }); 

      //Modal Form
      var mForm = $(opt.formId).clone();
      sessionStorage.setItem('awMform-'+opt.idForm, mForm.clone().find('.awCaptchaRe').html('<div class="aw-form-row row" style="margin:0 !important"><div style="'+opt.captAlign+'; display:table;" class="awLoader-18"></div></div>').end().html());

      var formI = $(opt.formId).find('.aw-form-fields').clone().find('.awCaptchaRe').html('<div class="aw-form-row row" style="margin:0 !important"><div style="'+opt.captAlign+'; display:table;" class="awLoader-18"></div></div>').end().html();
      sessionStorage.setItem('awForm-'+opt.idForm, formI);
    }


    const awEdit = function(){
      if($('.aw-form-edit-bg').length == 0 && $('.aw-form-edit-content'))
      {
        $('body').prepend('<div class="aw-form-edit-bg" style="display:none"></div><div class="aw-form-edit-content" style="display:none"><div class="awLoader-19 awLoading"></div></div>');
      }
      
      $('.aw-form-edit-bg').fadeIn('slow');
      $('.aw-form-edit-content').fadeIn('slow');
      var formIEdit = $(opt.awEditId);
      setTimeout(function() {
        $(formIEdit).hide().prependTo('.aw-form-edit-content').fadeIn("slow");
        $('.aw-form-edit-content').find('.awLoading').remove();
      }, 500);
    }

    /*********
     Form Valid
    *********/
    const awFormValid = function(){
      var form = $(opt.formId);
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

        $( opt.formId ).validate({
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

    /*********
     Ajax Form
    *********/
    const awFormAjax = function(formId){
      var awformId = formId.replace('#','');

      $(document).on(awformId, function(evt,formId) {
        var form = $('#'+formId),
        formData = new FormData();
            formParams = form.serializeArray();

          $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
              formData.append(tag.name, file);
            });
          });

          $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
          });

          formData.append('moduleId',formId);

        $(form).find(".wkstatus").html('tt').fadeIn('slow');
        
        $.ajax({
          url   : 'index.php?option=com_ajax&module=s7dform&method=get&format=raw',
          type   : 'POST',
          data: formData,
          contentType: false,
              cache: false,
              processData:false,
          beforeSend: function (response) {
            //$(form).find(".aw-form-status").html( '<div class="aw-status-loading awLoader-19"></div>' ).fadeIn('slow');
            //animeScroll($(form).find('.aw-form-status'),500,120);
            $('body').prepend($('<div class="aw-form-loader-bg"></div><div class="aw-form-loader"><div class="awLoader-18"></div></div>').hide().fadeIn(300));
          },
          success: function (response) {
            setTimeout(function(){
              if(response) {
                try {
                  data = JSON.parse(response);
                } catch(e) {
                  data = response;
                }
              }

              if(typeof data == 'object')
              {
                if(data.success == true)
                {
                  success = data.mSuccess;

                  if(data.redirect != false){
                    $(".aw-form-loader").fadeOut(function(){
                      $(this).html( '<span class="redirectAw" style="display:none">'+ success+'<div class="awLoader-15 loading"></div></span>').fadeIn();
                      $('.redirectAw').fadeIn();
                    });
                    setTimeout(function(){window.location.assign(data.redirectUrl);},data.redirectTime);
                    return;
                  }
                  $(form).find('.aw-form-fields').fadeOut(300, function(){ 
                    $(this).remove();
                    var offTop = $(form).find('.aw-form-fields').outerHeight() + opt.offSetTop;
                    animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop); 
                    $(form).find(".aw-form-status").html( success ).fadeIn('slow');

                    //Limpando formulário
                    $(form).trigger('reset');
                  });
                  
                }
              }
              else
              {
                $(form).find(".aw-form-status").html( response ).fadeIn('slow');
                animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop);
                $( document ).trigger('awCaptcha'+formId,[formId]);
              }

              $('body').find('.aw-form-loader-bg').fadeOut(300, function(){
                $(this).remove();
              })

               $('body').find('.aw-form-loader').fadeOut(300, function(){
                $(this).remove();
              })
            },1200);
    
          }
        });

      });
    }

    const awCaptcha = function(formId){

      $(document).on('awCaptcha'+formId, function(evt,formId) {
        var form = $('#'+formId);
        formData = new FormData();
        formData.append('moduleId',formId);

        $.ajax({
            url   : 'index.php?option=com_ajax&module=s7dform&method=awCaptcha&format=raw',
            type   : 'POST',
             data: formData,
            contentType: false,
                cache: false,
                processData:false,
            success: function (response) {
              $(form).find('.awCaptchaRe').html(response);
            }
          });
      });
    }

    const awLogin = function(formId){

      $(document).on('awLogin'+formId, function(evt,formId) {
        var form = $('#'+formId);
        formData = new FormData();
        formData.append('moduleId',formId);
        formParams = form.serializeArray();

         $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
          });

        $.ajax({
            url   : 'index.php?option=com_ajax&module=s7dform&method=awLogin&format=raw',
            type   : 'POST',
             data: formData,
            contentType: false,
                cache: false,
                processData:false,

              beforeSend: function(){
                 $('body').prepend($('<div class="aw-form-loader-bg"></div><div class="aw-form-loader"><div class="awLoader-18"></div></div>').hide().fadeIn(300));
              },
            success: function (response) {
              setTimeout(function() {

                var offTop = $(form).find('.aw-form-fields').outerHeight() + opt.offSetTop;
                    animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop); 
                    $(form).find(".aw-form-status").html( response ).fadeIn('slow');

              $('body').find('.aw-form-loader-bg').fadeOut(300, function(){
                $(this).remove();
              })

               $('body').find('.aw-form-loader').fadeOut(300, function(){
                $(this).remove();
              })

              }, 1200);

            }
          });
      });
    }

    const awUpDado = function(formId){

      $(document).on('awUpDado'+formId, function(evt,formId,awUEToken) {
        var form = $('#'+formId);
        formData = new FormData();
        formData.append('moduleId',formId);
        formData.append('awUEToken',awUEToken);

        $.ajax({
            url   : 'index.php?option=com_ajax&module=s7dform&method=awD&format=raw',
            type   : 'POST',
             data: formData,
            contentType: false,
                cache: false,
                processData:false,

              beforeSend: function(){
                 $('body').prepend($('<div class="aw-form-loader-bg"></div><div class="aw-form-loader"><div class="awLoader-18"></div></div>').hide().fadeIn(300));
              },
            success: function (response) {
              setTimeout(function() {

                var offTop = $(form).find('.aw-form-fields').outerHeight() + opt.offSetTop;
                    animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop);
                    $(form).find(".aw-form-status").html( response ).fadeIn('slow');

              $('body').find('.aw-form-loader-bg').fadeOut(300, function(){
                $(this).remove();
              })

               $('body').find('.aw-form-loader').fadeOut(300, function(){
                $(this).remove();
              })

              }, 1200);

            }
          });
      });
    }

    const animeScroll = function(el,tmp,offTop){
      var $doc = opt.modalForm == true ?  $('.awMform') : $('html, body');
          $doc.animate({
            scrollTop: $(el).offset().top - offTop
          }, tmp);
    }

    const maskTel = function(){
    
      $( document ).awMask();
      var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
      },
      spOptions = {
        onKeyPress: function(val, e, field, options) {
          field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
      };

    var masArr = $.makeArray($(opt.formId + ' input[type="tel"]'));
      
      $.each(masArr,function( ind, val){
        if($( this ).data('mask')){
          $( this ).mask(SPMaskBehavior, spOptions);
        }
      });
    }

    //modal form
    const awModal = function(){
      //Removendo parent do form
      if(opt.divParent != ''){
        $(opt.formId).parents(opt.divParent).remove();
      }


      $(opt.formId).remove();
      const modalForm = function(fid){
      //removendo scroll body
      $('body').css('overflow-y','hidden')
        var layModal = [];
        var fid = $(fid).attr('href').replace(/[^\d]+/g,'');
      if($('.awMform').length == 0){
        layModal.push('<div class="awMform" style="display:none">');
        var modForm = '<form action="" method="post" id="awForm-'+fid+'" novalidate="novalidate">'+sessionStorage.getItem('awMform-'+fid)+'</form>';
        $('#awForm-'+fid).show();
        layModal.push('<div class="awMformContent animated fadeInDown" style="display:none"><span class="awMfClose"></span>'+modForm+'</div>');
        layModal.push('</div>')
        layModal = layModal.join('');
        $('body').prepend(layModal);
        $('.awMform').fadeIn(function(){
            $('.awMformContent').fadeIn(function(){
        $( document ).trigger('awCaptcha'+'awForm-'+fid,['awForm-'+fid]);
            });
        })
      }

      //Mascara telefone
      maskTel();
      //Validação
      awFormValid();
      }

      $(document).on('click','.awFormModal',function(event){
        event.preventDefault();
        if(awEl.attr('id') == 'awForm-'+$(this).attr('href').replace(/[^\d]+/g,'')){
          modalForm($(this));
        }
      })
      const fClose = function(event){
        event.preventDefault();
        $('.awMformContent').removeClass('fadeInDown').addClass('bounceOutUp')
        setTimeout(function(){
          $('.awMformContent').fadeOut(function(){
          $('.awMform').fadeOut(function(){
            //removendo scroll body
            $('body').css('overflow-y','auto')
            $(this).remove();
          })
        })  
        },600)
        
      }

      $(document).on('click','.awMformContent',function(event){
        event.stopPropagation();
      })

      $(document).on('click','.awMbg,.awMfClose',function(event){
        event.preventDefault();
        fClose(event);
      })
    }

    return this.each(function(){

    /****************
     *Eventos
    ****************/
    var awformId = opt.formId.replace('#','');

    $( document ).on("submit",opt.formId,function(event){
      event.preventDefault();
      let tFormId = $(this).attr('id');
      $( document ).trigger(tFormId,[tFormId]);
    });


    //New Form
    $( document ).on("click",opt.formId+' .aw-new',function(event){
      event.preventDefault();
      let form = $(this).parents('form');
      let formIdNew = form.attr('id');
      $( document ).trigger('awCaptcha'+awformId,[awformId]);
      form.prepend('<div class="aw-form-fields">'+sessionStorage.getItem(formIdNew)+'</div>');
      $( document ).awMask();
      form.find(".aw-form-status").html( '' );
      //form.find(".awValidMsg").removeClass( 'has-feedback has-error has-success' );
      //form.find(".awValidMsg").find('.glyphicon').remove();
      animeScroll(form,500,120);
      
    });


     $( document ).on("click",opt.formId+' .aw-refresh',function(event){
      event.preventDefault();
      let tFormId = $(this).parents('form').attr('id');
      $(this).addClass('fa-spin');
      $( document ).trigger('awCaptcha'+tFormId,[tFormId]);
    });

    $( document ).on("click",opt.formId+' .aw-edit',function(event){
      event.preventDefault();
      let tFormId = $(this).parents('form').attr('id');
      $( document ).trigger('awLogin'+tFormId,[tFormId]);
    });

    //AwDado
    $( document ).on('click',opt.formId+' .aw-upD',function(){
        event.preventDefault();
        let tFormId = $(this).parents('form').attr('id');
        let awUConfirm = $(this).data('confirm');
        let awUEToken = $(this).data('aw-edit-token');
        if(confirm(awUConfirm) == true){
            $( document ).trigger('awUpDado'+tFormId,[tFormId,awUEToken]);
        }
    })


   
    awFormValid();
    awCaptcha(awformId);
    if(!opt.edit){
      awStart();
      awFormAjax(opt.formId);
    }else{
      awLogin(awformId);
      awEdit();
      awUpDado(awformId);
    }

    if(opt.modalForm == true){
      awModal();
    }

    $(this).each(function(){
       maskTel();
    })
    
  })

  };
 
}( jQuery ));
