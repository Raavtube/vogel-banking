<?php
    
    function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
       return $string;
    }
    include_once ('./vendor/autoload.php'); 

    use Socketlabs\SocketLabsClient;
    use Socketlabs\Message\BasicMessage;
    use Socketlabs\Message\EmailAddress;

    date_default_timezone_set('America/New_York');
    $servername = "localhost";
    $username = "root";
    $password = "MYSQLraavid2";
    $dbname = "php_bank";
    $tableName = 'bank_table_ryan';
    $syntaxCheck = 1;
    $addORsub = $_POST['addORsub'];

    $amount = $_POST['amount'];
    $reason = $_POST['reason'];
    $descriptionx = $_POST['description'];
    $description = clean($descriptionx);
    $reasonDropdown = $_POST['reasonDrop'];
    echo $reasonDropdown;
    echo $reason;
    if ($reason != ""){
        $reason = $_POST['reason'];
    }else{
        $reason = $reasonDropdown;
    }

    if ($reasonDropdown == "Paypal"){
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

	$CurrentDate = date("Y-m-d") . ' Time: ' . date("h:i:sa");
	echo $CurrentDate;
	echo "<br>";
        $sql = "INSERT INTO $tableName (`Balance`, `Reason`, `transaction_date`, `description`) VALUES ($amount, '$reason', '$CurrentDate', '$description')";
	echo $sql;
	echo "<br>";

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
                $tempPaypal = $paypalBal - $pureAmount;
                $newSQL = "UPDATE `bank_table_ryan` SET `Reason` = $tempPaypal WHERE `bank_table_ryan`.`id` = 1";
                mysqli_query($conn,$newSQL);
            }
        
            
        }
        
        $newSQL = "UPDATE `$tableName` SET `Balance` = $temporalBalance WHERE `$tableName`.`id` = 1";
        echo $newSQL;
        mysqli_query($conn,$newSQL);


        
        
        

    }
    $sql = "SELECT balance, reason from $tableName where id = 1"; // SQL with parameters
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("i", $redirectID);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    while ($row = $result->fetch_assoc()) {
        $currentBal = $row['balance'];
        $paypalBal = $row['reason'];
    }
    $conn->close();
    $serverId = 37210;
    $injectionApiKey = "b7RSg9t3DPw48WqNa52F";

    $client = new SocketLabsClient($serverId, $injectionApiKey);

    $message = new BasicMessage(); 

    $message->subject = "Ryan Vogel Banking - New Transaction";
    $message->htmlBody = "
    <html>
        <h1>New Transaction</h1>
        <p><b>Amount:</b> $amount<br><b>Paypal Balance:</b> $paypalBal<br><b>Current Balance:</b> $currentBal<br><b>Reason:</b> $reason<br><b>Notes:</b> $description</p>
    </html>
    ";
    //$message->plainTextBody = "This is the Plain Text Body of my message.";

    $message->from = new EmailAddress("info@raavcorp.com");
    $message->addToAddress("ryan.vogel2004@gmail.com");
    $message->addToAddress("joevogel2005@gmail.com");

    $response = $client->send($message);
   header('Location: https://rserver.ml/bank/ryan.php');

?>
