<?php
/**
 * @package     
 * @subpackage  com_s7dpayments
 **/
//financeiro@depaulanegocios.com.br - email para o pagseguro da Depaula
// No direct access.
defined('_JEXEC') or die;

/********
 Dados do Pagarme
********/


// Define a URL da API do Pagar.me
$url = 'https://api.pagar.me/core/v5/orders';

// Inicializa as variáveis
$data = [];
$amount = 0;

// Obtém o carrinho de compras
$cart = json_decode(paymentsCart::getCart($cartid,'cartid','products')) ?: '[]';

//Ober forma de pagamento
$forma_pagamento = $_POST['forma_pagamento'] ?? false;

//Formas aceitas
$formas_pagamento = $forma_pagamento == '_pix' ? ['pix'] : ['credit_card','boleto'];

// Itera pelos produtos no carrinho
foreach($cart as $k => $items)
{
    $title = $items->course;
    $quantity = 0; 
    foreach($items->criancas as $qnt) {
      $quantity = $quantity += count($qnt);
    }

    // Adiciona os dados do produto ao array de dados
    $data['items'][$k]['quantity'] = $quantity;
    $data['items'][$k]['description'] = '.:: '.$mTitle.' ::. '.$title;
    $data['items'][$k]['amount'] = (int) number_format($items->price, 2, '', '');
    $data['items'][$k]['code'] =   '_item_'.($k+1);

    // Soma o preço do produto ao preço total
    $amount += ($items->price*$quantity);
}

// Formata o preço total em centavos
$amountFormated = (int) number_format($amount, 2, '', '');

// Total pix 3% de desconto
$amountFormated =  $forma_pagamento == '_pix' ? formatarValor($amount - ($amount * (3 / 100))) : $amountFormated;

// Obtém as informações do cliente
$user = JFactory::getUser();
$pref  = explode(")", $user->telefone);
$prefix  = str_replace("(", "", $pref[0]);
$phone = preg_replace("/[^0-9]/", "", $pref[1]);

// Adiciona as informações do cliente ao array de dados
$data['customer'] = array(
    'external_id' => 'REF-'.paymentsCart::getCart($cartid,'cartid','id').'PTF',
    'name' => $user->name,
    'email' => $user->email ?? '',
    'document_number' => $user->cpf ?? '',
    'phone' => array(
        'ddd' => $prefix ?? '',
        'number' => $phone ?? ''
    )
);

// Adiciona as informações do pagamento ao array de dados
$data['payments'] = array(
    array(
        'amount' => $amountFormated,
        'payment_method' => 'checkout',
        //Url de notificação
        'pagarme_hook_url' => 'https://pinetreefarm.com.br/index.php?option=com_s7dpayments&notification',
        'metadata' => array(
            'id_pedido' => 'REF-'.paymentsCart::getCart($cartid,'cartid','id').'PTF'
        ),
        'checkout' => array(
            'expires_in' => 120,
            'billing_address_editable' => false,
            'customer_editable' => true,
            'accepted_payment_methods' => $formas_pagamento,
            'success_url' => 'https://pinetreefarm.com.br/informacoes-importantes/143-informacoes-importantes-t.html',
             "boleto" => array(
             "bank" => "033",
             "instructions" => "Pagar até o vencimento",
             "due_at" => date('Y-m-d').'T00:00:00Z',
            ),
            'credit_card' => array(
                'installments' => array(
                    array(
                        'number' => 1,
                        'total' => $amountFormated  ?? 0
                    ),
                    array(
                        'number' => 2,
                        'total' => formatarValor($amount * 1.0637)  ?? 0
                    ),
                    array(
                        'number' => 3,
                        'total' => formatarValor($amount * 1.0796)  ?? 0
                    ),
                    array(
                        'number' => 4,
                        'total' => formatarValor($amount * 1.0955) ?? 0
                    )
                )
            ),
            'pix' => array(
                'expires_in' => '52134613',
                'additional_information' => array(
                    array(
                        'name' => 'Quantidade',
                        'value' => '2'
                    )
                )
            )
        )
    )
);


//Formatar Valor para o pagarme.
function formatarValor($v){
    return (int) number_format($v, 2, '', '');
}

$username = 'sk_gByP09xSQsJrGn7Q';//'sk_nNOkOA7UvVuLVBdW'; //'sk_test_jxpGrb2t9F5WGYKm';
$password = 'Sofia707+';

if(isset($_POST['formPag']) && !isset($_SESSION['pineVip']) && !isset($_SESSION['error_envio']) && !isset($_POST['formPagVip']) ):
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($username . ':' . $password)
));

$response = json_decode(curl_exec($ch));
curl_close($ch);

$url = $response->checkouts[0]->payment_url;
$reference = $response->checkouts[0]->id;

if($url){
    unset($_SESSION['finalizarPagamento']);
    unset($_SESSION['cartid']);
}else{
    echo implode(',',$response->erros);
    return;
}

?>

<form action="<?= $url;?>" method="get" name="checkout">
  <input type="hidden" name="iot" value="button" />
</form>

<?php endif;?>
