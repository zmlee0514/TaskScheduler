/* Must include after w3.js and jquery.min.js */

// fill in banner and sidebar
async function initial(){
  function wait_include(){
    return new Promise(function(resolve, reject){
      w3.includeHTML(resolve, reject);
    });
  }
  
  await wait_include();
  return initialize_banner_sidebar();
}
var initial_response = initial();

// PART: Sidebar
function toggle_sidebar(){
  $(".w3-sidebar").toggle();
}
function checkCategory(obj){
  if($(obj).children("input[name=name]").val().length > 32){
    alert("名稱不能超過32個字元");
    return false;
  }

  // redirect to origin page
  const origin_action = $(obj).attr("action");
  const refer = encodeURI(window.location.pathname);
  $(obj).attr("action", origin_action+"?refer="+refer);
}

// PART: Countdown timer
var count_down_target;
function set_count_down(clock){
  let now = new Date()
  let today = now.toLocaleDateString();
  count_down_target = new Date(today);

  // if target clock hour less than clock hour now, we need to add one day
  if(clock <= now.getHours()) count_down_target.setDate(count_down_target.getDate() + 1);
  count_down_target.setHours(clock);
}

// globals
var category, colors;
function initialize_banner_sidebar(){
  // PART: Sidebar
  $("#menu").on("click", () => {toggle_sidebar(); return false;});
  $("#add_category").on("click", () => $("#modal_category").show());
  let fetch_promise = fetch("models/get_category.php")
  .then( res => res.json())
  .then( json => {
    // wait color settings
    category = json;
    if(json["category"]) w3.displayObject("category_list", json);
    else $("#category_list > li").remove();
  })
  .catch( error => console.log(error));
  
  // PART: Banner icons
  $("#timer").on("click", () => $("#modal_timer").show());
  $("#close_timer_modal").on("click", () => $("#modal_timer").hide());
  $("#help").on('click', () => {
    fetch("models/get_slogan.php").then(res => res.json())
    .then(json =>{
      if(json["content"]){
        if(json["class"] == "link"){
          $("#slogan_content").html(`<a href="${json["content"]}" target="blank">${json["content"]}</a>`);
          $("#modal_help").show();
        }
        else{
          $("#slogan_content").text(json["content"]);
          $("#modal_help").show();
        }
      }
      else{
        $("#slogan_content").text("沒人能救你，自求多福吧");
        $("#modal_help").show();
      }
      
      return false
    })
    .catch(error => console.log(error));
  });
  $("#close_help_modal").on("click", () => $("#modal_help").hide());

  // PART: Modal
  // When the user clicks anywhere outside of the modal, close it. For all modals.
  window.onclick = function(event) {
    if ($(event.target).hasClass("w3-modal")) {
      event.target.style.display = "none";
    }
  }
  // add category modal
  $("#close_category_modal").on("click", () => $("#modal_category").hide());
  fetch("setting/colors.json").then( res => res.json())
  .then( json => {
    colors = json["colors"];
    json["colors"].forEach( (item,i) =>{
      $("#color_options").append(`<option value="${i}">${item["description"]}</option>`);
    });
  })
  .catch( error => console.log(error));
  
  // PART: Countdown timer
  // Set the counting down target to tomorrow AM 1:00
  set_count_down(1);
  // Update the count down every 1 second
  var x = setInterval(function() {
    // Get today's timestamp
    var now = new Date().getTime();
      
    // count_down_target will auto casting to timestamp and return time interval in millisecond
    var distance = count_down_target - now;
    // Before 6'o clock, count down will be minus sign, means our time debt
    var countdown_str = "";
    var timeInterval = 0;
    if(distance > 68400000){
      // transform distance to ISO format like "1970-01-01T15:03:42.655Z", it count from UNIX 0
      timeInterval = new Date(86400000-distance).toISOString();
      countdown_str = "- "
    }
    else{
      // transform distance to ISO format like "1970-01-01T15:03:42.655Z", it count from UNIX 0
      timeInterval = new Date(distance).toISOString();
    }
      
    // Only catch time part of this format "1970-01-01T15:03:42.655Z"
    document.getElementById("countdown").innerHTML = countdown_str + timeInterval.substr(11,8);
    
  }, 1000);

  // return promise
  return fetch_promise;
}