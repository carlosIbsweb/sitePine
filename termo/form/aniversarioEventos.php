<?php

foreach ($_POST as $key => $value) {
    $$key = $value;
}

//Aqui queridos vai chamar o arquivo do file com o name em array, em (array). rsrsrsrs
//$arquivo = $_FILES['file'];

//Aqui vou estanciar minha classe para enviar o meu php. rsrssr
require 'enviar/PHPMailerAutoload.php';

$emailRemetente     = 'coloniapinetreefarm@gmail.com';
$nomeRemetente      =  'Pine Tree Farm';
$assunto            = 'Aniversários e Eventos';
$emailDestinatario  = 'coloniapinetreefarm@gmail.com';
$nomeDestinatario   = 'Pine Tree Farm';

//Aqui vai ser o corpo do e-mail;
$mensagem ='<html>
<head>
<title>Aniversários e Eventos - Pine Tree Farm</title>
</head>
<body>

<div style=
"
    border: 1px solid #eee;
    width: 90%;
    padding: 10px;
    font-size: 14px;
"
>
    <p style="font-size: 14px; padding: 8px; background-color: #00a161; color: #FFFFFF;">Dados do Responsável</p>
        <p><strong style="color: #412D0A">Nome: </strong>'.$nome_responsavel.'<p>
        <p><strong style="color: #412D0A">Telefone: </strong>'.$telefone_responsavel.'<p>
        <p><strong style="color: #412D0A">E-mail: </strong>'.$email_responsavel.'<p>                
        
    <p style="font-size: 14px; padding: 8px; background-color: #00a161; color: #FFFFFF;">Dados da Criança</p>
       <p><strong style="color: #412D0A">Nome: </strong>'.$nome_crianca.'<p>
        <p><strong style="color: #412D0A">Idade da criança: </strong>'.$datan_crianca.'<p>
        <p><strong style="color: #412D0A">Nome da Escola: </strong>'.$escola_crianca.'<p>

        <p style="font-size: 14px; padding: 8px; background-color: #00a161; color: #FFFFFF;">Dados do Evento</p>
        <p><strong style="color: #412D0A">Dia da festa: </strong>'.$data_evento.'<p>
       <p><strong style="color: #412D0A">Nº de crianças: </strong>'.$n_criancas.'<p>
       <p><strong style="color: #412D0A">Nº de adultos: </strong>'.$n_adultos.'<p>
       <p><strong style="color: #412D0A">Hora de início: </strong>'.$h_inicio.'<p>
       <p><strong style="color: #412D0A">Tema: </strong>'.$tema.'<p>
        <p style="font-size: 14px; padding: 8px; background-color: #00a161; color: #FFFFFF;">Opcionais</p>
         <p><strong style="color: #412D0A">Opções: </strong>'.implode(", ", $opcionais).'<p>
    </div>
</body>
</html>
';


$mail = new PHPMailer;

$mail->CharSet = 'UTF-8';

$mail->setFrom($emailRemetente,$nomeRemetente);

//$mail->addReplyTo('carlos@ibsweb.com.br', 'Carlos');

$mail->addAddress($emailDestinatario,$nomeDestinatario);

$mail->Subject = $assunto;

$mail->msgHTML($mensagem);

//Enviar o e-mail.
if ($mail->send())
{
	echo "<script> alert('Enviado com sucesso!')</script>";
    echo"<script> top.location.href=('/index.php')</script>";			
}
else
{
    echo "<script> alert('Não foi possivel cadastrar!')</script>";
    echo "<script> history.back()</script>";
    exit();
}

?>