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
    $query = $pdo->prepare("UPDATE `tasks` SET `name` = :name, `category_id` = :category, `description` = :description, `deadline` = :deadline, `predict` = :predict, `tags` = :tags WHERE `id` = :task_id;");
    $query->bindParam(':task_id', $_POST["id"]);
    $query->bindParam(':name', $_POST["name"]);
    $query->bindParam(':category', $_POST["category"]);
    $query->bindParam(':description', $description);
    $query->bindParam(':deadline', $deadline);
    $query->bindParam(':predict', $predict);
    $query->bindParam(':tags', $tags);
    
    $description = empty($_POST["description"])? "": $_POST["description"];
    $deadline_time = (empty($_POST["deadline-time"]))? "00:00:00": "{$_POST["deadline-time"]}:00";
    $deadline = (empty($_POST["deadline-date"]))? null: "{$_POST["deadline-date"]} {$deadline_time}";
    $predict = empty($_POST["predict"])? 0: $_POST["predict"];
    $tags = empty($_POST["tags"])? "": $_POST["tags"];
    $query->execute();

    if($query->rowCount() == 0){
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': update fail -> SET `name` = {$_POST["name"]}, `category_id` = {$_POST["category"]}, `description` = $description, `deadline` = $deadline, `predict` = $predict, `tags` = $tags WHERE `id` = {$_POST["id"]}\n", 3, "error_log");
      $fail = true;
    }
    else header("Location: bulletin.php");
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': parameters -> SET `name` = {$_POST["name"]}, `category_id` = {$_POST["category"]}, `description` = $description, `deadline` = $deadline, `predict` = $predict, `tags` = $tags WHERE `id` = {$_POST["id"]}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;
}

if(isset($_REQUEST["task_id"])){
  ;
}
else{
  header("Location: bulletin.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TTM</title>
  <link rel="icon" href="favicon.png">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="css/w3.css">
  <link rel="stylesheet" href="css/w3-theme-dark-grey.css"> -->
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-dark-grey.css">
  <link rel="stylesheet" href="css/label.css">
  <link rel="stylesheet" href="css/ttm.css">
  <!-- for multiple tags -->
  <link rel="stylesheet" href="css/bootstrap-tokenfield.min.css">
  <link rel="stylesheet" href="css/tokenfield-typeahead.min.css">
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
        <h2 class=""><b>Edit Task</b></h2>
      </div>
      <hr>
      
      <!-- Task form -->
      <div class="w3-row">
        <div class="w3-col l9">
          <form class="" action="" method="POST" onsubmit="return checkForm(this);" id="task_form">
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
            <label for="deadline"><b>Deadline</b></label>
            <div class="w3-row">
              <input class="w3-half w3-round" type="date" name="deadline-date" style="padding: 8px">
              <input class="w3-half w3-round" type="time" name="deadline-time" style="padding: 8px">
            </div>
            <br>
            <label for="predict"><b>Predict time cost</b></label>
            <input class="w3-input w3-round" type="text" placeholder="Enter prediction in hour" name="predict">
            <br>
            <label for="description"><b>Description</b></label>
            <textarea class="w3-input w3-round" placeholder="Enter description" name="description"></textarea>
            <br>
            <label for="tags"><b>Tags</b></label>
            <input class="form-control" type="text" placeholder="tokenize text after enter" name="tags" id="tokenField">
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
  <!-- for multiple tags -->
  <script src="js/bootstrap-tokenfield.min.js"></script>
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const task_id = urlParams.get("task_id");
    var original_data;
    initial_response.then(() => {
      if(category["category"]){
        w3.displayObject("category_options", category);
      }
      else $("#category_options option:last-child").remove();
      
      return fetch('models/get_task.php?task_id=' + task_id);
    })
    .then(res => res.json())
    .then(json => {
      if(json["task"]){
        original_data = json["task"];
        $("#task_form input[name=name]").val(json["task"]["name"]);
        $(`#task_form option[value=${json["task"]["category_id"]}]`).attr("selected", true);
        if(json["task"]["deadline"]){
          const deadline = json["task"]["deadline"].split(" ");
          $("#task_form input[name=deadline-date]").val(deadline[0]);
          $("#task_form input[name=deadline-time]").val(deadline[1].substr(0, 5));
        }
        $("#task_form input[name=predict]").val(json["task"]["predict"]);
        if(json["task"]["description"]) $("#task_form textarea").text(json["task"]["description"]);
        if(json["task"]["tags"]) $("#task_form input[name=tags]").val(json["task"]["tags"]);
        $('#tokenField').tokenfield();
      }
      else{
        alert("Can not find this task.");
        window.location = "bulletin.php";
      }
    })
    .catch(error => console.log(error));

    function checkForm(obj){
      // if not change, return false
      let change = false;
      if($("#task_form input[name=name]").val() != original_data["name"]) change = true;
      if($("#task_form option:selected").val() != original_data["category_id"]) change = true;
      if(original_data["deadline"]){
        const deadline = original_data["deadline"].split(" ");
        if($("#task_form input[name=deadline-date]").val() != deadline[0]) change = true;
        if($("#task_form input[name=deadline-time]").val() != deadline[1].substr(0, 5)) change = true;
      }
      else{
        if($("#task_form input[name=deadline-date]").val()) change = true;
      }
      if($("#task_form input[name=predict]").val() != original_data["predict"]) change = true;
      if($("#task_form textarea").val() != original_data["description"]) change = true;
      if($("#task_form input[name=tags]").val() != original_data["tags"]) change = true;

      if(!change){
        alert("資料未更動");
        window.location = "bulletin.php";
        return false;
      }

      if($(obj).children("input[name=name]").val().length > 32){
        alert("名稱不能超過32個字元");
        return false;
      }

      let predict = parseInt($(obj).children("input[name=predict]").val());
      if($(obj).children("input[name=predict]").val() && isNaN(predict)){
        alert("預測時間只能輸入數字");
        return false;
      }

      // attach id for database
      $(obj).append(`<input type="hidden" name="id" value="${task_id}" />`);
    }

    <?php if(isset($fail)) echo "alert('上傳失敗');"; ?>
  </script>
</body>
</html>