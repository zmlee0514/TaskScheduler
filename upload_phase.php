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
    $query = $pdo->prepare("INSERT INTO `records` VALUES (null, :name, :category, :description, :start, :end, :cost, :minus, now(), :task_id, {$_SESSION["account_id"]});");
    $query->bindParam(':name', $_POST["name"]);
    $query->bindParam(':category', $_POST["category"]);
    $query->bindParam(':description', $description);
    $query->bindParam(':start', $start);
    $query->bindParam(':end', $end);
    $query->bindParam(':cost', $cost);
    $query->bindParam(':minus', $minus);
    $query->bindParam(':task_id', $task_id);
    
    $description = empty($_POST["description"])? null: $_POST["description"];
    $start = "{$_POST["start-date"]} {$_POST["start-time"]}:00";
    $end = "{$_POST["end-date"]} {$_POST["end-time"]}:00";
    $minus = empty($_POST["minus"])? 0: $_POST["minus"];
    $cost = ((strtotime($end) - strtotime($start)) / 60 - floatval($minus)) / 60;
    $task_id = empty($_POST["task"])? null: $_POST["task"];
    $query->execute();

    if($query->rowCount() == 0){
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': insert fail -> (null, {$_POST["name"]}, {$_POST["category"]}, {$description}, $start, $end, $cost, {$minus}, now(), null, {$_SESSION["account_id"]})\n", 3, "error_log");
      $fail = true;
    }
    else{
      header("Location: bulletin.php");
      exit();
    } 
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': parameters -> (null, {$_POST["name"]}, {$_POST["category"]}, {$description}, $start, $end, $cost, {$minus}, now(), null, {$_SESSION["account_id"]})\n", 3, "error_log");
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
        <h2 class=""><b>Upload Phase</b></h2>
      </div>
      <hr>
      
      <!-- Upload form -->
      <div class="w3-row">
        <div class="w3-col l9">
          <form id="upload_form" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkForm(this);">
            <label for="name"><b>Task name</b></label>
            <input class="w3-input w3-round" type="text" placeholder="Enter task name" name="name" required>
            <br>
            <label for="Category"><b>Category</b></label>
            <select class="w3-select w3-round" name="category" id="category_options" required>
              <option value="" disabled selected>Choose category</option>
              <option value="{{id}}" w3-repeat="category">{{name}}</option>
            </select>
            <br>
            <br>
            <label for="start"><b>Time start</b></label>
            <div class="w3-row">
              <input class="w3-half w3-round" type="date" name="start-date" style="padding: 8px" value="<?php echo isset($_SESSION["last_end"])? $_SESSION["last_end"][0]: date("Y-m-d"); ?>" required>
              <input class="w3-half w3-round" type="time" name="start-time" style="padding: 8px" value="<?php if(isset($_SESSION["last_end"])) echo $_SESSION["last_end"][1]; ?>" required>
            </div>
            <br>
            <label for="end"><b>Time end</b></label>
            <div class="w3-row">
              <input class="w3-half w3-round" type="date" name="end-date" style="padding: 8px" value="<?php echo date("Y-m-d"); ?>" required>
              <input class="w3-half w3-round" type="time" name="end-time" style="padding: 8px" value="<?php echo date("H:i"); ?>" required>
            </div>
            <br>
            <label for="minus"><b>Time minus</b></label>
            <input class="w3-input w3-round" type="text" placeholder="Enter minus amount in minute" name="minus">
            <br>
            <label for="description"><b>Description</b></label>
            <textarea class="w3-input w3-round" placeholder="Enter description" name="description"></textarea>
            <br>
            <label for="task"><b>Task</b></label>
            <select class="w3-select w3-round" name="task" id="task_options">
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

      // if there are data come from counter, fill it in.
      let urlParams = new URLSearchParams(window.location.search);
      let initial_data = urlParams.get("data");
      if(initial_data){
        let initial_json = JSON.parse(initial_data);
      
        $("#upload_form input[name=name]").val(initial_json["name"]);
        $(`#upload_form #category_options option[value=${initial_json["category_id"]}]`).attr("selected", true);
        let start = initial_json["start"].split(" ");
        $("#upload_form input[name=start-date]").val(start[0]);
        $("#upload_form input[name=start-time]").val(start[1].substr(0, 5));
        $("#upload_form input[name=minus]").val(parseInt(initial_json["minus"]));
        // $("#upload_form textarea").text(initial_json["description"].replace(/<br>/g, "\n"));
        $(`#upload_form #task_options option[value=${initial_json["task_id"]}]`).attr("selected", true);
      }
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

      let minus = parseInt($(obj).children("input[name=minus]").val());
      if($(obj).children("input[name=minus]").val() && isNaN(minus)){
        alert("刪減項只能輸入數字");
        return false;
      }

      let start = new Date($(obj).find("input[name=start-date]").val() + " " + $(obj).find("input[name=start-time]").val());
      let end = new Date($(obj).find("input[name=end-date]").val() + " " + $(obj).find("input[name=end-time]").val());
      let cost = (end - start) / 3600000; // transform to hour
      if(cost < 0){
        alert("任務時間必須為正");
        return false;
      }
      else if(cost > 12){
        let check = confirm("確定任務時間超過12小時？");
        if(!check) return false;
      }
    }

    <?php if(isset($fail)) echo "alert('上傳失敗');"; ?>
  </script>
</body>
</html>