<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/

// No direct access.
defined('_JEXEC') or die;


JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

defined('_JEXEC') or die;

// Obtém o documento Joomla
$doc = JFactory::getDocument();

// Adiciona CSS do Owl Carousel
$doc->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
$doc->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');

// Adiciona seu CSS personalizado
$doc->addStyleSheet(JURI::base() . 'components/com_s7dpayments/assets/css/usuario.css?'.uniqid()); // Altere para o caminho correto do CSS


// Adiciona a biblioteca de QR Code
$doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js');

// Adiciona o script do Owl Carousel
$doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js');

$doc->addScriptDeclaration('



jQuery(function($){

    $(document).ready(function() {
        // Simulando um banco de dados de pessoas
        const pessoas = '.json_encode(paymentsUser::getColoniaFutura()).'
    
        // Criando os slides do carrossel
        const $slider = $("#qrcode-slider");
    
        $.each(pessoas, function(index, pessoa) {
            let card = $(`
                <div class="qrcode-card">
                    <h5><b>${pessoa.nome_crianca}</b></h5>
                    
                    <div class="qrcode-container" id="qrcode-${index}"></div>
                    <p><h6><b><i class="fa fa-users"></i> ${pessoa.colonia}</b></h6></p>
                    <p><b><i class="fa fa-ticket"></i></b> ${pessoa.course}</p>
                    <p><span class="bg-green"><b><i class="fa fa-calendar"></i> ${pessoa.semana}</b></span></p>
                    <p><span class="color-green"><b><i class="fa fa-clock-o"></i> ${pessoa.periodo_nome || "N/A"}</span></b></p>
                </div>
            `);
    
            $slider.append(card);
        });
    
// Inicializar o Owl Carousel

    
    let carousel = $(".owl-carousel");

    carousel.owlCarousel({
        loop: false,
        margin: 10,
        nav: false,
        items: 1, // Apenas um item por slide
        center: true,
        onInitialized: function(event) {
            generateQRCode(0); // Gera QR Code para o primeiro slide
        },
        onChanged: function(event) {
        	setTimeout(function(){
          let currentIndex = event.item.index; // Obtendo índice correto
            generateQRCode(currentIndex);
          },500)
            
        }
    });

    // Função para gerar os QR Codes dinamicamente
    function generateQRCode(index) {
        // Limpa todos os QR Codes antes de criar um novo
        $(".qrcode-container").html("");

        // Obtém o slide ativo no carrossel
        let activeSlide = $(".owl-item.active .qrcode-container");

        if (activeSlide.length > 0) {
            let qrElement = activeSlide[0];

            // Pegando o ID do QR Code para encontrar os dados corretos
            let id = qrElement.id;
            let idx = parseInt(id.split("-")[1]); // Obtém o índice correto
            
            new QRCode(qrElement, {
                text: `${pessoas[idx].crianca_id}`,
                width: 220,
                height: 220
            });
        }
    }

    //Manuseando menus
    $(".dmstore").html("Minhas Inscrições")
    
});
})
');
?>

<div class="container pine-minhas-inscricoes">
    <h3>Minhas Inscrições</h3>
    <?php if(!count(paymentsUser::getColoniaFutura())):?>
        Nenhuma Inscrição
    <?php endif;?>
    <div class="owl-carousel owl-theme" id="qrcode-slider"></div>
</div>


<?php /*
<div class="row col-md-12">
    <?php print_r(json_encode(paymentsUser::getColoniaFutura()));?>
</div>*/?>