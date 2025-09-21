<?php
session_start();

if (isset($_SESSION["username"])) {
  $email = $_SESSION["username"];
} else {
  echo "Session variable not set.";
  exit;
}

$servername = "localhost";
$dbname = "team018";
$username = "team018";
$password = "nkAfiVuTsC4Yw9LvLEgP";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT employeeID, employeeDepartment, employeeFirstName FROM employeeTable WHERE employeeEmail = ?";
$stmt1 = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt1, "s", $email);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1, $employeeID, $employeeDepartment, $employeeFirstName);
mysqli_stmt_fetch($stmt1);
mysqli_stmt_close($stmt1);

$ifManagerQuery = "SELECT employeeID FROM employeeTable WHERE manager = 1 AND employeeID = $employeeID";
$ifManagerResult = mysqli_query($conn, $ifManagerQuery);
$row = mysqli_fetch_assoc($ifManagerResult);
if ($row['employeeID'] == $employeeID) {
  $ifManager = true;
} else {
  $ifManager = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/threeColumnStyles.css">
  <link rel="stylesheet" href="css/generalStyles.css">
  <link rel="stylesheet" href="css/homePageStyles.css">
  <link rel="stylesheet" href="css/Pop-upstyles.css">
  <link rel="stylesheet" href="css/toDoStyles.css"> <!--create to-do task pop up styles-->
  <link rel="stylesheet" href="css/todolistStyles.css">
  <style>
    .complete {
      display: none
    }
    /* CSS for each priority of tasks - changeable based on preference of colour */
    .low .grid-container, .low .content-container {background-color: rgba(0, 217, 15, 0.79);}
    .high .grid-container, .high .content-container{background-color:rgba(255, 99, 9, 1); }
    .veryhigh .grid-container, .veryhigh .content-container{
      background-color: rgba(248,220,2,255);
    }
  </style>
  <title>To-Do List</title>

</head>

<body>

  <!-- WEBSITE HEADER -->
  <div class="icon-container" style="padding: 1px; align-items: center; display: flex; justify-content: space-between">
    <div class="employee-info-container" style="margin-left: 13px; font-size: 15px; color: white; text-align: left; font-weight: bold;">
      <?php
      echo "<p>Employee ID No: $employeeID<br>Department: $employeeDepartment</p>";
      ?>
    </div>
    <a class="btn btn-dark" style="margin-right: 16px; font-size: 18px; color: black; text-decoration: none; padding: 5px 10px; 
	background-color: rgba(217,154,1,255); border-radius: 5px; transition: background-color 0.3s; font-weight: bold;" href="Login_Page.html">Log Out</a>
  </div>
  <div class="header homepage-header-top">
    <div class="left-content">
      <img src="todolistIconImage.png" alt="to do list icon image">
    </div>
    <h2>To-Do List</h2>
    <div class="center-content">
      <img src="makeItAllLogoImage.png" alt="make it all logo image">
    </div>
    <div class="left-content" style="opacity: 0;">
      <img src="todolistIconImage.png" alt="to do list icon image">
    </div>
    <h2 style="opacity: 0;">To-Do List</h2>
  </div>
  <div class="header homepage-header-bottom">
    <div class="nav-bar" style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 0; width: 99%;">
      <a style="border-radius: 0" href="homePage.php">Home Page</a>
      <a style="border-radius: 0" href="ToDoList.php">My To-Do List</a>
      <?php
      if ($ifManager) {
        echo '<a style="border-radius: 0" href="managerProjectView.php">View Projects</a>';
      } else if (!$ifManager) {
        echo '<a style="border-radius: 0" href="employeeProjectView.php">My Projects</a>';
      }
      ?>
      <a style="border-radius: 0" href="CommunityPostsMain.php">Community Posts</a>
    </div>
  </div>

  <div class="overlay" id="overlay"></div>
  <div class="container">

    <!-- FILTER/SORT PANEL -->
    <div class="left-sidebar">
      <form id="filterForm" > <!--form for filtering tasks-->
        <h2>Sort By:</h2>
        <input type="radio" checked id="recentAdded" name="sortBy" value="recentAdded"><label for="recentAdded">Recently Added</label><br>
        <input type="radio" id="mostRecent" name="sortBy" value="mostRecent"><label for="mostRecent">Deadline</label><br>
        <input type="radio" id="mostImportant" name="sortBy" value="mostImportant"><label for="mostImportant">Most Important</label><br>
        <input type="radio" id="leastImportant" name="sortBy" value="leastImportant"><label for="leastImportant">Least Important</label><br>
        <h2>Filter By:</h2>
        <p>Priority:</p>
        <input type="checkbox" id="veryhigh" name="veryHighPriority" value="veryhigh"><label>Very High</label><br>
        <input type="checkbox" id="high" name="highPriority" value="high"><label>High</label><br>
        <input type="checkbox" id="low" name="lowPriority" value="low"><label>Low</label><br><br>
        <button type='submit' id='submitButton' >Apply</button>
      </form>
    </div>

    <!-- DISPLAY TASKS PANEL -->
    <div class="main">
      <h2>To-Do List</h2>

      <ul class="no-bullets" id="tasks-list"> <!--ensures no bullet points/indentation-->
        <div class="tasks-element">
          <br>
          <?php echo "<div onload=\"sendEmployeeID($employeeID)\"></div>" ?>
          <?php include "toDoTasksGenerator.php" ?><!--generates to-do tasks from database-->
        </div>
      </ul>
    </div>

    <!-- USER ACTIONS PANEL -->
    <div class="right-sidebar">
      <h2>Actions</h2>
      <!-- Overlay -->
      <div class="overlay" id="overlay"></div>

      <!-- Button to trigger the form, darkens when mouse hovered over it -->
      <button onclick="openForm()" style="padding: 15% 40%;border-radius: 10px; cursor: pointer;border: none;grid-area: title;
          background-color: rgba(248, 220, 2, 255); font-weight: bold; font-size: 18px;"
          onmouseover="this.style.backgroundColor='rgba(218, 190, 0, 255)';"
          onmouseout="this.style.backgroundColor='rgba(248, 220, 2, 255)';">Add Task</button>

      <!-- Pop-up form -->
      <div class="pop-up" id="pop-up">
        <form id="myForm" action="toDo.php" method="get" class="form-control">
          <h2>Create Task</h2>
          <div class="right-hand">
            <!-- Priority selection -->
            <div class="divPriority">
              <label>Priority</label>
              <br>
              <label for="low" id="radioLabel">Low</label>
              <input type="radio" name="priority" value="low">
              <label for="high" id="radioLabel">High</label>
              <input type="radio" name="priority" value="high">
              <label for="veryhigh" id="radioLabel">Very High</label>
              <input type="radio" name="priority" value="veryhigh">
              <br>
            </div>
            <!-- Deadline input -->
            <div class="divDeadline">
              <label for="deadlineDate">Deadline</label>
              <br>
              <input type="date" name="deadlineDate" value="" required>
              <input type="time" name="deadlineTime" value="00:00">
            </div>
            <div class="subTaskDiv" id="subTaskDiv">
              <label for="subTasks">Sub-Tasks</label>
              <input type="text" name="subTasks" id="subTasks">
              <button type="button" onclick="addSubTask(document.getElementById('subTasks').value)">Add Sub-Task</button>
              <p id="noOfSubtasks"></p>
              <input type="hidden" id="no-of-subtasks" name="no-of-subtasks">
            </div>
          </div>

          <div class="left-hand">
            <!-- Heading input -->
            <label for="heading">Heading</label>
            <br>
            <input type="text" id="heading" name="heading" required>
            <!-- Description input -->
            <label for="description">Description</label>
            <br>
            <textarea rows="10" cols="40" id="description" name="description" required></textarea>
          </div>

          <!-- Submit and cancel buttons -->
          <button type="submit" onclick="changeBackToDoPage()">Submit</button>
          <button type="button" onclick="closeForm()">Cancel</button>
        </form>
      </div>

      <script>
        //display form and and grey out background using overlay
        function openForm() {
          document.getElementById("pop-up").style.display = "block";
          document.getElementById("overlay").style.display = "block";
          setTimeout(() => {
            document.getElementById("overlay").style.backgroundColor = "rgba(0, 0, 0, 0.5)"; //fade-in effect
          }, 10);
        }

        //close form and return page to normal brightness
        function closeForm() {
          document.getElementById("pop-up").style.display = "none";
          document.getElementById("overlay").style.backgroundColor = "rgba(0, 0, 0, 0)";
          setTimeout(() => {
            document.getElementById("overlay").style.display = "none"; // fade-out effect
          }, 300);
          document.getElementById('myForm').reset();
        }

        //display subtasks to user when adding them to the form
        function addSubTask(sTask) {
          let no_of_tasks = document.createElement('p');
          let count = document.querySelectorAll('.subTaskDiv > li').length + 1;
          document.getElementById("noOfSubtasks").innerHTML = "Number of Subtasks: " + count
          document.getElementById('no-of-subtasks').value = count; //hidden input for number of sub-tasks - for server side processing
          const list = document.createElement('li')
          const input = document.createElement('input') //hidden input for list of sub-task names - for server side processing
          input.type = "hidden";
          input.name = "subtasks[]";
          input.value = sTask;
          const task = document.createTextNode(sTask); //text node for dynamic displaying sub-task name to user
          list.append(input);
          list.append(task);
          document.getElementById('subTaskDiv').appendChild(list); //appending displays new subtask on the form 
        }
      </script>
      <p></p>
    </div>
  </div>

  <script defer>
    //add event listeners to all subtasks
    let list = document.querySelectorAll(".subtask > ul > li");
    list.forEach(li => {
      li.addEventListener('click', completedSubTask);
    });
    
    //function to toggle subtask completion and make AJAX request to update database
    function completedSubTask(event) {
      let checkbox = event.currentTarget.querySelector('input[type="checkbox"]');
      checkbox.checked = !checkbox.checked; //toggle the checked state of checkbox when subtask area is clicked
      subTaskID = event.currentTarget.value; //subtask marked with value = subTaskID upon generation

      if (checkbox.checked) {
        var xhr = new XMLHttpRequest();
        var parameterValue = subTaskID; //subTaskID to be passed
        var status = "1";
        xhr.open('GET', 'updateSubTask.php?check=' + status + '&parameter=' + parameterValue, true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText); // Print response to console
          }
        };
        xhr.send();
        event.currentTarget.classList.add('ticked'); //change subtask to green and check it
      } else {
        var xhr = new XMLHttpRequest();
        var parameterValue = subTaskID; // subTaskID to be passed
        var status = "0";
        xhr.open('GET', 'updateSubTask.php?check=' + status + '&parameter=' + parameterValue, true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText); // Print response to console
          }
        };
        xhr.send();
        event.currentTarget.classList.remove('ticked'); //changes subtask to grey and uncheck it
      }
    };

    //AJAX request to send employeeID to toDoTasksGenerator.php - to get tasks for that employee
    function sendEmployeeID(employeeID) {
      alert("this runs!");
      var xhr = new XMLHttpRequest();
      var parameterValue = employeeID; // Your value to be passed
      xhr.open('GET', 'toDoTasksGenerator.php?parameter=' + parameterValue, true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          console.log(xhr.responseText); // Print response to console
          console.log("compl"); //
        }
      };
      xhr.send();
    }

    function changeBackToDoPage() {
      alert("Task Added!");
      window.location.href = "ToDoList.php"
    }

    //unused function
    function changePage() {
      window.location.href = "Create_Task.html"
    }

    //updates task status on the database
    function completeTask() {
      let button = event.target;
      let ifComplete = button.closest('li').classList[1];
      let toDoID = button.closest('li').id;

      if (ifComplete == "incomplete") {
        //change task's class to complete instead of incomplete
        button.classList.remove('incomplete');
        button.classList.add('complete');
        //AJAX request to database
        var xhr = new XMLHttpRequest();
        var parameterValue = toDoID; // Task ID to be passed
        xhr.open('GET', 'markTaskComplete.php?complete=' + ifComplete + '&parameter=' + parameterValue, true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText); 
          }
        };
        xhr.send();
        location.reload();
      }
    }

    // AJAX request to delete tasks from database and the page
    function deleteTask() {
      let message = "Press OK to delete task";
      if (confirm(message) == true) {
        let priority = event.target.closest('li').classList[0];
        let toDoID = event.target.closest('li').id;
        console.log("will delete" + toDoID);
        var xhr = new XMLHttpRequest();
        var parameterValue = toDoID; // Task ID to be passed
        xhr.open('GET', 'deleteToDoTask.php?parameter=' + parameterValue, true);
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText); 
          }
        };
        xhr.send();
        alert("Task Deleted!");
        location.reload();
      } else {
        console.log("not deleted"); 
      }
    }
  </script>

  <script>
    // script for collapsing taskbox
    var tasks = document.getElementsByClassName("title-container");
    var i;

    for (i = 0; i < tasks.length; i++) {
      tasks[i].addEventListener("click", function() {
        var content = this.nextElementSibling;
        if (content.style.display === "grid") {
          content.style.display = "none";
        } else {
          content.style.display = "grid";
        }
      });
    }
  </script>

</body>

</html>