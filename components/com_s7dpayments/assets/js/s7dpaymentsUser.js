
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

    jQuery(function($){
        $( document ).on('keyup','#username',function(){
            $( this ).val($(this).val().toLowerCase());
        })
    })