(function ( $ ) 
{
  $.fn.addb = function( opt )
  {

    // This is the easiest way to have default options.
    var opt = $.extend({
        // These are the defaults.
        max_fields: 10,
        addButon: ".ibsmDm",
        rowName: ".ibsmD" 
    }, opt );
   
   const addb = function(){
        var add_button = $(opt.addButon); //Add button ID
        var conta = $(opt.rowName).length;

        if(conta == 0){
            $(opt.addButon).before('<div class=\"ndnn\">Nenhum dependente</div>');
        }else{
            $('.ndnn').remove();
        }

        var x = 1; //initlal text box count
        $(document).on('click',opt.addButon,function(e) { //on add input button click
            e.preventDefault();

            var conta = $(opt.rowName).length;

            var peg = $(opt.rowName);

            sum = [];
            peg.each(function(){
                sum.push($(this).attr('datadmq'));
            });

            var ndnn = $('.ndnn').length;

            if(conta == 0){
                sm = 0;
                $('.ndnn').remove();
            }else{
                sm = Math.max.apply(null, sum );
                $('.ndnn').remove();
            }

            var number = Math.random() // 0.9394456857981651
number.toString(36); // '0.xtis06h6'
var uId = number.toString(36).substr(2, 9); // 'xtis06h6'
uId.length >= 9; // false
            if (x < opt.max_fields) { //max input box allowed
                x++; //text box increment
                $(opt.addButon).before(
                    '<div class="ibsmD" datadmq="'+uId+'">'+
                        '<div class="form-group field-nomed wk-col-sm-4">'+
                            '<label for="nomed" class="fb-nomed-label">Type<span class="required">*</span> </label>'+
                            '<select class="form-control required" name="'+ uId +'[type]" id="fieldType">'+
                                '<optgroup label="SQL: Date and time" id="optgroup_sql_date">'+
                                '<option value="DATE">DATE</option>'+
                                '<option value="DATETIME">DATETIME</option>'+
                                '</optgroup>'+
                                '<optgroup label="SQL: String" id="optgroup_sql_string">'+
                                '<option value="CHAR">CHAR</option>'+
                                '<option value="VARCHAR">VARCHAR</option>'+
                                '<option value="TINYTEXT">TINYTEXT</option>'+
                                '<option value="TEXT">TEXT</option>'+
                                '<option value="MEDIUMTEXT">MEDIUMTEXT</option>'+
                                '<option value="LONGTEXT">LONGTEXT</option>'+
                                '</optgroup>'+
                            '</select>'+
                        '</div>'+
                        '<div class="form-group">'+
                            '<label for="nomed" class="fb-nomed-label">Name<span class="required"></span> </label>'+
                            '<input type="text" class="form-control" name="'+ uId +'[name]">'+
                        '</div>'+
                        '<div class="form-group field-parentd wk-col-sm-4">'+
                            '<label for="nomed" class="fb-parentd-label">Tamanho<span class="required">*</span> </label>'+
                            '<input type="text" class="form-control" name="'+ uId +'[tamanho]">'+
                        '</div>'+
                        '<a href="#" class="remove_field">Remover</a></div>'
                    );
                }
            }); 
            
            $( document ).on('hover','#toolbar-apply',function(){
                var b = $('#attrib-qwDb').find(':input').serializeJSON();
                //alert(JSON.stringify(b));
            })

            $( document ).on("click", ".remove_field", function(e) {
                e.preventDefault();
                var ndnn = $('.ibsmD').length;
                $(this).parent('div').remove();
                 if(ndnn == 1 ){
                        $('.ibsmDm').before('<div class="ndnn">Nenhum dependente</div>');
                    }
                    else
                    {
                        $('.ndnn').remove();
                    }
                x--;
              })            
            };

            addb();
   }
}(jQuery));
