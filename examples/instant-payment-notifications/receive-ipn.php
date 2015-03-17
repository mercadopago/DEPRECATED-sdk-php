<?php
/**
 * MercadoPago SDK
 * Receive IPN
 * @date 2015/03/17
 * @author fvaccaro
 */
// Include Mercadopago library
require_once "../../lib/mercadopago.php";

// Create an instance with your MercadoPago credentials (CLIENT_ID and CLIENT_SECRET): 
// Argentina: https://www.mercadopago.com/mla/herramientas/aplicaciones 
// Brasil: https://www.mercadopago.com/mlb/ferramentas/aplicacoes
// Mexico: https://www.mercadopago.com/mlm/herramientas/aplicaciones 
// Venezuela: https://www.mercadopago.com/mlv/herramientas/aplicaciones 
$mp = new MP("CLIENT_ID", "CLIENT_SECRET");

// Get the payment reported by the IPN. Glossary of attributes response in https://developers.mercadopago.com
if($_GET["topic"] == 'payment'){
	$payment_info = $mp->get_payment_info($_GET["id"]);
	$merchant_order_info = $mp->get_merchant_order_info($payment_info["response"]["collection"]["merchant_order_id"]);
// Get the merchant_order reported by the IPN. Glossary of attributes response in https://developers.mercadopago.com	
}else if($_GET["topic"] == 'merchant_order'){
	$merchant_order_info = $mp->get_merchant_order_info($_GET["id"]);
}

//If the payment's transaction amount is equal (or bigger) than the merchant order's amount you can release your items 
if ($merchant_order_info["status"] == 200) {
			$transaction_amount_payments= 0;
			$transaction_amount_order = $merchant_order_info["response"]["total_amount"];
		    $payments=$merchant_order_info["response"]["payments"];
		    foreach ($payments as  $payment) {
		    	if($payment['status'] == 'approved'){
			    	$transaction_amount_payments += $payment['transaction_amount'];
			    }	
		    }
		    if($transaction_amount_payments >= $transaction_amount_order){
		    	echo "release your items";
		    }else{
		    	echo "dont release your items";
		    }
	}

?>
