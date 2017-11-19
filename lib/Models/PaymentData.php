<?php

namespace MercadoPago\Sdk\Models;

class PaymentData
{
    public $transaction_amount;
    public $token;
    public $description;
    public $installments;
    public $payment_method_id;
    public $payer;
    public $external_reference;
    public $notification_url;
    public $sponsor_id;
    public $statement_descriptor;
    public $additional_info;
    public $date_of_expiration;
}

