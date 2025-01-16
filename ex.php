<?php
$url = 'https://api.pagar.me/core/v5/orders';

$data = array(
    'items' => array(
        array(
            'amount' => 1,
            'description' => 'Chaveiro do Teressact',
            'quantity' => 1
        )
    ),
    'customer' => array(
        'name' => 'Tony Stark'
    ),
    'payments' => array(
        array(
            'payment_method' => 'checkout',
            'checkout' => array(
                'expires_in' => 120,
                'billing_address_editable' => false,
                'customer_editable' => true,
                'accepted_payment_methods' => array('credit_card', 'boleto', 'pix'),
                'success_url' => 'https://www.pagar.me',
                'boleto' => array(
                    'bank' => '033',
                    'instructions' => 'Pagar até o vencimento',
                    'due_at' => '2025-07-25T00:00:00Z'
                ),
                'credit_card' => array(
                    'installments' => array(
                        array(
                            'number' => 1,
                            'total' => 3000
                        ),
                        array(
                            'number' => 2,
                            'total' => 3000
                        )
                    )
                ),
                'payment_method' => 'pix',
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
    )
);

$username = 'sk_nNOkOA7UvVuLVBdW';
$password = 'Sofia707+';

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

header("Location: $url");
exit();

