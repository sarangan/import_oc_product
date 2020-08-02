<?php

echo "IMPORT STARTS<br/>";
echo "MYSQL CONNECTION STARTS<br/>";
$conn = mysqli_connect("localhost","motovati_root","DhmPw!root","motovati_moto_v2");

if (!$conn){
    echo "Failed to connect to MySql:".mysqli_connect_error();
}

echo "MYSQL CONNECTION SUCCESS<br/>";

echo "FILE READ STARTS<br/>";

$file = fopen('data.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
    $product_id = 0;
    $product_name = str_ireplace('"', '', $line[0]);
    $manufacture_id = 0;
    $sku = $line[6];
    $description = str_ireplace('"', '', $line[1]);
    $meta_tag_title = ($line[2]) ? $line[2] : $product_name;
    $meta_tag_title = str_ireplace('"', '', $meta_tag_title);
    $meta_tag_desc = $line[3];
    $keywords = $line[4];
    $price = ($line[7])? $line[7] : '0.00';
    $quantity = ($line[8])? $line[8] : '1';
    $minimum = ($line[9])? $line[9] : '1';
    $subtract = ($line[10])? ((strtoupper($line[10]) === 'YES')? '1' : '0') : '1';
    $stock_status_id = ($line[11])? ((strtoupper($line[11]) === 'YES')? '5' : '0') : '1';
    $manufacture_name = ($line[5])? strtoupper(trim($line[5])) : '';
    
    if($product_name){
        echo "--------------------------------------------------<br/>";
        echo "READ LINE:<br/>";
        echo $product_name . "<br/>";
        
        echo "CHECKING IF " . $product_name . " ALREADY EXISTS<br/>";
        
        $get_product_query = "SELECT product_id FROM product_description WHERE name='$product_name' LIMIT 1";
        $result_product = mysqli_query($conn, $get_product_query);
        
        if (mysqli_num_rows($result_product) > 0) {
            echo "FOUND " . $product_name . " IN PRODUCT DESCRIPTION TABLE<br/>";
            
            while($product_row = mysqli_fetch_assoc($result_product)) {
                $product_id = $product_row["product_id"];
                break;
            }
            
        }
        
        
        echo "CHECKING IF " . $manufacture_name . " ALREADY EXISTS<br/>";
        
        if($manufacture_name){
            
            $get_manufactor_query = "SELECT manufacturer_id FROM manufacturer WHERE name='$manufacture_name' LIMIT 1";
            $result_manufacture = mysqli_query($conn, $get_manufactor_query);
        
            if (mysqli_num_rows($result_manufacture) > 0) {
                echo "FOUND " . $manufacture_name . " IN MANUFACTURE TABLE<br/>";
                
                while($manufacture_row = mysqli_fetch_assoc($result_manufacture)) {
                    $manufacture_id = $manufacture_row["manufacturer_id"];
                    break;
                }
                
            }
            else{
                echo "COULD NOT FIND " . $manufacture_name . " IN TABLE<br/>";
                echo "CHECKING " . $manufacture_name . " WITH LIKE QUERY<br/>";
                
                $get_manufactor_query = "SELECT manufacturer_id FROM manufacturer WHERE name LIKE '$manufacture_name%' LIMIT 1";
                $result_manufacture = mysqli_query($conn, $get_manufactor_query);
                if (mysqli_num_rows($result_manufacture) > 0) {
                    echo "FOUND " . $manufacture_name . " IN MANUFACTURE TABLE WITH LIKE QUERY<br/>";
                    while($manufacture_row = mysqli_fetch_assoc($result_manufacture)) {
                        $manufacture_id = $manufacture_row["manufacturer_id"];
                        break;
                    }
                }
                else{
                    echo "COULD NOT FIND " . $manufacture_name . " IN TABLE WITH LIKE QUERY<br/>";
                }
                
            }
            
        }
        
        if($product_id){
            echo "GOT PRODUCT ID " . $product_id . "<br/>";
            echo "GOING TO UPDATE TABLE<br/>";
            
            $update_product_query = "UPDATE `product` SET 
            `model` = '$product_name', 
            `sku` = '$sku', 
            `quantity` = '$quantity', 
            `stock_status_id` = '$stock_status_id', 
            `manufacturer_id` = '$manufacture_id', 
            `price` = '$price', 
            `subtract` = '$subtract', 
            `minimum` = '$minimum' 
            WHERE `product_id` = '$product_id'"; 

            if (mysqli_query($conn, $update_product_query)) {
              echo "PRODUCT TBL record updated successfully<br/>";
            } else {
              echo "Error: UPDATING PRODUCT TBL <br/>" . mysqli_error($conn);
            }
            
            $update_product_desc_query = "UPDATE `product_description` SET 
            `name` = '$product_name', 
            `description` = '$description', 
            `meta_title` = '$meta_tag_title', 
            `meta_description` = '$meta_tag_desc', 
            `meta_keyword` = '$keywords' 
            WHERE `product_id` = '$product_id'"; 

            if (mysqli_query($conn, $update_product_desc_query)) {
              echo "PRODUCT DESCRIPTION TBL record updated successfully<br/>";
            } else {
              echo "Error: UPDATING PRODUCT DESCRIPTION TBL <br/>" . mysqli_error($conn);
            }
            
        }
        else{
            echo "COULD NOT FIND " . $product_name . " IN TABLE<br/>";
            echo "GOING TO INSERT IN TABLE<br/>";
            
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
                '$quantity', 
                '$stock_status_id', 
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
                '$subtract', 
                '$minimum', 
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
        
        }
        
        echo "<br/>END LINE:<br/>";
        echo "--------------------------------------------------<br/>";
    }
}
fclose($file);
echo "FILE READ STOPS<br/>";

mysqli_close($conn);
echo "MYSQL CONNECTION STOPS<br/>";


?>
