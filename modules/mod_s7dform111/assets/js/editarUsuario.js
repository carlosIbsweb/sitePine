/*dados para o form*/


function dadosFormUpload(jsonData) {
  jQuery(function($){
  try {
    // Analisar o JSON e pegar o primeiro objeto dentro do array, se existir
    var data = JSON.parse(jsonData)[0];

    if (data) {
      // Iterar pelas chaves do objeto
      for (var key in data) {
        if (data.hasOwnProperty(key)) {
          var valor = data[key];
          var elementos;

          if (key == 'aw_anexos') {
            try {
  var jsonObject = JSON.parse(valor);

  for (var chave in jsonObject) {
    if (jsonObject.hasOwnProperty(chave)) {
      var valores = jsonObject[chave];
      var elementos = $('[id^="' + chave + '[]"]');

      for (var i = 0; i < valores.length; i++) {
        if (i < elementos.length && $(elementos[i]).next('.uploadInput').length == 0) {
          $(elementos[i]).css({'display':'none'}).addClass('fileAnexo')
          $(elementos[i]).parent('.awValidMsg').prev('label').attr('for','').addClass('fileAnexo')
          $(elementos[i]).after(`
                <div class="awExibeAnexo">
                    <input name="aw_anexos[][${chave}]" class="uploadInput" type="hidden" value="${valores[i]}" />
                    <span class="uploadInput">${valores[i]}<span class="uploadClose">x</span></span>
                </div>
          `);
        }
      }
    }
  }
} catch (error) {
  console.error("Erro ao analisar JSON: " + error);
}

          }
        }
      }
    } else {
      console.warn("O JSON estÃ¡ vazio ou nÃ£o contÃ©m objetos.");
    }
  } catch (error) {
    console.error("Erro ao preencher campos com JSON: " + error);
  }
  })
}

function dadosForm(jsonData) {
  jQuery(function($){
  try {
    // Analisar o JSON e pegar o primeiro objeto dentro do array, se existir
    var data = JSON.parse(jsonData)[0];

    if (data) {
      // Iterar pelas chaves do objeto
      for (var key in data) {
        if (data.hasOwnProperty(key)) {
          var valor = data[key];
          var elementos; 

  
          // Verificar se existe um campo no formulÃ¡rio com colchetes "[]"
          var nomeCampoFormulario = key + "[]";

          elementos = $('[name="' + nomeCampoFormulario + '"]');
          if (elementos.length === 0) {
            // Se nÃ£o houver campos com colchetes, tente encontrar o campo sem colchetes
            elementos = $('[name="' + key + '"]');
          }

          elementos.each(function() {
            var elemento = $(this);

            if (elemento) {
              // Verificar o tipo de elemento e preenchÃª-lo adequadamente
              if (
                elemento.is('input[type="text"]') ||
                elemento.is('input[type="tel"]') ||
                elemento.is('textarea') ||
                elemento.is('select') ||
                elemento.is('input[type="email"]')
              ) {
                elemento.val(valor);
              } else if (elemento.is('input[type="checkbox"]')) {
                var valoresCheck = valor;

                // Marcar o campo de checkbox apenas se seu valor estiver na lista de valores
                elemento.prop('checked', valoresCheck.includes(elemento.val()));
              } else if (elemento.is('input[type="radio"]')) {
                // Tratar campos de input radio
                elemento.prop('checked', elemento.val() === valor.toString());
              } else if (elemento.is('input[type="date"]')) {
                // Tratar campos de data
                elemento.val(valor); // Supondo que o valor seja uma data vÃ¡lida no formato ISO (por exemplo, "YYYY-MM-DD")
              }
            }
          });
        }
      }
    } else {
      console.warn("O JSON estÃ¡ vazio ou nÃ£o contÃ©m objetos.");
    }
  } catch (error) {
    console.error("Erro ao preencher campos com JSON: " + error);
  }
  })
}


function dadosUser() {
  jQuery(function($){
      // Dados que vocÃª deseja enviar para o servidor
      let formData = new FormData();
      let form = $('.aw-form').attr('id');
      let formDados = form.split('-')
      formData.append('formId', formDados[1]);
      formData.append('formName', form)

      $.ajax({
          url: '/index.php?option=com_ajax&module=s7dform&method=dadosTabelaRelacaoUsuarios&format=raw', // Substitua 'com_seucomponente' e 'suaTarefa' pelos valores corretos do seu componente
          type: 'POST',
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          success: function (response) {

            data = JSON.parse(response)

            if(data.user != false){


            dadosForm(response)
            jsonMultis(response)

            setTimeout(function() {
              gerarMulti('.aw-form-row');
            }, 100);
            


            dadosForm(jsonMultis(response,true))

            setTimeout(function() {
              dadosFormUpload(response)
            }, 100);
            
          }
          }   
      });

  });

    }



jQuery(function($){
  $( document ).ready(function(){
      dadosUser()

      $(document).on('click','.uploadClose',function(ev){
        ev.preventDefault();

        $(this).parents('.form-group').find('.fileAnexo').show()
        $(this).parents('.awExibeAnexo').remove()
      })
  })

})

function dadosFormMultia(json) {

    // Itere sobre as chaves do objeto de chaves
    for (let chave in json) {
        // Encontre a div com a classe correspondente Ã  chave
        const divElement = document.querySelector(`.${chave}`);

        if(document.querySelectorAll(`.${chave}`).length > Object.keys(json).length){
          continue;
        }

        if (!divElement) {
            console.log(`Div com classe "${chave}" nÃ£o encontrada.`);
            continue;
        }

        const arrayDeObjetos = json[chave];


        // Contador para os Ã­ndices
        let index = 1;

        // Itere sobre o array de objetos
        arrayDeObjetos.forEach(obj => {

             // Clone a div original
            const cloneDiv = divElement.cloneNode(true);

            // Insira a div clonada como irmÃ£o apÃ³s a div original
            divElement.insertAdjacentElement('afterend', cloneDiv);
           

            // Incremente o contador
            index++;
        });


        // Remova apenas o primeiro elemento clonado
        const firstClone = document.querySelector(`.${chave} + .${chave}`);

        if (firstClone) {
            firstClone.remove();
            gerarMulti('.aw-form-row');
        }
    }

}

function dadosFormMultJson(json) {

    // Array para armazenar os objetos reformatados
    const resultado = [];

    // Itere sobre as chaves do objeto de chaves
    for (let chave in json) {
      const arrayDeObjetos = json[chave];

      // Itere sobre o array de objetos
      arrayDeObjetos.forEach((obj, index) => {
        const newObject = {};
        for (let key in obj) {
          const value = obj[key];
          const indexa = index + 1;
          newObject[`${chave}[${indexa}][${key}]`] = value;
        }
        resultado.push(newObject); // Adicione o objeto ao resultado
      });
    }

    console.log(resultado);
    return resultado;
}

function jsonMultis(json, soJson){

  let informacoes = ''
  let info = []
  jQuery(function($){

    let multis = []
    $('.multi').each(function(){
      multis.push($(this).data('multi'))
    })

    multis = [...new Set(multis)]


    // Objeto para armazenar as informaÃ§Ãµes extraÃ­das
    const informacoesExtraidas = {};

    // Itera sobre as categorias
    for (const categoria of multis) {
        // Acessa a primeira entrada do array (o Ãºnico objeto do seu JSON)

        const usuario = JSON.parse(json)[0];

        // Extrai as informaÃ§Ãµes da categoria do objeto do usuÃ¡rio
        const informacoes = JSON.parse(usuario[categoria]);

        // Armazena as informaÃ§Ãµes extraÃ­das no objeto de resultado
        informacoesExtraidas[categoria] = informacoes;

        info.push(informacoesExtraidas)
    }

    info = JSON.stringify(info)
      info = JSON.parse(info)[0]

      console.log(info)
    if(soJson){
      informacoes = dadosFormMultJson(info);
    }else{
        dadosFormMultia(informacoesExtraidas);
    }
    
  })
  if(soJson){
    let objetoCombinado = {};

  informacoes.forEach(obj => {
    for (let key in obj) {
      objetoCombinado[key] = obj[key];
    }
  });

  objetoCombinado = [objetoCombinado]

  console.log(objetoCombinado)

  return JSON.stringify(objetoCombinado)
  }
  
}