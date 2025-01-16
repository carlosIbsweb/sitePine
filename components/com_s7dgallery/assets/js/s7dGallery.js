(function ($) {  
    $.fn.paginationImages = function(options) {

      var options = $.extend({
        id : '',
        count : '',
        start : '',
        imgLoader : ''

      }, options)
        $.ajax({
            url: "index.php?option=com_s7dgallery&task=s7dimages.nada",
            type: "POST",
            data: {itemId: options.id,countLoader: options.count,countStart:options.start},
            beforeSend: function(){
              $('.sg-loadMore').hide();
              $('#sg-loadMore').append('<img class="sg-loadClick" src="'+options.imgLoader+'"/>');

            },
            success: function(response){
              var data = $.parseJSON(response);
              $('#s7dGallery').append(data.image);
               
                $("#s7dGallery").justifiedGallery({
                lastRow : 'nojustify',
              });

              $('.sg-loadMore').data('start',data.start);
              
              if(data.count <= data.limit ){
                $('.sg-loadMore').remove();
              }
              $('.sg-loadClick').remove();
            },
            complete: function(){
              setTimeout(function() {
                $('.sg-loadMore').show();
              }, 60); 
            }


          });
    }

})(jQuery)