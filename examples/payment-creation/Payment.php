<?php

use MercadoPago\Sdk\Models\AdditionalInfo;
use MercadoPago\Sdk\Models\Item;
use MercadoPago\Sdk\Models\Payer;
use MercadoPago\Sdk\Models\PaymentData;
use MercadoPago\Sdk\Models\Shipments;
use MercadoPago\Sdk\MP;

class Payment
{
    /**
     * According to https://www.mercadopago.com.br/developers/pt/api-docs/custom-checkout/create-payments/
     * 
     */
    public function doPay()
    {
        $payment_data = new PaymentData();

        $payment_data->transaction_amount = (double) 100;
        $payment_data->description        = "First Order";
        $payment_data->installments       = 1;
        $payment_data->payment_method_id  = 1;
        $payment_data->notification_url   = "url.net/notification";

        $payment_data->token = 'generated_payment_token';

        $payer = new Payer;
        $payer->first_name = "First Name";
        $payer->last_name = "LastName";
        $payer->registration_date = (new \DateTime())->format(\DateTime::ATOM);
        $payer->address = [
            'street_name'   => "Street name",
            'street_number' => "200",
            'zip_code'      => "zip code"
        ];

        $items[] = new Item([
            'id'          => 'product_id',
            'title'       => 'Product title',
            'description' => 'Product description',
            'picture_url' => 'product url',
            'quantity'    => 1,
            'unit_price'  => 100
        ]);

        $shipments = new Shipments;
        $shipments->receiver_address = [
            "zip_code"      => 'zip code',
            "street_name"   => 'street name',
            "street_number" => 200
        ];

        $additionalInfo = new AdditionalInfo(
            $items,
            $payer,
            $shipments
        );

        $payment_data->additional_info = $additionalInfo;

        $mp = new MP(self::ACCESS_TOKEN);
        $return = $mp->post("/v1/payments", json_encode($payment_data));

        echo json_encode($return);
    }

    const ACCESS_TOKEN = "LONG_LIVE_ACCESS_TOKEN";
}