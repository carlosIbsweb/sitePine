jQuery(function($) {
    $(document).ready(function() {
        let scanner = new Instascan.Scanner({ 
            video: $('#preview')[0],
            mirror: false 
        });


        scanner.addListener('scan', function(qrCodeUrl) {
            // Se o loading estiver visível, não processa outro QR Code
            if ($('#loading').is(':visible')) {
                console.log("Processamento em andamento. Ignorando nova leitura.");
                return;
            }

            console.log("QR Code Lido:", qrCodeUrl);

            qrCodeUrl = 'https://pinetreefarm.com.br/index.php?option=com_ajax&module=checkin&method=items&format=json&code=' + qrCodeUrl;
            
            // Exibir loading enquanto processa
            $('#loading').show();

            // Fazer requisição AJAX para a URL do QR Code
            $.ajax({
                url: qrCodeUrl,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    $('#loading').hide(); // Esconder loading

                    // Se houver erro, exibe alerta
                    if (data.data.error) {
                        alert(data.data.error);
                        return;
                    }

                    // Se for sucesso, carrega a página de check-in
                    if (data.data.success) {
                        $('.check').load('modules/mod_checkin/tmpl/_checkin.php', function() {
                            exibeOcultaCheck()
                            let dado = data.data
                           
                           $('.check-success').html(dado.success)
                           $('.check-nome').html(dado.crianca.nome)
                           $('.check-colonia').html(dado.colonia.colonia)
                           $('.check-ingresso').html(dado.course)
                           $('.check-semana').html(dado.colonia.semana)
                           $('.check-periodo').html(dado.colonia.periodo)
                           $('.check-possuidoenca').html(dado.crianca.possuidoenca)
                           $('.check-medicamento').html(dado.crianca.medicamento)
                           $('.check-alergia').html(dado.crianca.alergia)
                           $('.check-nascimento').html(dado.crianca.nascimento)
                           $('.check-autorizada').html(dado.crianca.autorizada)

                           //Responsável
                           $('.check-responsavel-nome').html(dado.responsavel.name)
                           $('.check-responsavel-email').html(dado.responsavel.email)
                           $('.check-responsavel-telefone').html(dado.responsavel.telefone)
                           $('.check-responsavel-cpf').html(dado.responsavel.cpf)

                           
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading').hide(); // Esconder loading
                    console.error("Erro:", error);
                    alert("Erro ao processar o QR Code.");
                }
            });
        });
        startScan()
        $('#startScan').on('click', function() {
            startScan()
        });

        $( document ).on('click','.novo-checkin',function(){
            exibeOcultaCheck()
        })

        function startScan()
        {
            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length === 0) {
                    alert("Nenhuma câmera encontrada!");
                    return;
                }

                let backCameras = cameras.filter(camera => camera.name.toLowerCase().includes('back') || camera.name.toLowerCase().includes('traseira'));
                let bestCamera = backCameras.length > 0 ? backCameras[0] : cameras[0];

                scanner.start(bestCamera);
                $('#preview').show(); // Exibir a câmera
            }).catch(function(error) {
                console.error("Erro ao acessar a câmera:", error);
            });
        }

        function exibeOcultaCheck()
        {
            let startCheckin = $('.start-checkin')
            let check = $('.check')

            if(startCheckin.css('display') === 'none'){
                startCheckin.show();
                check.hide();
            }else{
                startCheckin.hide();
                check.show().css('display','flex')
            }
        }
        
    });

});
