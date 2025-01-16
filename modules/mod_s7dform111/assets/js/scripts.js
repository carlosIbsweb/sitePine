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
     Ajax Form
    *********/
    const awFormAjax = function(formId){
      var awformId = formId.replace('#','');

      $(document).on(awformId, function(evt,formId) {
          var form = $('#'+formId),
          formData = new FormData();
          formParams = form.serializeArray();

          //botão de submit
          let botaoSubmit = form.find('[type="submit"]')
          let textoBotaoSubmit = botaoSubmit.text();

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
            $(form).find(".aw-form-status").html( `<div class="alert alert-info" style="text-align:center">Aguarde... <div class="awLoader-18"></div></div>` ).fadeIn('slow')
            //Desarmar o botão em quanto faz o processamento.
            //botaoSubmit.attr('disabled',true)
            animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop);
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
                  
                  //Limpando formulário
                  if(!data.edicaoUsuario){
                     $(form).trigger('reset');
                  }
                  if(data.redirect === '1'){
                    let redirectUrl = data.redirecturl
                    // Redirecionar para uma URL específica
                    setTimeout(function() {
                      window.location.href = redirectUrl
                    }, data.timeredirect);
                    
                  }
                   

                   $(form).fadeOut(function(){
                      $(this).html( success ).fadeIn('slow')
                   })
                  //$(form).find(".aw-form-status").html( success ).fadeIn('slow')
                  animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop);
                }
              }
              else
              {
                try {
                  data = JSON.parse('['+data.trim().replace(/,$/, '')+']');
                } catch(e) {
                  data = data;
                }

                if(typeof data == 'object')
                {
  
                  let mensagemPorCampo = data

                  $('.aw-text-error').remove()
                  $('.aw-input-error').removeClass('aw-input-error')
  
                $(mensagemPorCampo).each(function(n,v){

                  let name = Object.keys(v)
                  let mensagem = Object.values(v)
                  let itemName = $('[id="'+name+'"]')
                  
                  itemName.addClass('aw-input-error')
                  $('[id="'+name+'"]').after(`<span class="aw-text-error">${mensagem}</span>`)
                  
                })
                if($(form).find('.aw-text-error').length){
                  animeScroll($(form).find('.aw-text-error').eq(0),500,opt.offSetTop);
                }
                
                $(form).find(".aw-form-status").fadeOut('slow')
              }else{
                //alert(response)

                $(form).find(".aw-form-status").html( response ).fadeIn('slow');
                animeScroll($(form).find('.aw-form-status'),500,opt.offSetTop);
                $( document ).trigger('awCaptcha'+formId,[formId]);
              }
                
              }

              botaoSubmit.html(textoBotaoSubmit).
              attr('disabled',false)
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

    const animeScroll = function(el, tmp, offTop) {
      const $doc = opt.modalForm === true ? $('.awMform') : $('html, body');
      const $el = $(el);

      if ($el.length === 0) {
        console.error('Element not found:', el);
        return;
      }

      const elTop = $el.offset().top;

      if (isNaN(elTop)) {
        console.error('Invalid element position:', el);
        return;
      }

      $doc.animate(
        {
          scrollTop: elTop - offTop,
        },
        tmp
      );
    };


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

    //Verificar se são iguais os campos
    function validarIgualdade() {
                var elementosIguais = document.querySelectorAll('[equalto]');

                elementosIguais.forEach(function (elemento) {

                    var campoAtual = elemento;
                    var campoAlvoId = campoAtual.getAttribute('equalto');
                    var mensagemErro = campoAtual.getAttribute('mequal');

                    if (!campoAlvoId) {
                        // Se o atributo equalto não estiver definido, pule este campo
                        return;
                    }

                    var campoAlvo = document.getElementById(campoAlvoId);

                    if (!campoAlvo) {
                        // Se o campo de destino não existir, pule este campo
                        return;
                    }

                    // Verifique se os campos são iguais
                    if (campoAtual.value !== campoAlvo.value) {
                        // Campos não são iguais, mostre a mensagem de erro
                        var mensagemSpan = document.createElement('span');
                        mensagemSpan.className = 'aw-text-error';
                        mensagemSpan.textContent = mensagemErro;

                        // Verifique se já existe uma mensagem de erro e a remova
                        var mensagemAnterior = campoAtual.parentNode.querySelector('.aw-text-error');
                        if (mensagemAnterior) {
                            campoAtual.parentNode.removeChild(mensagemAnterior);
                        }


                        // Insira a mensagem de erro após o campo atual
                        campoAtual.parentNode.insertBefore(mensagemSpan, campoAtual.nextSibling);
                        return false; // As senhas não conferem, retorne false
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
      //awFormValid();
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


   
   // awFormValid();
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
