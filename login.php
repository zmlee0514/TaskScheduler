<?php
session_start();
if(isset($_POST["submit"])){
  $logTime = date("d-M-Y H:i:s e"); // for logging
  require_once("setting/config.php");
  try{
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query("SET CHARACTER SET UTF8");
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': connect error -> {$e->getMessage()}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }

  try{
    $account = test_input($_POST["account"]);
    $sql = "SELECT * FROM `accounts` where `account` = '{$account}' and `password` = CONCAT('*', UPPER(SHA1(UNHEX(SHA1('{$_POST["password"]}')))));";
    $query = $pdo->query($sql);
    if($row = $query->fetch(PDO::FETCH_ASSOC)){
      // set cookie and session
      $guid = md5(time() . $row["id"] . rand());
      $query = $pdo->query("UPDATE `accounts` SET `guid` = '{$guid}' WHERE `id` = {$row['id']}");
      setcookie("session_id", $guid, time() + 86400);
      $_SESSION["account_id"] = $row["id"];

      if(isset($_REQUEST["refer"])) header("Location: ". $_REQUEST["refer"]);
      else header("Location: bulletin.php");
    }
    else{
      $error_msg = "找不到帳號或是密碼錯誤";
    }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': statement -> {$sql}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TTM</title>
  <link rel="icon" href="favicon.png">
  <!-- <link rel="stylesheet" href="css/w3.css">
  <link rel="stylesheet" href="css/w3-theme-dark-grey.css"> -->
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-dark-grey.css">
  <link rel="stylesheet" href="css/ttm.css">
  <style>
    .login-form{background-color:rgba(51, 51, 51, 0.425);}
    .error{color: red;}
  </style>
</head>
<body class="bgColor">
  <!-- Login form -->
  <div class="login-form w3-round w3-display-middle w3-mobile" style="width: 450px">
    <div class="w3-card-2">
      <header class="w3-container"> 
        <h2 class="w3-center w3-text-light-blue"><b>Login</b></h2>
      </header>
      <form class="w3-container" action="" method="POST">
        <label class="w3-text-light-blue" for="account"><b>Account</b></label>
        <input class="w3-input w3-round" type="text" placeholder="Enter Account name" name="account" required>
        <br>
        <label class="w3-text-light-blue" for="password"><b>Password</b></label>
        <input class="w3-input w3-round" type="password" placeholder="Enter Password" name="password" required>
        <?php 
        if(isset($error_msg)) echo "<p class='error w3-large'>{$error_msg}</p>";
        else echo '<br>';
        ?>
        <button class="w3-button w3-round w3-block w3-indigo" type="submit" name="submit">Send</button>
        <br>
      </form>
    </div>
  </div>

  <!-- Include js -->
  <!-- <script src="js/jquery.min.js"></script>  -->
  <script>
    
  </script>
</body>
</html>