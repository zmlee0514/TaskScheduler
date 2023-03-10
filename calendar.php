<?php
include('session_check.php');
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
  <link rel="stylesheet" href="css/reservation.css">
  <style>
    /* @media (max-width:992px){
      .date{max-width: 120px}
    }
    @media (min-width: 993px){
      .date > div{padding: 0 16px}
    } */
  </style>
</head>
<body class="bgColor">
  <!-- Banner -->
  <div w3-include-html="templates/banner.html"></div>

  <div class="w3-row banner-height">
    <!-- Sidebar -->
    <div class="w3-sidebar w3-collapse bgColor" id="sidenav" style="width: 200px;" w3-include-html="templates/sidebar.html"></div>

    <!-- Main -->
    <div class="w3-main w3-row-padding" style="margin-left: 200px;">
      <!-- Center column -->
      <div class="w3-col w3-half">
        <!-- events of 7 days -->
        <div class="w3-round widget w3-responsive">
          <!-- title -->
          <div class="w3-bar w3-padding">
            <strong class="w3-bar-item w3-large">Schedule</strong>
            <a href="add_reservation.php" class="w3-bar-item w3-right w3-button w3-circle" title="add reservation"><i class="fas fa-plus"></i></a>
          </div>

          <!-- event cards -->
          <div class="w3-container" id="schedule">
            <!-- card -->
            <!-- <div class="w3-row card" w3-repeat="reservations">
              <div class="w3-container w3-left date">
                <img class="w3-round" src="img/{{day}}.jpg" style="width:100px">
                <div class="w3-center">
                  <div>{{date}}</div>
                  <div>{{weekday}}</div>
                </div>
              </div> -->

              <!-- reservations -->
              <!--<div class="w3-rest card-container">
                <div class="w3-display-container">
                  <a class="w3-display-topright w3-small w3-text-gray w3-hover-text-white" title="delete reservation" data-id="{{id}}"><i class="fas fa-times"></i></a>
                  <div class="w3-margin-top w3-large" title="{{description}}">
                    <div>{{name}}</div>
                    <div>{{predict}} hour</div>
                  </div>
                </div>

              </div>
            </div> -->
          
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="w3-col w3-half">
        <!-- Tasks -->
        <div class="w3-round widget w3-responsive">
          <div class="w3-bar w3-padding">
            <strong class="w3-bar-item w3-large">Tasks</strong>
            <a class="w3-bar-item w3-right w3-button w3-circle" href="add_task.php" title="add task"><i class="fas fa-plus"></i></a>
            <a class="w3-bar-item w3-right w3-button w3-circle" id="tasks_wrench" title="edit table"><i class="fas fa-wrench"></i></a>
          </div>
          <table class="w3-table table-striped w3-centered" id="task_table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Deadline</th>
                <th>Predict</th>
                <th>Description</th>
                <th class="action_col">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr w3-repeat="tasks">
                <td>{{name}}</td>
                <td><span class="label label-{{color_text}}">{{category}}</span></td>
                <td>{{deadline}}</td>
                <td>{{predict}}</td>
                <td class="w3-justify w3-center">{{description}}</td>
                <td class="action_col">
                  <a href="edit_task.php?task_id={{id}}" class="label label-info" title="edit task"><i class="fas fa-edit"></i></a>
                  <a href="#" class="label label-danger" data-id='{{id}}' title="delete task"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            </tbody>
          </table>
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
    // PART: Schedule widget
    fetch("models/get_reservations.php").then(res => res.json())
    .then(json => {
      if(json["reservations"]){
        // fill records into table and attach click event
        let week = ['日', '一', '二', '三', '四', '五', '六'];
        Object.keys(json["reservations"]).forEach((date, i) => {
          let day = new Date(date).getDay();
          let weekday = "星期" + week[day];
          $("#schedule").append(`<div class="w3-row card" id="${date}">
              <div class="w3-container w3-left date">
                <img class="w3-round" src="img/${day}.jpg" style="width:100px">
                <div class="w3-center">
                  <div>${date}</div>
                  <div>${weekday}</div>
                </div>
              </div>

              <!-- reservations -->
              <div class="w3-rest card-container">
                <div class="w3-display-container" w3-repeat="${date}">
                  <a class="w3-display-topright w3-small w3-text-gray w3-hover-text-white" title="delete reservation" data-id="{{id}}"><i class="fas fa-times"></i></a>
                  <div class="w3-margin-top w3-large" title="{{description}}">
                    <div>{{name}}</div>
                    <div>{{predict}} hour</div>
                  </div>
                </div>

              </div>
            </div>`);
          
          w3.displayObject(date, json["reservations"]);
        });
        $(".card").after("<hr>");
            
        // cancel reservation
        $("#schedule a").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }
          
          let check = confirm("Delete this reservation?");
          if(check){
            fetch("models/delete_reservation.php?reservation_id=" + id)
            .then(res => res.text())
            .then(text => {
              if(text.trim() == "success"){
                alert("刪除成功");
                $(e.target).closest(".w3-display-container").remove();
              }
              else alert("刪除失敗");
            })
            .catch(error => console.log(error));
          }

          return false;
        });
      }
      else $("#schedule").remove();
    })
    .catch(error => console.log(error));

    // PART: Tasks widget 
    fetch("models/get_tasks.php").then(res => res.json())
    .then(json => {
      if(json["tasks"]){
        // fill records into table and attach click event
        json["tasks"].forEach((item, i) => item["description"] = item["description"].replace(/\r\n/g, "<br>"));
        w3.displayObject("task_table", json);
            
        // delete button
        $("#task_table .action_col a:nth-child(2)").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }
          
          let check = confirm("Delete this task?");
          if(check){
            fetch("models/delete_task.php?task_id=" + id)
            .then(res => res.text())
            .then(text => {
              if(text.trim() == "success"){
                alert("刪除成功");
                $(e.target).parents("tr").remove();
              }
              else alert("刪除失敗");
            })
            .catch(error => console.log(error));
          }

          return false;
        });
      }
      else $("#task_table > tbody").remove();
    })
    .catch(error => console.log(error));

    // action column switch
    $("#tasks_wrench").on("click", () => {
      if($('#task_table thead th:nth-child(6)').css("display") == "none"){
        $('#task_table .action_col').show();
      }
      else{
        $('#task_table .action_col').hide();
      }
    });
    
    // PART: Tables
    // hide all action column because class setting can not work
    $('.action_col').hide();
  </script>
</body>
</html>