jQuery(function($){
  $(document).ready(function() {
  var buildWrap = document.querySelector('.build-wrap'),
    renderWrap = document.querySelector('.render-wrap'),    
    editBtn = document.getElementById('edit-form'),
    formData = window.sessionStorage.getItem('formData'),
    editing = false,
    fbOptions = {
      dataType: 'json'
    };
  if($("#jform_params_formdata").val() != ''){
    fbOptions.formData = JSON.parse($("#jform_params_formdata").val());
  }
  


  var toggleEdit = function() {
    document.body.classList.toggle('form-rendered', editing);
    //editing = !editing;
  };

  var formBuilder = $(buildWrap).formBuilder(fbOptions).data('formBuilder');

  $('.wkcontact').hover(function() {
    toggleEdit();
    $(renderWrap).formRender({
      dataType: 'json',
      formData: formBuilder.formData
    });

    $(".render-wrap label").each(function(){
      var cRequired = $.trim($( this ).clone().find('.required').remove().end().text().replace(/ /g, "").length);
      if(cRequired == 0)
      {
        $( this ).remove();
      }
    });
    

    $("#jform_params_formdata").val(JSON.stringify(formBuilder.formData));
    $("#jform_params_form").val($(".render-wrap").html());
  });

 

  editBtn.onclick = function() {
    toggleEdit();
  };  

//add class general
$(".wkcontact").prev().parent('.control-group').addClass('wkform');

//colunas
$(".addcol").click(function(){
  if($('.wkcolin').length == 0){
    $(".wkcol").fadeIn('slow');
    $(".wkcol").append('<div class="wkcolin"><ul><li><input type="text" class="wkinputcol" value="'+$('#jform_params_cols').val()+'" /><span onclick="Joomla.submitbutton(\'module.apply\')" class="wkinputok">Salvar</span></li></ul></div>');
  }else{
    $(".wkcol").fadeOut('slow');
     setTimeout(function(){
      $(".wkcol .wkcolin").remove();
    },800);
  };

  $( "#jform_params_cols" ).val($( ".wkinputcol" ).val());

  $( document ).on("keyup click",".wkinputcol",function(){
    $( "#jform_params_cols" ).val($( this ).val());
  });

  $( document ).on("click",".wkinputok", function(){
    $(".wkcol").fadeOut("slow");
    setTimeout(function(){
      $(".wkcol .wkcolin").remove();
    },800);
    
  });
  
});

var href = $(location).attr('href');
  //Selecionando o type.
  $( document ).on("change","#jform_params_wkContactType",function(){
    var modType = $( this ).val();
    if(modType == 'modal'){
      if($("#getWkParams").data('id') == ''){
        $(this).parent('.controls').after('<div id="wkModalType" class="wkModalTypeError">Para utilizar em modal primeiro você precisa salvar.</div>');
      }else{
        $(this).parent('.controls').after('<div id="wkModalType" class="wkModalType">&lta href="#" class="wkcontactbtnmodal" data-formid="'+$("#getWkParams").data('id')+'"&gtTitle WK Contact Modal&lt/a&gt</div>');
      }
      $("#wkModalType").hide().fadeIn("slow");
      $(".wkTypeDesc").parent('.spacer').parent('.control-label').parent('.control-group').fadeIn("slow");
    }else{
      $("#wkModalType").fadeOut("slow");
      $(".wkTypeDesc").parent('.spacer').parent('.control-label').parent('.control-group').fadeOut("slow");
      setTimeout(function(){
        $("#wkModalType").remove();
      },600);
    }
  });


  if($("#jform_params_wkContactType option[selected]").val() == 'modal'){
    $("#jform_params_wkContactType").parent('.controls').after('<div id="wkModalType" class="wkModalType">&lta href="#" class="wkcontactbtnmodal" data-formid="'+$("#getWkParams").data('id')+'"&gtTitle WK Contact Modal&lt/a&gt</div>');
    $("#wkModalType").hide().fadeIn("slow");
    $(".wkTypeDesc").parent('.spacer').parent('.control-label').parent('.control-group').show();
  }else{
    $(".wkTypeDesc").parent('.spacer').parent('.control-label').parent('.control-group').hide();
  }

  if($("input[name='jform[params][mailuserativ]']:checked").val() == '0'){
    $("#myTabTabs li a[href$='#attrib-wkbodyuser']").parent("li").hide();
    $(".paramsWkMailUser").parent('.spacer').parent('.control-label').parent('.control-group').hide();
      $("#jform_params_mailuser").parent('.controls').prev('.control-label').parent('.control-group').hide();
      $("#jform_params_subjectuser").parent('.controls').prev('.control-label').parent('.control-group').hide();
  }

  $( document ).on("change","input[name='jform[params][mailuserativ]']", function(){
    if($(this).val() == '0'){
      $("#myTabTabs li a[href$='#attrib-wkbodyuser']").parent("li").fadeOut("slow");
      $(".paramsWkMailUser").parent('.spacer').parent('.control-label').parent('.control-group').fadeOut("slow");
      $("#jform_params_mailuser").parent('.controls').prev('.control-label').parent('.control-group').fadeOut("slow");
      $("#jform_params_subjectuser").parent('.controls').prev('.control-label').parent('.control-group').fadeOut("slow");
    }else{
      $("#myTabTabs li a[href$='#attrib-wkbodyuser']").parent("li").fadeIn("slow");
      $(".paramsWkMailUser").parent('.spacer').parent('.control-label').parent('.control-group').fadeIn("slow");
      $("#jform_params_mailuser").parent('.controls').prev('.control-label').parent('.control-group').fadeIn("slow");
      $("#jform_params_subjectuser").parent('.controls').prev('.control-label').parent('.control-group').fadeIn("slow");
    }
    
  });

  if($('#jform_params_bodyadmin').val() == ''){
    $("#jform_params_bodyadmin").val('Este é um e-mail de consulta via {site} enviado por:<br />{name}. &lt;email&gt; <br /><br />{message}');
  }

});

$( window ).load(function(){
  var output = $('#menu');
  $(window).on('scroll', function () {
    if($(window).width() > 767)
    {
      var sum = ($(".subhead").outerHeight() + $(".navbar").outerHeight()) - 10;
    }
    else
    {
      var sum = 0;
    }
  
    var scrollTop     = $(window).scrollTop(),
        elementOffset = $('.build-wrap').offset().top,
        distance      = (elementOffset - scrollTop);

        if(distance < 30){
          $("#frmb-0-cb-wrap").css({"margin-top": (distance * -1) + sum})
        }else{
          $("#frmb-0-cb-wrap").css({"margin-top": 0})
        }

   
}); 

});
});
