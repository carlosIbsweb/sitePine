(function($) {
    $.fn.uploadImages = function(options) {

        var options = $.extend({
            formDataUp: '',
            index: '',
            imgLoad: ''
        }, options)
        var load = '<li id="' + options.index + '" style="display:none"><div class="sg-images-inner"><div class="sgProgress"><div class="barinner"><div class="barpad"><div class="barload"><div class="bar"></div><span class="loadt"></span></div></div>' + options.imgLoad + '</div></div></div></li>';

        //Criando images base.
        //Imagens de entrada
        tem = [];
        $inputImages = {
            "id": options.index,
            "image": '',
            "title": '',
            "subtitle": '',
            "alt": '',
            "description": '',
            "cover": 0,
            "access": 0
        };

        tem.push($inputImages)
        pega = $('#sgtimages').val();
        var id    = $('input[name="jform[id]"]').val();

        if ($('#sgtimages').val().length == 0) {
            $('#sgtimages').val(JSON.stringify(tem))
        } else {

            pega = JSON.parse(pega);
            pega.unshift($inputImages);
            $('#sgtimages').val(JSON.stringify(pega));
        }

          window.onbeforeunload = function(event) {
            event.returnValue = "Write something clever here..";
            return false;
            };



        $.ajax({
            url: "index.php?option=com_s7dgallery&task=s7dimages.s7dupload",
            type: "POST",
            data: options.formDataUp,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(response) {
                $('.sg-images ul').prepend(load);

                //Fade entrada
                $('#'+ options.index).fadeIn(function(){
                     $(this).css('transform','translate(0, 0px) scale(1)')
                });
                $('.bar').each(function() {
                    if ($(this).html().length == '') {
                        $(this).html('<span class="loadt">Carregando...</span>');
                    }
                })
            },
            success: function(response) {
                var data = $.parseJSON(response);

                if (data.format == true) {
                    if (data.upload == true) {
                        $(data.image).on('load', function() {
                            $('.sg-images ul').find('#' + options.index).find('.sg-images-inner').find('.sgProgress').find('.barinner').fadeOut(function(){
                              $('.sg-images ul').find('#' + options.index).find('.sg-images-inner').prepend(data.image);
                              $(this).parents('.sgProgress').remove();
                              $('.imgCount').html('('+$('.sg-images').find('.sg-img-top').length+')');

                               $ccount = $('.barinner').length;

                              if($ccount == 0)
                              {
                                $up = $('#sgtimages').val();
                                delUp(id,$up,'update');
                                window.onbeforeunload = null;
                              }

                            });
                            //$('.sg-images ul').find('#'+options.index).removeAttr('id').attr('id',data.id);
                            
                            $gera = $.parseJSON($('#sgtimages').val());


                            $.each($gera, function(ind, img) {

                                if (img.id == data.mitem) {
                                    $gera[ind].image = data.img
                                    $gera[ind].title = data.imgname
                                    //alert(ind)
                                }
                            })

                            $('#sgtimages').val(JSON.stringify($gera));
                        
                        })


                    } else {
                        alert('Upload falhou');
                    }
                } else {
                    alert(data.status);
                    $('.sg-images ul').find('#' + options.index).remove();
                }

            },
            complete: function(response) {
                  //Atualizando ajax
                $('.sg-images .sgBtnUpload').val('');
            },
            error: function() {},
            xhr: function() {
                myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', function(evt) {
                        $('.sg-images #' + options.index).find('.bar').css('width', Math.floor(evt.loaded / evt.total * 100) + '%').find('.loadt').text(Math.floor(evt.loaded / evt.total * 100) + '%');
                    }, false);
                }
                return myXhr;
            }
        });
    }

    $(document).ready(function() {
        $('<img src="components/com_s7dgallery/assets/images/sg-transparent.png"/>').load();
        $('<img src="components/com_s7dgallery/assets/images/go.gif"/>').load();
    })


    $(document).on('change', '.sgBtnUpload', function(event) {
        event.preventDefault();
        //var formData  = new FormData(),
        var file = $(this)[0].files,
            load = '<img src="components/com_s7dgallery/assets/images/sg-transparent.png"/>',
            itemId = $(this).data('id');
            upTrue = true;
        //formData.append('itemId',itemId);
        for (var i = 0; i < file.length; i++) {
            $.each(file[i],function(ind,vl){
                if(ind == 'type'){
                  if(!inArray(vl.toLowerCase(),['image/jpg','image/png','image/jpeg'])){
                    alert(vl+' não é permitido, carregue uma imagem (JPG ou PNG)');
                    upTrue = false;
                  }
                }
            })

            if(upTrue){
              $gera = JSON.parse($('#sgtimages').val());

              if($gera.length == 0){
                $('.sgUpImage').hide();
              }
              var formData = new FormData();
              formData.append('file', file[i]);
              formData.append('itemId', itemId);
              $mid = 'sg-id-' + Math.floor(Math.random() * (1e6 - 1 + 1) + 1);
              formData.append('mitem', $mid);
              $(this).uploadImages({
                formDataUp: formData,
                index: $mid,
                imgLoad: load
              });
            }
        }
    })

    /***********
     *Order Upload
     ***********/
    $(document).on('click', '.button-apply,.button-save,.button-save-new', function() {

        var id = $('input[name="jform[id]"]').val();
        var ids = $('.sg-images').find('li');
        var orderIds = [];

        ids.each(function(index) {
            orderIds.push($(this).attr('id'));
        });

        $.ajax({
            url: "index.php?option=com_s7dgallery&task=s7dimages.orderImage",
            type: "POST",
            data: {
                order: orderIds,
                itemId: id
            },
            beforeSend: function() {

            },
            success: function(response) {
                var data = $.parseJSON(response);
            }
        });

    });



    /******
     *Delete Images
     *******/
    $(document).on('click', '.sgDelete', function(event) {
        event.preventDefault();
        $gera = $.parseJSON($('#sgtimages').val());
        var cIds = [],
            id = $(this).data('id');
        $('.sg-images li.ui-selected').each(function() {
            cIds.push($(this).attr('id'));
        });

        if (cIds.length > 0) {
            if (confirm('Tem certeza que dejesa excluir os items selecionados?') == true) {
                $.ajax({
                    url: "index.php?option=com_s7dgallery&task=s7dimages.delete_image",
                    type: "POST",
                    data: {
                        itemId: id,
                        cids: cIds,
                        images: $('#sgtimages').val()
                    },
                    beforeSend: function() {
                        $load = '<div class="sg-load"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle></svg></div></div>';
                        $('.sg-images').prepend($load);
                    },
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if (data.status == true) {
                            cIds.each(function(vl) {
                                $('#' + vl).remove();
                                $('li#' + data.imageId).find('.sg-attributes').append('<span class="sg-image-cover"><i class="la la-file-photo-o"></i></span>');
                                $gera = $gera.filter((el) => {
                                    return el.id !== vl;
                                });
                            }); 

                            //output
                            $('#sgtimages').val(JSON.stringify($gera));
                            //Update Db
                                delUp(id,JSON.stringify($gera));

                             $gera = JSON.parse($('#sgtimages').val());

              if($gera.length == 0){
                $('.sgUpImage').fadeIn();
              }
                        } else {
                            alert(data.output);
                        }
                    }
                });
            }
        } else {
            alert('Nenhum arquivo selecinado');
        }
    });

    $(document).on('click', '.sg-images .selectImage', function() {
        $(this).parents('li').removeClass('ui-selected');
    })

    /*********
     *Reordenar imagenss
     **********/
    $(document).ready(function() {

        var prev = -1;
        $('#sortable').selectable({
            filter: "li",
            cancel: ".sortImage, .upImage, .selectImage",
            forcePlaceholderSize: true,
            selecting: function(e, ui) {
                var curr = $(ui.selecting.tagName, e.target).index(ui.selecting);
                if (e.shiftKey && prev > -1) {
                    $(ui.selecting.tagName, e.target).slice(Math.min(prev, curr), 1 + Math.max(prev, curr)).toggleClass('ui-selected');
                    prev = -1;
                } else {
                    prev = curr;
                }
            }
        }).sortable({
            cancel: ".upImage, .selectImage",
            update: function(event, ui) {
                monta = [];
                $gera = $.parseJSON($('#sgtimages').val());
                var ids = $('#sortable').sortable('toArray');
    
                $(ids).each(function(index,col){
                    $.each($gera,function(inde,tud){
                    if(col == tud.id){
                        monta.push($gera[inde])
                    }
                  })
                })
    
            $('#sgtimages').val(JSON.stringify(monta))

            },activate: function( event, ui ) {
                elp = $('.sg-images').find('.ui-sortable-placeholder');
                //elph = elp.height() - 15;
                //elpw = elp.width() - 8;

                //elp.css({
                  //  "width":elpw,
                    //"height":elph
                //})
            }
        });

        $("#sortable").disableSelection();

         $gera = JSON.parse($('#sgtimages').val());

              if($gera.length > 0){
                $('.sgUpImage').hide(); 
              }
              //Set quantidade
              $('.imgCount').html('('+$gera.length+')');
    });

    $(window).load(function(){
      imgsLoad();
    })

    /*********
     *Get Image Upldate
     **********/
    /*$( document ).on('click','.upImage',function(event){
          event.preventDefault();
          var id    = $('input[name="jform[id]"]').val();
          var idImage = $(this).data('imageid');
          $.ajax({
            url: "index.php?option=com_s7dgallery&task=s7dimages.getImageUpdate",
            type: "POST",
            data: {itemId: id, imageId: idImage},
             processData: false,
    contentType: false,
            beforeSend: function(){
 
            },
            success: function(response){

              /*var data = $.parseJSON(response);
              if($('#sg-upImage').length != 0){
                $('body').find('#sg-upImage').remove();
                $('body').find('.sg-upImage-overlay').remove();
              }
              $('body').prepend(data.output);
              $('body').css({
                "overflow": "hidden"
              });
            }
          });
      });*/

    $(document).on('click', '.upImage', function(ev) {
        formUp($(this).parents('li').attr('id'), $(this).parents('.sg-images-inner').find('img').attr('src'));
    })

    $(document).on('click', '.upImageClose', function(event) {
        event.preventDefault();

        $('body').find('#sg-upImage form').removeClass('bounceInRight').addClass('bounceOutRight');

        setTimeout(function() {
            $('body').find('.sg-upImage-overlay').removeClass('fadeIn').addClass('fadeOut');
        }, 600);

        setTimeout(function() {
            $('body').find('#sg-upImage').remove();
            $('body').find('.sg-upImage-overlay').remove();
            $('body').css({
                "overflow": "auto"
            });

        }, 900);
    })

    /*********
     *Update Image
     **********/
    $(document).on('click', '.upImageClose', function(event) {
        event.preventDefault();
        var id = $('input[name="jform[id]"]').val();
        var setData = $('#sg-upImage form').find(':input').serializeArray();

        getP = {}
        $.each(setData, function(ind, val) {
            getP[val.name] = getP[val.name] ? getP[val.name] || val.value : val.value;
        })

        $gera = $.parseJSON($('#sgtimages').val());


        $incover = false;

        $.each($gera, function(ind, img) {

            if (getP.id == img.id) {
                $.each(getP, function(vind, vval) {
                    if (vind == 'cover') {
                        if (vval == "1") {
                            $incover = true;
                            $incoverId = getP.id;
                            $inAtt = $('#' + getP.id).find('.sg-attributes');
                            if ($('.sg-image-cover').length == 0) {
                                $inAtt.append('<span class="sg-image-cover"><i class="la la-file-photo-o"></i></span>');
                            } else {
                                $('.sg-image-cover').appendTo($inAtt)
                            }

                        } else {
                            $('#' + getP.id).find('.sg-attributes').find('.sg-image-cover').remove()
                        }
                    }
                    $gera[ind][vind] = vval
                })
            }


        })

        //Criando item capa
        if ($incover) {
            $.each($gera, function(gin, gvl) {
                if (gvl.id != $incoverId) {
                    $gera[gin]['cover'] = 0
                }
            })
        }



        $('#sgtimages').val(JSON.stringify($gera))
    });

    /**************
     Form Update
    **************/

    const formUp = function($imgId, $img) {
        $formJson = $.parseJSON($('#sgtimages').val());
        $output = [];
        $output.push('<div id="sg-upImage">');
        $output.push('<form action="" method="post" class="bounceInRight animated">');
        $output.push('<div class="sg-upImageInner">');

        $.each($formJson, function($key, $image) {

            if ($image.id == $imgId) {
                //Cover
                $cover = $image.cover == 1 ? 'checked' : null;
                $access = $image.access == 1 ? 'checked' : null;
                $output.push('<div class="sg-upImage-header"><h3>Opções</h3><span class="upImageClose upClose"></span></div>');
                $output.push('<div class="sg-upImage-img"><span class="sg-img-h"></span><img src="' + $img + '"/></div>');
                $output.push('<div class="sg-upImage-form">');
                $output.push('<input type="hidden" name="id" value="' + $image.id + '">');
                $output.push('<input type="hidden" name="image" value="' + $image.image + '">');
                $output.push('<div class="form-group">');
                $output.push('<label>Título</label>');
                $output.push('<input type="text" name="title" value="' + $image.title + '">');
                $output.push('</div>');
                $output.push('<div class="form-group">');
                $output.push('<label>Subtítulo</label>');
                $output.push('<input type="text" name="subtitle" value="' + $image.subtitle + '">');
                $output.push('</div>');
                $output.push('<div class="form-group">');
                $output.push('<label>Alt</label>');
                $output.push('<input type="text" name="alt" value="' + $image.alt + '">');
                $output.push('</div>');
                $output.push('<div class="form-group">');
                $output.push('<label>Descrição</label>');
                $output.push('<textarea name="description">' + $image.description + '</textarea>');
                $output.push('</div>');
                $output.push('<div class="form-check">');
                $output.push('<input type="checkbox" id="cover" class="sg-check" name="cover" value="1" ' + $cover + '>');
                $output.push('<label class="form-check-label" for="cover">Capa <span class="s-checked"></span></label>');
                $output.push('<input type="hidden" name="cover" value=0>');
                $output.push('</div>');
                $output.push('<div class="form-check">');
                $output.push('<input type="checkbox" id="access" class="sg-check" name="access" value="1" ' + $access + '>');
                $output.push('<label class="form-check-label" for="access">Restrito <span class="s-checked"></span></label>');
                $output.push('<input type="hidden" name="access" value=0>');
                $output.push('</div>');
                $output.push('</div>');
            }
        });
        $output.push('</div>');
        $output.push('</form>');
        $output.push('</div>');
        $output.push('<div class="sg-upImage-overlay upImageClose fadeIn animated"></div>');

        $output = $output.join('');

        if ($('#sg-upImage').length != 0) {
            $('body').find('#sg-upImage').remove();
            $('body').find('.sg-upImage-overlay').remove();
        } else {
            $('body').prepend($output);
            $('body').css({
                "overflow": "hidden"
            });
        }
    }

    //Carregando as imagens preload

    const imgsLoad = function($search = ''){

      $gera = $.parseJSON($('#sgtimages').val());
      $outImg = '';
      $folderId = $('input[name="jform[id]"]').val();
      $folder = '/images/s7dgallery/gal-'+$folderId+'/thumbs/';

      
      $.each($gera,function(ind,$image){
        //Seach
        $search = RegExp($search,'gim');
        
        if(ntext($image.title).match($search)){
            $imgs = '<img src="'+$folder+$image.image+'">';
            $outImg = $imgs;
            $output = '<li id="'+$image.id+'">';
            $output +='<div class="sg-images-inner">'
            $output += '<span class="sg-img-top">'
            $output += '<div class="upImage" data-imageId="'+$image.id+'"></div>'
            $output += '<div class="sortImage"><i class="la la-arrows"></i></div>'
            $output += '</span>'
            $output += $imgs;
            $output += '<span class="selectImage"></span>'
            $output += '<span class="sg-attributes">'
              if($image.cover == 1){
                $output += '<span class="sg-image-cover"><i class="la la-file-photo-o"></i></span>'
              }
              if($image.access == 1){
                $output += '<span class="sg-image-access"><i class="la la-user-times"></i></span>'
              }
            $output += '</span>'
            $output += '</div>'
            $output += '</li>'

            $('.sg-images ul').append($output);

            $($imgs).load(function(){
                $('#'+$image.id).css('transform','translate(0, 0px) scale(1)')
            })

          }

      })


      if($('.sg-load').length == 0 && $('.sgUpImage').css('display') != 'block'){
        $load = '<div class="sg-load"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle></svg></div></div>';
        $('.sg-images').prepend($load);
    }

        $($outImg).load(function(){

        if($('.sg-images ul').html() != ''){
           $('.sg-load').fadeOut(function(){
              $(this).remove();
           })
        } 
    })
        if($('.sg-images ul').html() == ''){
            $('.sg-images').find('#sortable').append('<div class="sg-img-empty">Nenuma imagem encontrada.</div>');
             $('.sg-load').fadeOut(function(){
              $(this).remove();
            })
          }
 

          if($('.sgUpImage').css('display') == 'block'){
            $('.sg-img-empty').remove();
          }

           if($('.sg-search').val() != ''){
                $('.sortImage').hide();
              }else{
                $('.sortImage').show();
              }
        
    }


    //Delete e update
    const delUp = function(id,setData,type = 'delete'){
      $.ajax({
        url: "index.php?option=com_s7dgallery&task=s7dimages.updateImage",
        type: "POST",
        data: {itemId: id, dataSet: setData},
        beforeSend: function(){

        },
        success: function(response){
          $data = JSON.parse(response);

          if($data.status && type == 'delete')
          {
              //remove load
              $('.sg-load').remove();
              $('.imgCount').html('('+JSON.parse(setData).length+')');
          }
        }
      });

    }
    //scroll
    const iCnav = function() {
        var sitem = $('.sgUpImage');
        if (sitem.length) {
            var a = $(window).scrollTop();
            var b = sitem.offset().top;
            off = parseInt(b - a);
            if (off < 0) {
                if ($('.sFixed').length == 0) {
                    $('body').append('<span class="sFixed"></span>');
                    sitem.clone().appendTo('.sFixed');
                }
            } else {
                $('.sFixed').remove();
            }
        }
    }
typingTimer = '';
    //Pesquisar
    $(document).on('keyup','.sg-search',function(){

        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, 500);
    })

    function doneTyping(val){
        $('.sg-images ul').html('');
        imgsLoad($('.sg-search').val());
    }

    $(window).scroll(function() {
        iCnav()
    })

    $(document).on('click', '.sFixed', function(ev) {
        ev.preventDefault();
        $(this).find('.sgBtnUpload').change()
    })

    function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
    }

    //Texto sem acento
    const ntext = function(ntext){
        ntext = ntext.replace(new RegExp('[ÁÀÂÃ]','gi'), 'a');
        ntext = ntext.replace(new RegExp('[ÉÈÊ]','gi'), 'e');
        ntext = ntext.replace(new RegExp('[ÍÌÎ]','gi'), 'i');
        ntext = ntext.replace(new RegExp('[ÓÒÔÕ]','gi'), 'o');
        ntext = ntext.replace(new RegExp('[ÚÙÛ]','gi'), 'u');
        ntext = ntext.replace(new RegExp('[Ç]','gi'), 'c');
        return ntext;                    
    }

})(jQuery)


//nandinha309
/*
var dat = [
 {
  "id":1,
  "nome":"jaca 1"
 },{
  "id":2,
  "nome":"jaca 2"
 },
 {
  "id":3,
  "nome":"jaca 3"
 },
 {
  "id":4,
  "nome":"jaca 4"
 }
]

fil = [1,2,3,4]

$.each(fil,function(vi,vl){
  dat = dat.filter((el) => {
  return el.id !== vl;
});
})

  
oi = JSON.stringify(dat)
$('body').html(oi)


//IN Array Equivalente ao php
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}*/