<?php



// Recebe o webhook do Pagar.me
$data = json_decode(file_get_contents('php://input'), true);

$data = $data['data'];
$id_pedido = $data['metadata']['id_pedido'];
$status = $data['status'];

$pagamento = $data['charges'] ?? false;


if(!empty( array_shift(s7dPayments::getPagments('id','ref',$id_pedido))->id )){
    s7dPayments::setCoursesUpdate($id_pedido,$status);
    // Envia uma resposta HTTP 200 (OK) para o Pagar.me
    http_response_code(200);
}


//Fazer algo se existir pagamento 
if($pagamento && $status == 'paid')
{
  $metodo         = $data['charges'][0]['payment_method'];
  $nome           = $data['charges'][0]['customer']['name'];
  $email          = $data['charges'][0]['customer']['email'];
  $obterData      = $data['charges'][0]['updated_at'];
  $valorPagamento = $data['charges'][0]['amount'];

  $dataPagamento = date('d/m/Y',strtotime($obterData));
  $valorPagamento = number_format($valorPagamento/100, 2, ',', '.');



        //Notificação de compra Cliente coloniapinetreefarm@gmail.com,.
        s7dPayments::sendEmail(JPATH_SITE.'/components/com_s7dpayments/tmpl/default_emailClienteFinalizacao.php','Confirmação de pagamento - Pedido '.$id_pedido,'contato@pinetreefarm.com.br','Pine Tree Farm',$email,compact('metodo','nome','dataPagamento','valorPagamento'));


        //Notificação de compra Administrador coloniapinetreefarm@gmail.com,.
        s7dPayments::sendEmail(JPATH_SITE.'/components/com_s7dpayments/tmpl/default_emailAdminFinalizacao.php','Novo pagamento recebido - Pedido '.$id_pedido,'contato@pinetreefarm.com.br','Pine Tree Farm','coloniapinetreefarm@gmail.com,jcarloswk@gmail.com,liliane@ibsweb.com.br',compact('metodo','nome','dataPagamento','valorPagamento','email','id_pedido'));
}

// Retorna uma resposta de sucesso
http_response_code(200);

exit();





/*
chk_Ndeb19liDce9b9nL
// Processa o evento recebido
switch ($data->event) {
  case 'transaction_status_changed':
    // Processa a mudança de status da transação
    processTransactionStatusChanged($data->id, $data->current_status);
    break;
  case 'boleto_paid':
    // Processa o pagamento do boleto
    processBoletoPaid($data->id);
    break;
  default:
    // Evento desconhecido
    http_response_code(400);
    die('Evento desconhecido');
}

// Função para processar a mudança de status da transação
function processTransactionStatusChanged($transactionId, $status) {
  // Aqui você pode executar o código necessário para processar a mudança de status da transação
  // Por exemplo, atualizar o status da transação no banco de dados ou notificar o usuário
}

// Função para processar o pagamento do boleto
function processBoletoPaid($transactionId) {
  // Aqui você pode executar o código necessário para processar o pagamento do boleto
  // Por exemplo, atualizar o status da transação no banco de dados ou notificar o usuário
}

// Retorna uma resposta para o webhook
http_response_code(200);
echo 'OK';


$signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];

// 'payload' é a string que você recebeu no webhook
if (hash_equals($signature, hash_hmac('sha1', $payload, $secret_key))) {
    // Assinatura correta, processa a requisição
} else {
    // Assinatura incorreta, não confie na requisição
}

return;

// Recebe a requisição POST do Pagar.me
$request = file_get_contents('php://input');

// Verifica a autenticidade da requisição
if (isset($_SERVER['HTTP_X_HUB_SIGNATURE']) && $_SERVER['HTTP_X_HUB_SIGNATURE'] == hash_hmac('sha1', $request, $secret_key)) {
  // A requisição é autêntica, processa a notificação
  $data = json_decode($request, true);
  
  // Verifica o status da transação e atualiza os dados do seu sistema
  if ($data['event'] == 'transaction_status_changed') {
    $reference = $data['content']['id'];
    $transaction_status = $data['content']['current_status'];
    
    if(!empty( array_shift(s7dPayments::getPagments('id','ref',$reference))->id )){
      s7dPayments::setCoursesUpdate($reference,$transaction_status);
    }
  }
  
  // Envia uma resposta HTTP 200 (OK) para o Pagar.me
  http_response_code(200);
} else {
  // A requisição não é autêntica, retorna um erro
  http_response_code(401);
}
?>