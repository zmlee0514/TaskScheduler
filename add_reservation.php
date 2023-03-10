<?php
include('session_check.php');

// save form data into database
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
    // insert a row into review
    $query = $pdo->prepare("INSERT INTO `reservation` VALUES (null, :name, :category, :description, :date, :predict, now(), :task_id, {$_SESSION["account_id"]});");
    $query->bindParam(':name', $_POST["name"]);
    $query->bindParam(':category', $_POST["category"]);
    $query->bindParam(':description', $description);
    $query->bindParam(':date', $_POST["date"]);
    $query->bindParam(':predict', $_POST["predict"]);
    $query->bindParam(':task_id', $task_id);
    
    $description = empty($_POST["description"])? "": $_POST["description"];
    $task_id = empty($_POST["task_id"])? null: $_POST["task_id"];
    $query->execute();
    
    if($query->rowCount() == 0){
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': insert fail -> (null, {$_POST["name"]}, {$_POST["category"]}, {$description}, {$_POST["date"]}, {$_POST["predict"]}, now(), $task_id, {$_SESSION["account_id"]})\n", 3, "error_log");
      $fail = true;
    }
    else{
      header("Location: calendar.php");
      exit();
    }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': parameters -> (null, {$_POST["name"]}, {$_POST["category"]}, {$description}, {$_POST["date"]}, {$_POST["predict"]}, now(), $task_id, {$_SESSION["account_id"]})\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;
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
  <link rel="stylesheet" href="css/label.css">
  <link rel="stylesheet" href="css/ttm.css">
  <style>
    
  </style>
</head>
<body class="bgColor">
  <!-- Banner -->
  <div w3-include-html="templates/banner.html"></div>

  <div class="w3-row banner-height">
    <!-- Sidebar -->
    <div class="w3-sidebar w3-collapse bgColor" id="sidenav" style="width: 200px;" w3-include-html="templates/sidebar.html"></div>

    <!-- Main -->
    <div class="w3-main w3-container" style="margin-left: 200px;">
      <!-- Title -->
      <div class=""> 
        <h2 class=""><b>Add Reservation</b></h2>
      </div>
      <hr>
      
      <!-- Task form -->
      <div class="w3-row">
        <div class="w3-col l9">
          <form class="" action="" method="POST" onsubmit="return checkForm(this);">
            <label for="name"><b>Task name</b></label>
            <input class="w3-input w3-round" type="text" placeholder="Enter task name" name="name" required>
            <br>
            <label for="category"><b>Category</b></label>
            <select class="w3-select w3-round" name="category" id="category_options" required>
              <option value="" disabled selected>Choose category</option>
              <option value="{{id}}" w3-repeat="category">{{name}}</option>
            </select>
            <br>
            <br>
            <label for="date"><b>Date</b></label>
            <input class="w3-input w3-round" type="date" name="date" style="padding: 8px" required>
            <br>
            <label for="predict"><b>Predict time cost</b></label>
            <input class="w3-input w3-round" type="text" placeholder="Enter prediction in hour" name="predict" required>
            <br>
            <label for="description"><b>Description</b></label>
            <textarea class="w3-input w3-round" placeholder="Enter description" name="description"></textarea>
            <br>
            <label for="task_id"><b>Task</b></label>
            <select class="w3-select w3-round" name="task_id" id="task_options">
              <option value="" disabled selected>Choose task</option>
              <option value="{{id}}" title="{{description}}" w3-repeat="tasks">{{name}}</option>
            </select>
            <br>
            <br>
            <button class="w3-button w3-round w3-mobile w3-indigo" type="submit" name="submit">Send</button>
            <br>
          </form>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Modals for Banner and Sidebar-->
  <div w3-include-html="templates/modals.html"></div>

  <!-- Include js -->
  <!-- <script defer src="js/fontawesome-v5.10.2-all.js"></script>
  <script src="js/w3.js"></script> 
  <script src="js/jquery.min.js"></script>  -->
  <script src="https://www.w3schools.com/lib/w3.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="js/ttm.js"></script> 
  <script>
    initial_response.then(() => {
      if(category["category"]){
        w3.displayObject("category_options", category);
      }
      else $("#category_options option:last-child").remove();
    })
    .catch(error => console.log(error));
    
    fetch("models/get_tasks.php").then(res => res.json())
    .then(json => {
      if(json["tasks"]){
        w3.displayObject("task_options", json);
      }
      else $("#task_options option:last-child").remove();
    })
    .catch(error => console.log(error));

    function checkForm(obj){
      if($(obj).children("input[name=name]").val().length > 32){
        alert("名稱不能超過32個字元");
        return false;
      }

      let predict = parseFloat($(obj).children("input[name=predict]").val());
      if($(obj).children("input[name=predict]").val() && isNaN(predict)){
        alert("預測時間只能輸入數字");
        return false;
      }
    }

    <?php if(isset($fail)) echo "alert('上傳失敗');"; ?>
  </script>
</body>
</html>