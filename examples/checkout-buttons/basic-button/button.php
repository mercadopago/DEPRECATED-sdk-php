<!doctype html>
<!--
MercadoPago SDK
Checkout button with MD5 hash
@date 2012/03/29
@author hcasatti
-->
<?php
// Get your Mercadopago credentials (CLIENT_ID and CLIENT_SECRET): 
// Argentina: https://www.mercadopago.com/mla/herramientas/aplicaciones 
// Brasil: https://www.mercadopago.com/mlb/ferramentas/aplicacoes

// Define item data according to form
$data = array (
    // Required
    "item_title" => "Title",
    "item_quantity" => "1",
    "item_unit_price" => "10.00",
    "item_currency_id" => "ARS", //Argentina: ARS, Brasil: BRL

    // Optional
    "item_id" => "CODE_012",
    "item_description" => "",
    "item_picture_url" => "Image URL",
    "external_reference" => "BILL_001",
    "payer_name" => "",
    "payer_surname" => "",
    "payer_email" => "",
    "back_url_success" => "https://www.success.com",
    "back_url_pending" => "",
    "excluded_payment_methods_id" => "",
    "excluded_payment_types_id" => "",
);

$md5String = "CLIENT_ID".                    
        "CLIENT_SECRET".                
        $data["item_quantity"].                 // item_quantity
        $data["item_currency_id"].              // item_currency_id
        $data["item_unit_price"].               // item_unit_price
        $data["item_id"].                       // item_id
        $data["external_reference"].            // external_reference
        $data["excluded_payment_types_id"].     // excluded_payment_types_id
        $data["excluded_payment_methods_id"].   // excluded_payment_methods_id
        $data["credit_card_installments"];      // credit_card_installments

// Get md5 hash
$md5 = md5($md5String);
?>
<html>
    <head>
        <title>Checkout button with MD5 hash</title>
    </head>
    <body>
		<form action="https://www.mercadopago.com/unified/MD5/checkout/pay" method="post" enctype="application/x-www-form-urlencoded" target="">
			<!--Required authentication. Get the CLIENT_ID: 
			Argentina: https://www.mercadopago.com/mla/herramientas/aplicaciones 
			Brasil: https://www.mercadopago.com/mlb/ferramentas/aplicacoes -->	
			<input type="hidden" name="client_id" value="CLIENT_ID"/>
		
			<!-- Hash MD5 -->
			<input type="hidden" name="key" value="<?php echo $md5 ?>"/>
		   
			<!-- Required -->
			<input type="hidden" name="item_title" value="<?php echo $data["item_title"]?> "/>
			<input type="hidden" name="item_quantity" value="<?php echo $data["item_quantity"]?>"/>
			<input type="hidden" name="item_currency_id" value="<?php echo $data["item_currency_id"]?>"/>
			<input type="hidden" name="item_unit_price" value="<?php echo $data["item_unit_price"]?>"/>
		   
			<!-- Optional -->
			<input type="hidden" name="item_id" value="<?php echo $data["item_id"]?>"/>
			<input type="hidden" name="external_reference" value="<?php echo $data["external_reference"]?>"/>
			<input type="hidden" name="item_picture_url" value="<?php echo $data["item_picture_url"]?>"/>
			<input type="hidden" name="payer_name" value="<?php echo $data["payer_name"]?>"/>
			<input type="hidden" name="payer_surname" value="<?php echo $data["payer_surname"]?>"/>
			<input type="hidden" name="payer_email" value="<?php echo $data["payer_email"]?>"/>
			<input type="hidden" name="back_url_success" value="<?php echo $data["back_url_success"]?>"/>
			<input type="hidden" name="back_url_pending" value="<?php echo $data["back_url_pending"]?>"/>
		   
			<!-- Checkout Button -->
			<button type="submit" class="lightblue-rn-m-tr-arall" name="MP-payButton">Pagar</button>
		</form>
		
		<!-- More info about render.js: https://developers.mercadopago.com -->
		<script type="text/javascript" src="http://mp-tools.mlstatic.com/buttons/render.beta.js"></script>
    </body>
</html>
