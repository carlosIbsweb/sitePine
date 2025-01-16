
/**
 * Registro Cavernoma
 * Versão: 1.0
 * Autor: José Carlos
 * Data de Criação: 18 de setembro de 2023
 * Descrição: Este é um projeto JavaScript incrível que faz coisas incríveis.
 * 
 * Direitos Autorais (c) 2023 IBS WEB
 * Este código é licenciado sob a Licença MIT.
 */

var currentTab = 0;
showTab(currentTab);

function showTab(n) {
  var tabs = document.getElementsByClassName("tab-pane");
  for (var i = 0; i < tabs.length; i++) {
    if (i === n) {
      tabs[i].classList.add("active", "in");
    } else {
      tabs[i].classList.remove("active", "in");
    }
  }
  var prevBtn = document.getElementById("prevBtn");
  var nextBtn = document.getElementById("nextBtn");

  if (n === 0) {
    prevBtn.style.display = "none";
  } else {
    prevBtn.style.display = "inline";
  }

  if (n === tabs.length - 1) {

    if(document.querySelectorAll('.cadastro-cavernoma-etapas').length){
      nextBtn.style.display = "none";
    }
    
    nextBtn.querySelector('a').innerText = 'Finalizar e próximo'
    
    jQuery(function($){
        let linkDepois = $('.breadcrumb-cavernoma li a.active').parent('li').prev('li').find('a')
        alert(linkDepois)
        $('#nextBtn').find('a').attr('href',linkDepois.attr('href'))
      })

    let tabAtivo = document.querySelector('.breadcrumb-cavernoma').querySelector('.active')
    let linkAtivo = tabAtivo.parentNode.previousElementSibling

    if(!linkAtivo){
      nextBtn.querySelector('a').innerText = 'Finalizar cadastro'
    }

    nextBtn.querySelector('a').addEventListener('click',function(){
      

      if(linkAtivo){
        if(!validateForm(false)){
          return false;
        }
        //linkAtivo.querySelector('a').click()
        
      }else{ 
        document.querySelector('.breadcrumb-cavernoma').querySelector('li:last-of-type').querySelector('a').click()
      }
    })
     prevBtn.style.display = "none";
  } else {
    nextBtn.style.display = "inline";
  }

  var navTabs = document.getElementsByClassName("nav-tabs")[0];
  var tabLinks = navTabs.getElementsByTagName("a");
  for (var i = 0; i < tabLinks.length; i++) {
    if (i === n) {
      tabLinks[i].parentNode.classList.add("active");
    } else {
      tabLinks[i].parentNode.classList.remove("active");
    }
  }
}

function ajaxDados() {
  jQuery(function($) {

    let formData = new FormData();
    let dados = $('.tab-pane.active').find('form').serializeArray();
    let form = $('[data-form]').attr('data-form');

    $.each(dados, function(i, val) {
      formData.append(val.name, val.value);
    });

    formData.append('formName', form);

    $.ajax({
      url: '/index.php?option=com_cavernoma&task=ajaxRequest&format=raw',
      type: 'POST',
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      success: function(response) {
        let dado = JSON.parse(response)
        if(dado.status == false){
           window.location.href = '/entrar';
        }

        $('.dados').html(response);
      }
    });

  });

}

/*
  *Carregar os dados do usuário logado
*/
function dadosUser()
{
  jQuery(function($) 
  {
    let formData = new FormData();
    let dados = $('.tab-pane.active').find('form').serializeArray();
    //console.log(dados)
    let form = $('[data-form]').attr('data-form');

    $.each(dados, function(i, val) {
      formData.append(val.name, val.value);
    });

    formData.append('formName', form);

    $.ajax({
      url: '/index.php?option=com_cavernoma&task=ajaxDados&format=raw',
      type: 'POST',
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      success: function(response) {
        dadosForm(response)

        gerarArrayValues()
      }
    });
  });
}

jQuery(function($) {
  $(document).ready(function() {
    dadosUser()
  })
})

function changeTab(n) {
  var tabs = document.getElementsByClassName("tab-pane");
  if (n === 1 && !validateForm()) return false;

  //Salvar os dados 
  ajaxDados();

  var nextTab = currentTab + n;
  if (nextTab >= 0 && nextTab < tabs.length) {
    tabs[currentTab].classList.remove("active", "in");
    currentTab = nextTab;
    showTab(currentTab);
  }

}

function validateForm(alerta = true) {
  var tabs = document.getElementsByClassName("tab-pane");
  //var fields = tabs[currentTab].querySelectorAll("[required]");
  var valid = true;
  // Obtenha os elementos de entrada dentro do formulário na aba atual
  var form = document.querySelector("#tab" + (currentTab+1)).querySelector('form')

  if (form) {
          var elementos = form.elements;

          var elementosValidos = [];

          for (var i = 0; i < elementos.length; i++) {
              var elemento = elementos[i];
              if (!elemento.classList.contains('naoValidar')) {
                  elementosValidos.push(elemento);
              }
          }

          // Função para validar se um campo é vazio
          function isEmpty(value) {
              return value.trim() === '';
          }

          //função para adicionar a classe de obrigatório no campo form-gorup
          function campoObrigatorio(name){
            return parents(document.querySelector('[name="'+name+'"]'),'.form-group');
          }

          // Função para validar campos de radio e checkbox
          function validateRadioCheckbox(name) {
              var radioCheckbox = form.querySelectorAll('[name="' + name + '"]:checked');
              return radioCheckbox.length > 0;
          }

          // Função para validar o formulário
              for (var i = 0; i < elementosValidos.length; i++) {
                  var el = elementosValidos[i];

                  if (el.type !== "submit") {
                    if(isEmpty(el.value)){
                      campoObrigatorio(el.name).classList.add('campoObrigatorio')
                      if(alerta) 
                        alert("Por favor, preencha todos os campos.");
                      return false; // Impede o envio do formulário
                    }else{
                      //campoObrigatorio(el.name).classList.remove('campoObrigatorio')
                    }
                      
                  }

                  if (el.type === "radio" || el.type === "checkbox") {
                      // Valida campos de radio e checkbox
                      if (!validateRadioCheckbox(el.name)) {
                            if(campoObrigatorio(el.name))
                                campoObrigatorio(el.name).classList.add('campoObrigatorio')
                          if(alerta)
                          alert("Por favor, selecione uma opção para o campo");
                          return false;
                      }else{
                        if(campoObrigatorio(el.name))
                            campoObrigatorio(el.name).classList.remove('campoObrigatorio')
                      }
                  }

                  // Se você quiser validar selects (elementos <select>)
                  if (el.tagName === "SELECT" && isEmpty(el.value)) {
                    if(campoObrigatorio(el.name))
                    campoObrigatorio(el.name).classList.add('campoObrigatorio')
                      if(alerta)
                      alert("Por favor, selecione uma opção para o campo");
                      return false;
                  }else{
                    if(campoObrigatorio(el.name))
                    campoObrigatorio(el.name).classList.remove('campoObrigatorio')
                  }
              }

              // Se todos os campos foram preenchidos, o formulário pode ser enviado
              return true;

}
}






jQuery(function($) {
  $('.nav-tabs a').on('click', function(ev) {
    ev.stopPropagation()
  })
  $("#main-form").on("submit", function(e) {
    if (!validateForm()) {
      e.preventDefault();
    }
  });
})


function dadosForm(jsonData) {
  try {
    // Analisar o JSON e pegar o primeiro objeto dentro do array, se existir
    var data = JSON.parse(jsonData)[0];

    if (data) {
      // aIterar pelas chaves do objeto
      let ik = 0
      for (var key in data) {
        if (data.hasOwnProperty(key)) {
          var valor = data[key];
          var elementos;

          // Verificar se existe um campo no formulário com colchetes "[]"
          var nomeCampoFormulario = key + "[]";
          elementos = document.getElementsByName(nomeCampoFormulario);

          if (elementos.length === 0) {
            // Se não houver campos com colchetes, tente encontrar o campo sem colchetes
            elementos = document.getElementsByName(key);
          }

          let dados = document.querySelectorAll('[data-name="'+key+'"]')
            
          if(dados.length)
          {
              valor = eval(valor)
              valor.forEach(function(v,i){
              for(let iv in v){
                if(dados[ik])
                  dados[ik].value = v[iv]
                  ik++
              }
            })
          }

          for (var i = 0; i < elementos.length; i++) {
            var elemento = elementos[i];
            if (elemento) {
              // Verificar o tipo de elemento e preenchê-lo adequadamente
              if (elemento.type === "text" || elemento.type === "textarea" || elemento.type === "select-one" || elemento.type === "email" || elemento.type === "number") {
                elemento.value = valor;
              } else if (elemento.type === "checkbox") {
                var valoresCheck = valor;

                // Marcar o campo de checkbox apenas se seu valor estiver na lista de valores
                elemento.checked = valoresCheck.includes(elemento.value);
                console.log(valoresCheck)
              } else if (elemento.type === "radio") {
                // Tratar campos de input radio
                //elemento.checked = (elemento.value === valor.toString());
                var valoresCheck = valor;

                // Marcar o campo de checkbox apenas se seu valor estiver na lista de valores
                elemento.checked = valoresCheck.includes(elemento.value);
                
              } else if (elemento.type === "date") {
                // Tratar campos de data
                elemento.valueAsDate = new Date(valor); // Supondo que o valor seja uma data válida no formato ISO (por exemplo, "YYYY-MM-DD")
              }
            }
          }
        }
      }
    } else {
      console.warn("O JSON está vazio ou não contém objetos.");
    }
  } catch (error) {
    console.error("Erro ao preencher campos com JSON: " + error);
  }
}


function verificarCampo(obj, path) {
    var pathArray = path.split('[').map(function (item) {
        return item.replace(']', '');
    });

    var atual = obj;

    for (var i = 0; i < pathArray.length; i++) {
        var prop = pathArray[i];
        if (atual.hasOwnProperty(prop)) {
            atual = atual[prop];
        } else {
            return false;
        }
    }

    return true;
}

//Gerar novos campos para o cadastro de array para planilha: 07-01-2024
function gerarArrayValues() {
  let grupoArray = document.querySelectorAll('.grupo-array');

  function gera() {
    grupoArray.forEach(function (item) {
      let itemName = item.querySelector('input').getAttribute('name').replace('[]', '');
      let items = item.querySelectorAll('input:not([class="input-array-values"])');

      let grupos = Array.from(items).map(itemInput => itemInput.checked ? itemInput.value : 0);

      // Verifica se já existe um elemento .input-array-values
      let inputExistente = item.querySelector('.input-array-values');

      if (inputExistente) {
        // Atualiza o valor do elemento existente
        inputExistente.value = JSON.stringify(grupos);
      } else {
        // Cria um novo elemento apenas se não existir
        let novoInput = document.createElement('input');
        novoInput.setAttribute('type', 'hidden');
        novoInput.value = JSON.stringify(grupos);
        novoInput.name = itemName;
        novoInput.classList.add('input-array-values');

        item.appendChild(novoInput);
      }
    });
  }

  gera();

  grupoArray.forEach(function (item) {
    item.addEventListener('change', function () {
      gera();
    });
  });
}

document.addEventListener('DOMContentLoaded',function(){
  gerarArrayValues();
})

function parents(elemento, seletor) {

    if(!elemento) {
      return false
    }
    var pai = elemento.parentNode;
    
    while (pai !== null) {
        // Verificar se a propriedade matches está disponível
        var matches = pai.matches || pai.msMatchesSelector || pai.webkitMatchesSelector;

        if (matches && matches.call(pai, seletor)) {
            // O elemento pai corresponde ao seletor
            return pai;
        }

        pai = pai.parentNode;
    }

    // Nenhum pai correspondente encontrado
    return null;
}



