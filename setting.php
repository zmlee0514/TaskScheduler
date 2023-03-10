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
  <style>
    .setting-table{
      overflow-y: scroll; 
      max-height: 300px;
    }
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
      <div class=""> 
        <h2 class=""><b>Setting</b></h2>
      </div>
      <hr>

      <!-- Setting form -->
      <div class="w3-row">
        <div class="w3-col l9">
          <!-- normal setting -->
          <!-- <div class="w3-bar w3-padding">
            <strong class="w3-xlarge">Normal</strong>
          </div>
          <div class="w3-container">
            <form class="" action="#">
              <label for="boundary"><b>Date boundary</b></label>
              <input class="w3-input w3-round" type="text" placeholder="Enter clock point (0 ~ 23)" name="boundary" required>
              <br>
              <button class="w3-button w3-round w3-mobile w3-indigo" type="submit" name="submit">Save</button>
              <br>
              <br>
            </form>
          </div>
          <hr> -->
          
          <!-- category setting -->
          <div class="w3-bar w3-padding">
            <strong class="w3-xlarge">Category</strong>
          </div>
          <div class="w3-margin-right w3-margin-left setting-table">
            <table class="w3-table-all w3-text-black" id="category_table">
              <thead>
                <tr class="w3-blue">
                  <th>Name</th>
                  <th>Color</th>
                  <th class="action_col">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr w3-repeat="category">
                  <td>{{name}}</td>
                  <td><span class="label label-{{color_text}}">{{name}}</span></td>
                  <td class="action_col">
                    <a href="#" class="label label-info" data-id='{{id}}'><i class="fas fa-edit"></i></a>
                    <a href="#" class="label label-danger" data-id='{{id}}'><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <br>
          <hr>
          
          <!-- slogan setting -->
          <div class="w3-bar w3-padding">
            <strong class="w3-xlarge">Slogan</strong>
            <a href="#" class="w3-bar-item w3-right w3-button w3-circle" id="slogan_add"><i class="fas fa-plus"></i></a>
            <a href="#" class="w3-bar-item w3-right w3-button w3-circle" id="slogan_show"><i class="fas fa-eye"></i></a>
          </div>
          <div class="w3-margin-right w3-margin-left setting-table">
            <table class="w3-table-all w3-text-black" id="slogan_table" style="display: none;">
              <thead>
                <tr class="w3-blue">
                  <th>Content</th>
                  <th>Class</th>
                  <th class="action_col">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr w3-repeat="slogans">
                  <td>{{content}}</td>
                  <td>{{class}}</td>
                  <td class="action_col">
                    <a href="#" class="label label-danger" data-id='{{id}}'><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <br>

          <br>
        </div>
      </div>
    </div>

  </div>

  <!-- Modals for Banner and Sidebar-->
  <div w3-include-html="templates/modals.html"></div>
  
  <!-- Modal for editing category -->
  <div id="modal_category_edit" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-round-large w3-theme-l4 w3-animate-zoom">
      <header class="w3-container"> 
        <a id="close_category_edit_modal" class="w3-btn w3-display-topright w3-round-large"><i class="fas fa-times"></i></a>
        <h2 class="w3-center w3-text-indigo"><b>Edit Category</b></h2>
      </header>
      <form class="w3-container" action="models/edit_category.php" method="POST" onsubmit="return checkCategory(this);">
        <label class="w3-text-indigo" for="name"><b>Category name</b></label>
        <input class="w3-input w3-round" type="text" placeholder="Enter category name" name="name" required>
        <br>
        <label class="w3-text-indigo" for="color"><b>Color</b></label>
        <select class="w3-select w3-round" name="color" id="edit_color_options" required>
          <option value="" disabled selected>Choose category color</option>
        </select>
        <br>
        <br>
        <button class="w3-button w3-round w3-block w3-indigo" type="submit" name="submit">Save</button>
        <br>
      </form>
    </div>
  </div>

  <!-- Modal for adding slogan -->
  <div id="modal_slogan_add" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-round-large w3-theme-l4 w3-animate-zoom">
      <header class="w3-container"> 
        <a id="close_slogan_add_modal" class="w3-btn w3-display-topright w3-round-large"><i class="fas fa-times"></i></a>
        <h2 class="w3-center w3-text-indigo"><b>Add Slogan</b></h2>
      </header>
      <form class="w3-container" action="models/add_slogan.php" method="POST">
        <label class="w3-text-indigo" for="content"><b>Slogan content</b></label>
        <input class="w3-input w3-round" type="text" placeholder="Enter slogan content" name="content" required>
        <br>
        <label class="w3-text-indigo" for="class"><b>Slogan class</b></label>
        <input class="w3-input w3-round" type="text" placeholder="Enter slogan class" name="class">
        <br>
        <button class="w3-button w3-round w3-block w3-indigo" type="submit" name="submit">Save</button>
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
    initial_response.then(() => {
      if(category["category"]){
        w3.displayObject("category_table", category);

        // PART: Category
        // edit category
        $("#category_table .action_col a:nth-child(1)").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }
          
          let i = $(e.target).closest("tr").index();
          $("#modal_category_edit form input[name=name]").val(category["category"][i]["name"]);
          $(`#modal_category_edit form option[value=${category["category"][i]["color_id"]}]`).attr("selected", true);
          $("#modal_category_edit form").append(`<input type="hidden" name="category_id" value="${id}" />`)

          $("#modal_category_edit").show()
          return false;
        });
        // edit category modal
        $("#close_category_edit_modal").on("click", () => $("#modal_category_edit").hide());
        colors.forEach( (item,i) =>{
          $("#edit_color_options").append(`<option value="${i}">${item["description"]}</option>`);
        });
        // delete category
        $("#category_table .action_col a:nth-child(2)").on("click", (e) => {
          let id = $(e.target).attr("data-id");
          if(!id){
            id = $(e.target).closest("a").attr("data-id");
          }
          
          let check = confirm("Delete this category?");
          if(check){
            fetch("models/delete_category.php?category_id=" + id)
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
      else $("#category_table > tbody > tr").remove();
    })
    .catch(error => console.log(error));

    // PART: slogan
    $("#slogan_add").on("click", () => $("#modal_slogan_add").show());
    $("#close_slogan_add_modal").on("click", () => $("#modal_slogan_add").hide());
    $("#slogan_show").on("click", () => {
      fetch("models/get_slogans.php").then(res => res.json())
      .then(json => {
        if(json["slogans"]){
          w3.displayObject("slogan_table", json);
          $("#slogan_table").show();

          // delete slogan
          $("#slogan_table .action_col a").on("click", (e) => {
            let id = $(e.target).attr("data-id");
            if(!id){
              id = $(e.target).closest("a").attr("data-id");
            }
            
            let check = confirm("Delete this slogan?");
            if(check){
              fetch("models/delete_slogan.php?slogan_id=" + id)
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
        else $("#slogan_table > tbody > tr").remove();
      })
      .catch(error => console.log(error));
    });
    
  </script>
</body>
</html>