<!doctype html>
<html>
    <head>
        <title>Search approved payments in last month</title>
    </head>
    <body>
        <?php
        /**
         * MercadoPago SDK
         * Search approved payments in last month
         * @date 2012/03/29
         * @author hcasatti
         */
        
        // Include Mercadopago library
        require_once "../../lib/mercadopago.php";
      
        // Create an instance with your MercadoPago credentials (CLIENT_ID and CLIENT_SECRET): 
        // Argentina: https://www.mercadopago.com/mla/herramientas/aplicaciones 
        // Brasil: https://www.mercadopago.com/mlb/ferramentas/aplicacoes
        $mp = new MP ("CLIENT_ID", "CLIENT_SECRET");
      
        // Sets the filters you want
        $filters = array (
            "range" => "date_created",
            "begin_date" => "NOW-1MONTH",
            "end_date" => "NOW",
            "status" => "approved", 
            "operation_type" => "regular_payment"
        );
      
        // Search payment data according to filters
        $searchResult = $mp->search_payment ($filters);
        
        // Show payment information
        ?>
        <table border='1'>
            <tr><th>id</th><th>site_id</th><th>date_created</th><th>operation_type</th><th>external_reference</th></tr>
            <?php
            foreach ($searchResult["response"]["results"] as $payment) {
                ?>
                <tr>
                    <td><?php=$payment["collection"]["id"]?></td>
                    <td><?php=$payment["collection"]["site_id"]?></td>
                    <td><?php=$payment["collection"]["date_created"]?></td>
                    <td><?php=$payment["collection"]["operation_type"]?></td>
                    <td><?php=$payment["collection"]["external_reference"]?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </body>
</html>
