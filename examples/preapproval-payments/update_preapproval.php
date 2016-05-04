<?php
require_once "../../vendor/autoload.php";

$mp = new MP("CLIENT_ID", "CLIENT_SECRET");

$id = "PREAPPROVAL_ID";

$preapprovalPayment_data = array(
    "reason" => "Suscripción mensual",
    "external_reference" => "OP-1234",
    "auto_recurring" => array(
        "transaction_amount" => 60,
    )
);

$preapprovalPayment = $mp->update_preapproval_payment($id, $preapprovalPayment_data);

?>
