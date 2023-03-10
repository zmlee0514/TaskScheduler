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
  <link rel="stylesheet" href="css/bulletin.css">
  <link rel="stylesheet" href="css/reservation.css">
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
        <!-- counting card -->
        <div class="w3-round widget">
          <div class="w3-container w3-padding w3-row" id="work_counter">
            <div class="w3-col l8 w3-row">
              <!-- slides -->
              <div class="w3-col s6 w3-center">
                <div class="w3-content w3-left" id="slideGIF">
                  <img class="working-state w3-image" src="img/study.gif">
                  <img class="working-state w3-image" src="img/sleep.gif">
                  <img class="working-state w3-image" src="img/coding.gif">
                  <img class="working-state w3-image" src="img/internet.gif">
                </div>
              </div>

              <!-- task counter -->
              <div class="w3-col s6 w3-xlarge w3-center w3-cell-row" id="work_info">
                <!-- choose work -->
                <div class="w3-padding w3-text w3-cell w3-cell-middle">
                  <div>終於要開始工作了？</div>
                </div>
                <!-- start work -->
                <div class="w3-cell" style="display: none">
                  <div>{{name}}</div>
                  <div id="counter"></div>
                  <div class="w3-button w3-red w3-small" id="stopOrMinus">Stop</div>
                  <div class="w3-button w3-blue w3-small" id="finish_counting">Finish</div>
                </div>
              </div>
            </div>
            
            <div class="w3-col l4" id="work_description">
              <!-- choose work -->
              <form class="w3-padding" action="#" onsubmit="return start_work(this);">
                <select class="w3-select w3-round" name="task" id="counter_task_options" required>
                  <option value="" disabled selected>Choose task</option>
                  <option value="{{id}}" title="{{description}}" w3-repeat="tasks">{{name}}</option>
                </select>
                <button class="w3-button w3-block w3-indigo" type="submit" name="submit">Start</button>
              </form>
              
              <!-- task description -->
              <div class="w3-container" style="display: none">
                <hr class="w3-hide-large">
                <div class="w3-large">{{description}}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- countdown critical task -->
        <div class="w3-round widget w3-responsive">
          <!-- title -->
          <div class="w3-bar w3-padding">
            <strong class="w3-bar-item w3-large">Goal</strong>
            <a class="w3-bar-item w3-right w3-button w3-circle" id="goal_add" title="add goal"><i class="fas fa-plus"></i></a>
          </div>

          <!-- task cards -->
          <div class="w3-container" id="goals">
            <!-- card -->
            <div class="w3-row card">
              <!-- goals -->
              <div class="card-container">
                <div class="w3-display-container" w3-repeat="goals">
                  <a class="w3-display-topright w3-small w3-text-gray w3-hover-text-white" title="delete goal" data-id="{{task_id}}"><i class="fas fa-times"></i></a>
                  <div class="w3-padding-16 w3-large">
                    <div>{{name}}</div>
                    <div>{{remaining}} days left</div>
                  </div>
                </div>

              </div>
            </div> 
          
          </div>
        </div>

        <!-- reservations of today -->
        <div class="w3-round widget w3-responsive">
          <!-- title -->
          <div class="w3-bar w3-padding">
            <strong class="w3-bar-item w3-large">Schedule</strong>
            <a href="add_reservation.php" class="w3-bar-item w3-right w3-button w3-circle" title="add reservation"><i class="fas fa-plus"></i></a>
          </div>

          <!-- event cards -->
          <div class="w3-container" id="schedule_today">
            <!-- card -->
            <!-- <div class="w3-row card">
              <div class="w3-container w3-left date">
                <img class="w3-round" src="img/{{day}}.jpg" style="width:100px">
                <div class="w3-center">
                  <div>{{date}}</div>
                  <div>{{weekday}}</div>
                </div>
              </div> -->

              <!-- reservations -->
              <!-- <div class="w3-rest card-container">
                <div class="w3-display-container" w3-repeat="reservations">
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

        <!-- Log of today -->
        <div class="w3-round widget w3-responsive">
          <div class="w3-bar w3-padding">
            <strong class="w3-bar-item w3-large">Today</strong>
            <a href="upload_phase.php" class="w3-bar-item w3-right w3-button w3-circle" title="add record"><i class="fas fa-plus"></i></a>
            <a class="w3-bar-item w3-right w3-button w3-circle" id="record_wrench" title="edit table"><i class="fas fa-wrench"></i></a>
          </div>
          <table class="w3-table table-striped w3-centered" id="record_table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Cost</th>
                <th>Start</th>
                <th>End</th>
                <th class="action_col">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr w3-repeat="today">
                <td>{{name}}</td>
                <td><span class="label label-{{color_text}}">{{category}}</span></td>
                <td>{{cost}}</td>
                <td>{{start}}</td>
                <td>{{end}}</td>
                <td class="action_col">
                  <a href="#" class="label label-danger" data-id='{{id}}' title="delete record"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>

      <!-- Right column -->
      <div class="w3-col w3-half">
        <!-- Recently tasks -->
        <div class="w3-round widget w3-responsive">
          <div class="w3-bar w3-padding">
            <strong class="w3-bar-item w3-large">Recently</strong>
            <a class="w3-bar-item w3-right w3-button w3-circle" id="recent_add" title="add recent task"><i class="fas fa-plus"></i></a>
            <a class="w3-bar-item w3-right w3-button w3-circle" id="recent_wrench" title="edit table"><i class="fas fa-wrench"></i></a>
          </div>
          <table class="w3-table table-striped w3-centered" id="recent_table">
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
                  <a href="#" class="label label-success" data-id='{{id}}' title="edit order"><i class="fas fa-arrows-alt-v"></i></a>
                  <a href="edit_task.php?task_id={{id}}" class="label label-info" title="edit task"><i class="fas fa-edit"></i></a>
                  <a href="#" class="label label-warning" data-id='{{id}}' title="remove from recent task"><i class="fas fa-eye-slash"></i></a>
                  <a href="#" class="label label-danger" data-id='{{id}}' title="remove task"><i class="fas fa-trash"></i></a>
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
  
  <!-- Modal for adding recent task -->
  <div id="modal_recent_add" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-round-large w3-theme-l4 w3-animate-zoom">
      <header class="w3-container"> 
        <a id="close_recent_add_modal" class="w3-btn w3-display-topright w3-round-large"><i class="fas fa-times"></i></a>
        <h2 class="w3-center w3-text-indigo"><b>Add Recent Task</b></h2>
      </header>
      <form class="w3-container" action="models/add_recent_task.php" method="POST">
        <select class="w3-select w3-round" name="task" id="modal_task_options" required>
          <option value="" disabled selected>Choose task</option>
          <option value="{{id}}" title="{{description}}" w3-repeat="task_options">{{name}}</option>
        </select>
        <br>
        <br>
        <button class="w3-button w3-round w3-block w3-indigo" type="submit" name="submit">Send</button>
        <br>
      </form>
    </div>
  </div>
  <!-- Modal for editing order of recent tasks -->
  <div id="modal_recent_order" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-round-large w3-theme-l4 w3-animate-zoom">
      <header class="w3-container"> 
        <a id="close_recent_order_modal" class="w3-btn w3-display-topright w3-round-large"><i class="fas fa-times"></i></a>
        <h2 class="w3-center w3-text-indigo"><b>Edit Order Of Tasks</b></h2>
      </header>
      <form class="w3-container" action="models/edit_recent_task.php" method="POST">
        <select class="w3-select w3-round" name="target_id" id="modal_recent_task_options" required>
          <option value="" disabled selected>Choose task</option>
          <option value="{{id}}" title="{{description}}" w3-repeat="tasks">移動到 {{name}} 之前</option>
        </select>
        <br>
        <br>
        <button class="w3-button w3-round w3-block w3-indigo" type="submit" name="submit">Send</button>
        <br>
      </form>
    </div>
  </div>

  <!-- Modal for adding goal -->
  <div id="modal_goal_add" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-round-large w3-theme-l4 w3-animate-zoom">
      <header class="w3-container"> 
        <a id="close_goal_add_modal" class="w3-btn w3-display-topright w3-round-large"><i class="fas fa-times"></i></a>
        <h2 class="w3-center w3-text-indigo"><b>Add goal from Task</b></h2>
      </header>
      <form class="w3-container" action="models/add_goal.php" method="POST">
        <select class="w3-select w3-round" name="task" id="modal_goal_options" required>
          <option value="" disabled selected>Choose task</option>
          <option value="{{id}}" title="{{description}}" w3-repeat="task_options">{{name}}</option>
        </select>
        <br>
        <br>
        <button class="w3-button w3-round w3-block w3-indigo" type="submit" name="submit">Send</button>
        <br>
      </form>
    </div>
  </div>

  <!-- Include js -->
  <!-- <script defer src="js/fontawesome-v5.10.2-all.js"></script>
  <script src="js/w3.js"></script> 
  <script src="js/jquery.min.js"></script>  -->
  <script src="https://www.w3schools.com/lib/w3.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
  <script src="js/ttm.js"></script> 
  <script>
    // load banner and sidebar, along with array of category
    // initial_response.then(() => {
    //   return fetch("models/get_today_records.php").then(res => res.json());
    // })
    
    
    // PART: Counting card
    w3.slideshow(".working-state", 3000);

    var work, counter, start, stop, tasks;
    fetch("models/get_work.php").then(res => res.json())
    .then(json => {
      // already working
      if(json["name"]){
        work = json;
        work["description"] = work["description"].replace(/\r\n/g, "<br>");
        work["minus"] = parseInt(work["minus"]);

        // set work info and button event
        start = new Date(work["start"]);
        stop = new Date(work["stop"]);
        set_work(work["state"]);
      }
      // choose task
      else{
        fetch("models/get_tasks.php").then(res => res.json())
        .then(json => {
          // fill task_options into counting card
          if(json["tasks"]){
            tasks = json["tasks"];
            w3.displayObject("counter_task_options", json);
          }
          else $("#counter_task_options option:last-child").remove();
        })
        .catch(error => console.log(error));
      }
    })
    .catch(error => console.log(error));

    function start_work(form){
      let form_data = new FormData(form);
      let task_id = form_data.get("task");
      fetch("models/add_work.php?task=" + task_id)
      .catch(error => console.log(error));

      // set work
      start = new Date();
      tasks.forEach(item => {
        if(item["id"] == task_id){
          work = item;
          work["description"] = work["description"].replace(/\r\n/g, "<br>");
          work["minus"] = 0;
          work["task_id"] = work["id"];
          let ms_Offset = start.getTimezoneOffset() * 60000; // in minute: -480
          let localDateTime = new Date(start.getTime() - ms_Offset);
          work["start"] = localDateTime.toISOString().substr(0, 19).replace("T", " ");
          return;
        }
      });
      set_work("start");

      return false;
    }
    function set_work(state){
      // display task
      $("#work_counter > div:first-child").addClass("card-sep");
      $("#work_info > div:first-child").hide();
      $("#work_info > div:last-child > div:first-child").text(work["name"]);
      $("#work_info > div:last-child").show();
      $("#work_description > form").hide();
      $("#work_description > div > div:last-child").html(work["description"]);
      $("#work_description > div").show();

      // adjust button and counter
      let target = $("#stopOrMinus");
      if(state == 'stop'){
        // display counter
        let diff = stop - start - work["minus"]*60000;
        let pass = new Date(diff).toISOString().substr(11,8);
        $("#counter").text(pass);

        // button change
        $(target).removeClass("w3-red");
        $(target).addClass("w3-green");
        $(target).text("Resume");

        $(target).off("click").on("click", (e) => {
          let check = confirm("Restart counting?");
          if(check){
            restart_counting(e.target);//console.log("restart counting");
          }
        });
      }
      else{
        // display counter
        let now = new Date();
        let diff = now - start - work["minus"]*60000;
        let pass = new Date(diff).toISOString().substr(11,8);
        $("#counter").text(pass);
        
        // button change
        $(target).removeClass("w3-green");
        $(target).addClass("w3-red");
        $(target).text("Stop");

        $(target).off("click").on("click", (e) => {
          let check = confirm("Stop counting?");
          if(check){
            stop_counting(e.target);//console.log("stop counting");
          }
        });

        // if state is not stop, activate counter
        counter = setInterval(() => {
          let now = new Date();
          let diff = now - start - work["minus"]*60000;
          let pass = new Date(diff).toISOString().substr(11,8);
          $("#counter").text(pass);
        }, 1000);
      }
    }
    // finish this task, and clean the view of counting card
    $("#finish_counting").on("click", (e) => {
      let check = confirm("Does task finish?");
      if(check){
        // let form = new FormData();
        // Object.keys(work).forEach(key => {
        //   form.append(key, work[key]);
        // });
        // fetch("upload_phase.php", {
        //   method: "POST", 
        //   redirect: "follow",
        //   body: JSON.stringify(work)
        // })
        
        fetch("models/finish_work.php").then(res => res.text())
        .then(text => {
          // console.log(text);
          let temp = {"name": work["name"],"category_id": work["category_id"],"start": work["start"],"minus": work["minus"],"task_id": work["task_id"]};
          window.location = "upload_phase.php?data=" + JSON.stringify(temp);
        })
        .catch(error => console.log(error));
      }
    });
    // counting mechanism
    function stop_counting(target){
      // button change
      $(target).removeClass("w3-red");
      $(target).addClass("w3-green");
      $(target).text("Resume");

      $(target).off("click").on("click", (e) => {
        let check = confirm("Restart counting?");
        if(check){
          restart_counting(e.target);//console.log("restart counting");
        }
      });

      // stop counter
      fetch("models/edit_work.php?state=stop").catch(error => console.log(error));
      clearInterval(counter);
      stop = new Date();
    }
    function restart_counting(target){
      // button change
      $(target).removeClass("w3-green");
      $(target).addClass("w3-red");
      $(target).text("Stop");

      $(target).off("click").on("click", (e) => {
        let check = confirm("Stop counting?");
        if(check){
          stop_counting(e.target);//console.log("stop counting");
        }
      });

      // resume counter
      work["minus"] += (new Date() - stop)/60000;
      fetch("models/edit_work.php?state=resume").catch(error => console.log(error));
      counter = setInterval(() => {
        let now = new Date();
        let diff = now - start - work["minus"]*60000;
        let pass = new Date(diff).toISOString().substr(11,8);
        $("#counter").text(pass);
      }, 1000);
    }
    
    // PART: Goal widget
    fetch("models/get_goals.php").then(res => res.json())
    .then(json => {
      // console.log(json);
      // add goal options
      if(json["task_options"]){
        w3.displayObject("modal_goal_options", json);
      }
      else $("#modal_goal_options option:last-child").remove();

      // show goals
      if(json["goals"]){
        w3.displayObject("goals", json);
      }
      else $("#goals > div").remove();

      // delete goals
      $("#goals a").on("click", (e) => {
        let id = $(e.target).attr("data-id");
        if(!id){
          id = $(e.target).closest("a").attr("data-id");
        }
        
        let check = confirm("Delete this goal?");
        if(check){
          fetch("models/delete_goal.php?task_id=" + id)
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
    })
    .catch(error => console.log(error));

    // PART: Schedule widget
    fetch("models/get_today_reservations.php").then(res => res.json())
    .then(json => {
      if(json["reservations"]){
        // fill records into table and attach click event
        let week = ['日', '一', '二', '三', '四', '五', '六'];
        let date = Object.keys(json["reservations"])[0];
        let day = new Date(date).getDay();
        let weekday = "星期" + week[day];
        $("#schedule_today").append(`<div class="w3-row card" id="${date}">
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
              <div class="w3-padding-16 w3-large" title="{{description}}">
                <div>{{name}}</div>
                <div>{{predict}} hour</div>
              </div>
            </div>

          </div>
        </div>`);
        w3.displayObject(date, json["reservations"]);
            
        // cancel reservation
        $("#schedule_today a").on("click", (e) => {
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
      else $("#schedule_today").remove();
    })
    .catch(error => console.log(error));

    // PART: Today's records widget
    fetch("models/get_today_records.php").then(res => res.json())
    .then(json => {
      if(json["today"]){
        // fill records into table and attach click event
        w3.displayObject("record_table", json);
            
        // delete button
        $("#record_table a").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }
          
          let check = confirm("Delete this record?");
          if(check){
            fetch("models/delete_record.php?record_id=" + id)
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
      else $("#record_table > tbody").remove();
    })
    .catch(error => console.log(error));

    // action column switch
    $("#record_wrench").on("click", () => {
      if($('#record_table thead th:nth-child(6)').css("display") == "none"){
        $('#record_table .action_col').show();
      }
      else{
        $('#record_table .action_col').hide();
      }
    });

    // PART: Recently widget
    fetch("models/get_recent_tasks.php").then(res => res.json())
    .then(json => {
      // fill records into table and attach click event
      if(json["tasks"]){
        w3.displayObject("modal_recent_task_options", json);
        json["tasks"].forEach((item, i) => item["description"] = item["description"].replace(/\r\n/g, "<br>"));
        w3.displayObject("recent_table", json);

        // order modal
        $("#recent_table .action_col a:first-child").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }

          $("#modal_recent_order form").append(`<input type="hidden" name="task_id" value="${id}" />`)
          $("#modal_recent_order").show();
          return false;
        });
        $("#close_recent_order_modal").on("click", () => $("#modal_recent_order").hide());
            
        // remove from recent task
        $("#recent_table .action_col a:nth-child(3)").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }
          
          let check = confirm("Remove this task from recent tasks?");
          if(check){
            fetch("models/delete_recent_task.php?task_id=" + id)
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

        // delete task
        $("#recent_table .action_col a:last-child").on("click", (e) => {
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
      else $("#recent_table > tbody").remove();
      
      // fill task_options into recently modal
      if(json["task_options"]){
        w3.displayObject("modal_task_options", json);
      }
      else $("#modal_task_options option:last-child").remove();
    })
    .catch(error => console.log(error));

    $("#goal_add").on("click", () => $("#modal_goal_add").show());
    $("#close_goal_add_modal").on("click", () => $("#modal_goal_add").hide());
    $("#recent_add").on("click", () => $("#modal_recent_add").show());
    $("#close_recent_add_modal").on("click", () => $("#modal_recent_add").hide());
    $("#recent_wrench").on("click", () => {
      if($('#recent_table thead th:nth-child(6)').css("display") == "none"){
        $('#recent_table .action_col').show();
      }
      else{
        $('#recent_table .action_col').hide();
      }
    });
    
    // PART: Tables
    // hide all action column because class setting can not work
    $('.action_col').hide();
    
  </script>
</body>
</html>