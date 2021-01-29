<?php
$servername = "localhost";
$username = "root";
$password = "MYSQLraavid2";
$dbname = "php_bank";
$tableName = 'bank_table_ryan';


function console_log($output, $with_script_tags = true) {
  $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
  if ($with_script_tags) {
      $js_code = '<script>' . $js_code . '</script>';
  }
  echo $js_code;
}

function display_data($data) {
    $output = '<table>';
    foreach($data as $key => $var) {
        $output .= '<tr>';
        foreach($var as $k => $v) {
          
            if ($key === 0) {
                if (strpos($k, 'id') !== false) {
                  $placeholder = 0;  
                }else{
                  $output .= '<td><strong>' . $k . '</strong></td>';
                }
                
            } else {
              if (strpos($k, 'id') !== false) {
                $placeholder = 0;  
              }else{
                $output .= '<td>' . $v . '</td>';
              }
                
            }
        }
        $output .= '</tr>';
    }
    $output .= '</table>';
    return $output;
}

$servername = "localhost";
$username = "root";
$password = "MYSQLraavid2";
$dbname = "php_bank";

$conn = mysqli_connect($servername, $username, $password, $dbname);

$result = mysqli_query($conn,"SELECT * FROM $tableName");
//$tableresult = display_data($result);

$sql = "SELECT balance, reason from $tableName where id = 1"; // SQL with parameters
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $redirectID);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result
while ($row = $result->fetch_assoc()) {
    $currentBal = $row['balance'];
    $paypalBal = $row['reason'];
}


$conn->close(); //Make sure to close out the database connection

// -------------------------Newest at the front-------------------------


$conn = mysqli_connect($servername, $username, $password, $dbname);

$sql = "SELECT count(1) FROM $tableName";
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $redirectID);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result
while ($row = $result->fetch_assoc()) {
    $rows_count = $row['count(1)'];
}
console_log("Rows: " . $rows_count);


// Table Formatting

$output = '<table>';
$output .= '<tr><td><strong>Balance</strong></td><td><strong>Reason</strong></td></tr>';
$count_x = 0;
for ($count = 0; $count < $rows_count; $count++) {
  console_log("Count: " . $count_x);
  $rowToFetch = $rows_count - $count_x;
  console_log("Row Number: " . $rowToFetch);
  $sql = "SELECT balance, reason, transaction_date, description from $tableName where id = $rowToFetch"; // SQL with parameters
  $stmt = $conn->prepare($sql); 
  $stmt->bind_param("i", $redirectID);
  $stmt->execute();
  $result = $stmt->get_result(); // get the mysqli result
  while ($row = $result->fetch_assoc()) {
    $balance = $row['balance'];
    $reason = $row['reason'];
    $date = $row['transaction_date'];
    $description = $row['description'];
}
  console_log("Balance: " . $balance);
  console_log("ID NUM THING: " . $count);
  console_log($date);
  if ($reason == "CURRENT BALANCE"){
    $placeholder = 0;
  }else{
	if ($description == ""){
      if (strpos($reason, "Tithe") !== false){
        $output .= "<tr><td><span id='yellow'><b>$balance</b></span></td><td><span id='yellow'>$reason - Date: $date</span> </td></tr>";
      }
      if (strpos($reason, "Bank Deposit") !== false && $checkColor !== true){
        $output .= "<tr><td><span id='yellow'><b>$balance</b></span></td><td><span id='yellow'>$reason - Date: $date</span> </td></tr>";
      }
	    if (strpos($balance, "-") !== false){
	      $output .= "<tr><td><span id='red'><b>$balance</b></span></td><td><span id='red'>$reason - Date: $date</span></td></tr>";
	    }
	    else{
	      $output .= "<tr><td><span id='green'><b>$balance</b></span></td><td><span id='green'>$reason - Date: $date</span> </td></tr>";
	    }
      
	}else{
      if (strpos($reason, "Tithe") !== false){
        $output .= "<tr><td><span id='yellow'><b>$balance</b></span></td><td><span id='yellow'>$reason - Date: $date | Notes: $description</span> </td></tr>";
      }
      if (strpos($reason, "Bank Deposit") !== false){
        $output .= "<tr><td><span id='yellow'><b>$balance</b></span></td><td><span id='yellow'>$reason - Date: $date | Notes: $description</span> </td></tr>";
      }
	    if (strpos($balance, "-") !== false){
        $output .= "<tr><td><span id='red'><b>$balance</b></span></td><td><span id='red'>$reason - Date: $date | Notes: $description</span></td></tr>";
      }
      else{
        $output .= "<tr><td><span id='green'><b>$balance</b></span></td><td><span id='green'>$reason - Date: $date | Notes: $description</span> </td></tr>";
      }
      

	}

    
  }
  
  console_log($sql);

  $count_x = $count_x + 1;
}
$output .= "</table>";





mysqli_close($conn);




?>
<!DOCTYPE html>
<html>
<head>
<title>Ryan Vogel Banking</title>
<style>
#table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 60%;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
#red{
  color: red;
}
#green{
  color: green;
}
#yellow{
  color: darkblue;
}


</style>
</head>
<body>

<center><h1>Ryan Vogel Banking</h1></center>

<center>
<h2>Current Balance: $<?php echo $currentBal; ?> | Paypal Balance: $<?php echo $paypalBal; ?></h2>
<form action="getResult.php" method="post">
  <label for="amount">Enter Amount:</label><br><hr style="height:0px; visibility:hidden;" />
  <input type="text" id="amount" name="amount" required><br><br>
  <!-- Radio Buttons-->
  <input type="radio" id="add" name="addORsub" value="add" checked>
  <label for="add">Add to Balance</label>
  <input type="radio" id="subtract" name="addORsub" value="sub">
  <label for="subtract">Remove from Balance</label><br><br>
  <!-- Reasons -->
  <label >Reason or Notes:</label><br><hr style="height:0px; visibility:hidden;" />
  <label for="cars"> Transaction Description:</label>
  <select name="reasonDrop" id="reasonDrop" required>
    <option disabled selected value>Select An Option</option>
    <option value="STEP">STEP</option>
    <option value="Amazon">Amazon</option>
    <option value="Paycheck (TLTS)">Paycheck (TLTS)</option>
    <option value="Cash">CASH</option>
    <option value="Web Order">Web Order</option>
    <option value="Cash Withdrawal">CASH WITHDRAWAL</option>
    <option value="Other">OTHER</option>
    <option value="Paypal">Paypal</option>
    <option value="Tithe">Tithe</option>
    <option value="Bank Deposit">Bank Deposit</option>
  </select>
  <br>
  <br>
   <label for="amount">Notes: </label>
  <input type="text" id="description" name="description"><br><br>
  
  
  <br><br>
  
  
  
  <input type="submit" value="Submit">
  <br><br>
</form>
</center>
<center>
<div id="table"style="height:600px;overflow:auto;">
<?php echo $output; ?>
</div>
</center>
</body>
</html>
