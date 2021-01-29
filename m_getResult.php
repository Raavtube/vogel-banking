<?php
    function console_log($output, $with_script_tags = true) {
          $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
        ');';
          if ($with_script_tags) {
              $js_code = '<script>' . $js_code . '</script>';
          }
          echo $js_code;
    }
    $servername = "localhost";
    $username = "root";
    $password = "MYSQLraavid2";
    $dbname = "php_bank";
    $tableName = 'bank_table_mackay';
    $syntaxCheck = 1;
    $addORsub = $_POST['addORsub'];

    $amount = $_POST['amount'];
    $reason = $_POST['reason'];
    $reasonDropdown = $_POST['reasonDrop'];
    if ($reason != ""){
        $reason = $_POST['reason'];
    }else{
        $reason = $reasonDropdown;
    }

    if ($reasonDropdown == "paypal"){
        echo "HERE";
        $syntaxCheck = 0;
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        $sql = "SELECT balance, reason from bank_table_ryan where id = 1"; // SQL with parameters
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $redirectID);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        while ($row = $result->fetch_assoc()) {
            $paypalBal = $row['reason'];
        }
        // Add or subtract from paypal

        if ($addORsub == "add") {
            $temporalBalance = $paypalBal + $amount;
        }
        if ($addORsub == "sub") {
            $temporalBalance = $paypalBal - $amount;

        }
        echo $temporalBalance;  
        // Update Paypal
        echo '<br>';
        
        $newSQL = "UPDATE `bank_table_ryan` SET `Reason` = $temporalBalance WHERE `bank_table_ryan`.`id` = 1";
        echo $newSQL;
        mysqli_query($conn,$newSQL);
        $conn->close();

        
    }


    if ($addORsub == "sub") {
        $amount = '-' . $amount;
    }

    // This is a check to make sure that the code is correctly formatted.
    if ($syntaxCheck == 1) {


        $sql = "INSERT INTO $tableName (`Balance`, `Reason`) VALUES ($amount, '$reason')";

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // This is just to push the new value up.
        mysqli_query($conn,$sql);

        // Getting the current balance so we can do the math.
        $sql = "SELECT balance, reason from $tableName where id = 1"; // SQL with parameters
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $redirectID);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        while ($row = $result->fetch_assoc()) {
            $currentBal = $row['balance'];
            $paypalBal = $row['reason'];
        }
        $sql = "SELECT balance, reason from bank_table_ryan where id = 1"; // SQL with parameters
        $stmt = $conn->prepare($sql); 
        $stmt->bind_param("i", $redirectID);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        while ($row = $result->fetch_assoc()) {
            $paypalBal = $row['reason'];
        }
        
        // Math to check balance:
        if ($addORsub == "add") {
            $pureAmount = str_replace('+', "", $amount);
            $addValue = True;
            $temporalBalance = $currentBal + $pureAmount;
            
            // This is to help later on.
        }
        if ($addORsub == "sub") {
            $pureAmount = str_replace('-', "", $amount);
            $addValue = False;
            $temporalBalance = $currentBal - $pureAmount;
            if ($reason == "STEP"){
                echo $paypalBal;
                echo "<br>";
                echo $pureAmount;
                $tempPaypal = $paypalBal - $pureAmount;
                $newSQL = "UPDATE `bank_table_ryan` SET `Reason` = $tempPaypal WHERE `bank_table_ryan`.`id` = 1";
                echo "<br>";
                echo $newSQL;
                mysqli_query($conn,$newSQL);
            }
        
            
        }
        
        $newSQL = "UPDATE `$tableName` SET `Balance` = $temporalBalance WHERE `$tableName`.`id` = 1";
        mysqli_query($conn,$newSQL);


        $conn->close();
        
        

    }
    header('Location: https://rserver.ml/bank/mackay.php');

?>