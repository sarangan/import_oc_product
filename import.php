<?php

echo "IMPORT STARTS<br/>";
echo "MYSQL CONNECTION STARTS<br/>";
$conn = mysqli_connect("localhost","user","password","db_name");

if (!$conn){
    echo "Failed to connect to MySql:".mysqli_connect_error();
}

echo "MYSQL CONNECTION SUCCESS<br/>";

echo "FILE READ STARTS<br/>";

$file = fopen('data.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
    $product_name = str_ireplace('"', '', $line[0]);
    $manufacture_id = 38;
    $sku = $line[6];
    $description = str_ireplace('"', '', $line[1]);
    $meta_tag_title = ($line[2]) ? $line[2] : $product_name;
    $meta_tag_title = str_ireplace('"', '', $meta_tag_title);
    $meta_tag_desc = $line[3];
    $keywords = $line[4];
    $price = ($line[7])? $line[7] : '0.00';
    
    if($product_name){
        echo "READ LINE:<br/>";
        echo $product_name;

        $sql_product = "INSERT INTO `product` (
            `model`, 
            `sku`, 
            `upc`, 
            `ean`, 
            `jan`, 
            `isbn`, 
            `mpn`, 
            `location`, 
            `quantity`, 
            `stock_status_id`, 
            `image`, 
            `manufacturer_id`, 
            `shipping`, 
            `price`, 
            `points`, 
            `tax_class_id`, 
            `date_available`, 
            `weight`, 
            `weight_class_id`, 
            `length`, 
            `width`, 
            `height`, 
            `length_class_id`, 
            `subtract`, 
            `minimum`, 
            `sort_order`, 
            `status`, 
            `viewed`, 
            `date_added`, 
            `date_modified`) 
            VALUES ( 
                '$product_name', 
                '$sku', 
                '', 
                '', 
                '', 
                '', 
                '', 
                '', 
                '0', 
                '1', 
                NULL, 
                '$manufacture_id', 
                '1', 
                '$price', 
                '0', 
                '0', 
                '0000-00-00', 
                '0.00000000', 
                '0', 
                '0.00000000', 
                '0.00000000', 
                '0.00000000', 
                '0', 
                '1', 
                '1', 
                '0', 
                '1', 
                '0', 
                '2020-07-31 13:30:31', 
                '2020-07-31 13:30:31'
            )";
            

        if (mysqli_query($conn, $sql_product)) {
            $product_id = mysqli_insert_id($conn);
          echo $product_name . " :: ". $last_id . " :: " . " PRODUCT TBL record created successfully<br/>";
          
          $sql_product_desc = "INSERT INTO `product_description` (
              `product_id`, 
              `language_id`, 
              `name`, 
              `description`, 
              `tag`, 
              `meta_title`, 
              `meta_description`, 
              `meta_keyword`) 
              VALUES (
                  '$product_id', 
                  '1', 
                  '$product_name', 
                  '$description', 
                  '', 
                  '$meta_tag_title', 
                  '$meta_tag_desc', 
                  '$keywords'
            )";
            
            if (mysqli_query($conn, $sql_product_desc)) {
                echo "PRODUCT DESCRIPTION TBL record created successfully<br/>";
            }
            else {
              echo "Error: PRODUCT DESCRIPTION <br/>" . mysqli_error($conn);
            }
            
            $sql_store = "INSERT INTO `product_to_store` (`product_id`, `store_id`) VALUES ('$product_id', '0')";
            
            if (mysqli_query($conn, $sql_store)) {
                echo "PRODUCT STORE TBL record created successfully<br/>";
            }
            else {
              echo "Error: PRODUCT STORE <br/>" . mysqli_error($conn);
            }
          
          
        } else {
          echo "Error: PRODUCT TBL <br/>" . mysqli_error($conn);
        }
        
        echo "<br/>END LINE:<br/>";
    }
}
fclose($file);
echo "FILE READ STOPS<br/>";

mysqli_close($conn);
echo "MYSQL CONNECTION STOPS<br/>";


?>
