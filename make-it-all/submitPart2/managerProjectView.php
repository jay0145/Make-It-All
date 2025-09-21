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
  <link rel="stylesheet" href="css/adminDashboardStyles.css">
  <link rel="stylesheet" href="css/twoColumnStyles.css">
  <link rel="stylesheet" href="css/generalStyles.css">
  <link rel="stylesheet" href="css/homePageStyles.css">
  <link rel="stylesheet" href="css/pop-upstyles.css">
  <link rel="stylesheet" href="css/createProjectstyles.css">
  <link rel="stylesheet" href="css/addActivitystyles.css">
  <link rel="stylesheet" href="css/createTeamstyles.css">
  <link rel="stylesheet" href="css/DeleteProjectstyles.css">
  <link rel="stylesheet" href="css/removeActivitystyles.css">
  <link rel="stylesheet" href="css/managerButtons.css">
  <!-- Include jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Include Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

  <title>Manager Project View</title>
</head>

<style>
  /* Some essential styling for the overlay, i.e making the background darker in 3ms
          The styling for the other ones are self-explanatory
       */
  .overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0);
    transition: background-color 0.3s ease;
  }

  .pop-up {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: none;
    background-color: rgba(215, 215, 215, 255);
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    border-radius: 25px;
    padding: 20px;
    width: 60%;
  }

  #myForm {
    display: none;
  }

  .projectContainer {
    display: grid;
    grid-template-columns: 19fr 1fr;
    grid-gap: 20px;
    padding: 10px;
    margin-bottom: 10px;
    background-color: rgba(246, 217, 0, 255);
    border-radius: 15px;
  }

  .bottomMarginOnly {
    margin-top: 0px;
    margin-bottom: 10px;
  }

  .smallBottomMarginSmallText {
    margin-top: 10px;
    margin-bottom: 5px;
    font-size: 13px;
  }

  .longBottomMarginOnly {
    margin-top: 0px;
    margin-bottom: 40px;
  }

  .noMargin {
    margin: 0px;
  }

  .doubleBreak {
    content: "";
    margin: 2em;
    display: block;
    font-size: 24%;
  }
</style>

<body>
  <div class="icon-container" style="padding: 1px; align-items: center; display: flex; justify-content: space-between">
    <div class="employee-info-container"
      style="margin-left: 13px; font-size: 15px; color: white; text-align: left; font-weight: bold;">
      <?php
      echo "<p>Employee ID No: $employeeID<br>Department: $employeeDepartment</p>";
      ?>
    </div>
    <a class="btn btn-dark" style="margin-right: 16px; font-size: 18px; color: black; text-decoration: none; padding: 5px 10px; 
  background-color: rgba(217,154,1,255); border-radius: 5px; transition: background-color 0.3s; font-weight: bold;"
      href="Login_Page.html">Log Out</a>
  </div>
  <div class="header homepage-header-top">
    <div class="left-content">
      <img src="Images/managerIconImage.png" alt="manager icon image">
    </div>
    <h2>View Projects</h2>
    <div class="center-content">
      <img src="Images/makeItAllLogoImage.png" alt="make it all logo image">
    </div>
    <div class="left-content" style="opacity: 0;">
      <img src="Images/managerIconImage.png" alt="manager icon image">
    </div>
    <h2 style="opacity: 0;">View Projects</h2>
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
    <div class="left-sidebar">
      <h1>Projects</h1>

      <?php
      $query = "SELECT employeeID FROM employeeTable WHERE employeeEmail = '" . $email . "';";

      $query_result = mysqli_query($conn, $query);

      $currentEmployeeID = mysqli_fetch_all($query_result)[0][0];

      $conn = mysqli_connect("localhost", "team018", "nkAfiVuTsC4Yw9LvLEgP", "team018");

      $query = "SELECT projectID, title, description, deadline, employeeDepartment
      FROM projectTable
      WHERE managerID =" . $currentEmployeeID . ";";

      $query_result = mysqli_query($conn, $query);

      $projects = mysqli_fetch_all($query_result);

      $query = "SELECT DISTINCT employeeDepartment FROM projectTable;";
      $query_result = mysqli_query($conn, $query);

      $departments = mysqli_fetch_all($query_result);

      // Outer Loop to go through each department
      foreach ($departments as $department) {
        echo '<h2 id="' . $department[0] . '" style="display: inline; margin-right: 10px;">' . $department[0] . '</h2>
          <button id="' . $department[0] . 'Button" class="bottomMarginOnly">Hide Complete Projects</button>';
        $hide = true;
        // Inner loop that tests if the project is in the given department and then echoes its HTML if so
      
        foreach ($projects as $project) {
          $departmentTitle = $project[4];
          if ($department[0] == $departmentTitle) {
            $hide = false;
            $projectID = $project[0];
            $projectHTMLName = "project" . $projectID;
            $projectTitle = $project[1];
            $projectDesc = $project[2];
            $dueDate = $project[3];

            $projectTeam = "Project Team: ";

            $query = "SELECT employeeFirstName, employeeSurname 
              FROM employeeTable, teamEmployeeTable, projectTeamTable 
              WHERE employeeTable.employeeID = teamEmployeeTable.employeeID 
              AND teamEmployeeTable.teamID = projectTeamTable.teamID 
              AND projectTeamTable.projectID = " . $projectID . "
              
              UNION
              
              SELECT employeeFirstName, employeeSurname 
              FROM employeeTable, projectEmployeeTable 
              WHERE employeeTable.employeeID = projectEmployeeTable.employeeID 
              AND projectEmployeeTable.projectID = " . $projectID . ";";

            $query_result = mysqli_query($conn, $query);

            $teamMembers = mysqli_fetch_all($query_result);

            if (count($teamMembers) == 1) {
              $projectTeam = $projectTeam . ' (Individual) ' . $teamMembers[0][0] . ' ' . $teamMembers[0][1];
            } else {
              foreach ($teamMembers as $teamMember) {
                if ($projectTeam == "Project Team: ") {
                  $projectTeam = $projectTeam . $teamMember[0] . ' ' . $teamMember[1];
                } else {
                  $projectTeam = $projectTeam . ", " . $teamMember[0] . ' ' . $teamMember[1];
                }
              }
            }

            echo '<div class="projectContainer" id="' . $projectHTMLName . '">
                <div>
                  <h3 class="bottomMarginOnly">' . $projectTitle . '</h3>
                  ' . $projectDesc . '<br>
                  <h5 style="margin-top: 10px;
                margin-bottom: 0px;">' . $projectTeam . '<br>Due: ' . $dueDate . '</h5>

                </div>
                <div class="progress-bar-container">
                  <div class="progress-bar ' . $projectHTMLName . '">
                  </div>
                </div>
              </div>';

            $totalSubActivityWeight = 0;
            $totalCompletedSubActivityWeight = 0;

            $query = "SELECT subActivityID, subActivityTitle, description, deadline, completeness, employeeID, weight FROM subActivityTable WHERE projectID = " . $projectID . ";";
            $query_result = mysqli_query($conn, $query);

            $subActivities = mysqli_fetch_all($query_result);

            echo '<div class="pop-up" id="Pop-up' . $projectID . '">
              <div class="myForm" id="myForm' . $projectID . '">
                <form action="">
                  <h1 class="bottomMarginOnly">' . $projectTitle . '</h1>
                  <h3 class="bottomMarginOnly">Activities</h3>';

            foreach ($subActivities as $subActivity) {
              $subActivityID = $subActivity[0];
              $subActivityTitle = $subActivity[1];
              $subActivityDescription = $subActivity[2];
              $subActivityDueDate = $subActivity[3];
              $subActivityCompleteness = $subActivity[4];
              $subActivityEmployeeID = $subActivity[5];
              $subActivityWeight = $subActivity[6];

              $totalSubActivityWeight += $subActivityWeight;
              $totalCompletedSubActivityWeight += $subActivityCompleteness * $subActivityWeight;

              $query = "SELECT employeeFirstName, employeeSurname FROM employeeTable, subActivityTable
                WHERE employeeTable.employeeID = subActivityTable.employeeID AND subActivityTable.subActivityID = " . $subActivityID . ";";
              $query_result = mysqli_query($conn, $query);

              $subActivityDoerArray = mysqli_fetch_all($query_result);
              $subActivityDoer = $subActivityDoerArray[0][0] . " " . $subActivityDoerArray[0][1];

              if ($subActivityCompleteness == 1) {
                $completenessHTML = '<p class="longBottomMarginOnly" style="color: green;">Complete</p>';
              } else {
                $completenessHTML = '<p class="longBottomMarginOnly" style="color: red;">Incomplete</p>';
              }

              echo '<div class="subAcitity">
                      <h4 class="noMargin">' . $subActivityTitle . '</h4>
                      <br class="doubleBreak">
                      <p class="noMargin">' . $subActivityDescription . '</p>
                      <br class="doubleBreak">
                      <p class="noMargin">Task assigned to: ' . $subActivityDoer . '.</p>
                      <p class="smallBottomMarginSmallText">Due: ' . $subActivityDueDate . '</p>
                      ' . $completenessHTML . '
                      </div>';
            }

            echo '<h5 class="topMarginOnly">' . $projectTeam . '<br>
              Due: ' . $dueDate . '</h5><br>
              <button type="submit" onclick="closeForm' . $projectID . '()">Close</button>
              </form>
            </div>
          </div>';

            $overallCompletenessPercent = 0;
            if ($totalSubActivityWeight != 0) {
              $overallCompletenessPercent = round($totalCompletedSubActivityWeight / $totalSubActivityWeight * 100);
            }

            echo '<style>
              @keyframes ' . $projectHTMLName . '-progress {
                from {
                  --progress-value: 0;
                }
                to {
                  --progress-value: ' . $overallCompletenessPercent . ';
                }
              }

              .' . $projectHTMLName . ' {
                animation: ' . $projectHTMLName . '-progress 0.5s forwards;
              }

              .' . $projectHTMLName . '::before {
                animation: ' . $projectHTMLName . '-progress 0.5s forwards;
              }

              #' . $projectHTMLName . ' {
                cursor: pointer;
              }
            </style>';

            echo '<script>
                function openForm' . $projectID . '() {
                  document.getElementById("myForm' . $projectID . '").style.display = "block";
                  document.getElementById("Pop-up' . $projectID . '").style.display = "block";
                  // The above line makes sure that the form itself is not darkened, only the background
              
                  let backgroundOverlay = document.getElementById("overlay");
                  backgroundOverlay.style.display = "block";
                  setTimeout(() => {
                    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
                  }, 10);
                  // Above line makes the transition between dimming and brightened less jarring
                }

                function closeForm' . $projectID . '() {
                  document.getElementById("myForm' . $projectID . '").style.display = "none";
                  document.getElementById("pop-up' . $projectID . '").style.display = "none";
              
                  let backgroundOverlay = document.getElementById("overlay");
                  backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
                  setTimeout(() => {
                    backgroundOverlay.style.display = "none";
                  }, 300);
                }

                var project' . $projectID . 'Div = document.getElementById("project' . $projectID . '");

                project' . $projectID . 'Div.addEventListener("click", openForm' . $projectID . ');
              </script>';
          }
        }
        if ($hide == true) {
          echo '<script>
              var departmentHeader = document.getElementById("' . $department[0] . '");
              departmentHeader.style.display = "none";
              var departmentButton = document.getElementById("' . $department[0] . 'Button");
              departmentButton.style.display = "none";
            </script>';
        }

        echo '<script>
            document.getElementById("' . $department[0] . 'Button").addEventListener("click", function(event) {
            event.preventDefault(); // Prevent default form submission behavior
        
            var content = document.getElementById("' . $department[0] . 'Button").textContent;
        
            var newCompleteness = "0"
            if (content == "Hide Complete Projects") {
              document.getElementById("' . $department[0] . 'Button").textContent = "Show Complete Projects"
            } else {
              document.getElementById("' . $department[0] . 'Button").textContent = "Hide Complete Projects"
            }
        
            });
          </script>';
      }

      ?>
    </div>

    <div class="right-sidebar">
      <h2 style="text-align: center;">Actions</h2>
      <p style="text-decoration: underline;
      color: rgba(100,99,99,255);

      line-height: 25px;">
      <form id="projectForm" style="display: none;" method="post" action="createProjectphp.php">
        <div id="create-project-popup">
          <div>
            <span>Create Project</span>
            <button type="button" onclick="closeCreateProject()">Close</button>
          </div>

          <!-- Project name -->
          <label for="projectName">Project Name:</label>
          <input type="text" id="projectName" name="projectName" required>

          <!-- Employee department -->
          <label for="employeeDepartment">Employee Department:</label>
          <select id="employeeDepartment" name="employeeDepartment" required>
            <option value="Technical">Technical</option>
            <option value="Sales">Sales</option>
            <option value="Admin">Admin</option>
          </select>

          <!-- Project description -->
          <label>
            <span>Project Description:</span>
            <textarea id="projectDescription" name="projectDescription" required></textarea>
          </label>

          <!-- Project employees -->
          <label for="employeeListDropdown">Add Employees:</label>
          <select id="employeeListDropdown" name="employeeList[]" multiple style="width: 200px;" required></select>
          <ul id="employeeList"></ul>

          <!-- Project team -->
          <label for="teamListDropdown">Add team(s):</label>
          <select id="teamListDropdown" name="teamList[]" multiple style="width: 200px;" required></select>
          <ul id="teamList"></ul>

          <!-- Project Deadline -->
          <label for="projectDeadline">Project Deadline:</label>
          <input type="date" id="projectDeadline" name="projectDeadline" required>

          <!-- Submit button -->
          <button type="submit">Submit</button>
        </div>
      </form>
      <button id='create-project-button' onclick="createProject()">Create Project</button>
      <!-- make this a button that opens the form -->

      <form id="createTeam" style="display: none;" method="post" action="createTeamphp.php">
        <div id="create-team-popup">
          <div>
            <span>Create Team</span>
            <button id="create-team-button" type="button" onclick="closeCreateTeam()">Close</button>
          </div>

          <!-- Team name -->
          <label for="teamName">Team Name:</label>
          <input type="text" id="teamName" name="teamName" required>

          <!-- Team members -->
          <label for="teamMemberDropdown">Team Members:</label>
          <select id="teamMemberDropdown" name="teamList[]" multiple style="width: 200px;" required></select>
          <ul id="teamMemberList"></ul>

          <!--Submit Button -->
          <button type="submit">Submit</button>
        </div>
      </form>
      <button id="create-team-button2" onclick="createTeam()">Create Team</button>
      <!-- make this a button that opens the form -->

      <form id="addActivityForm" style="display: none;" method="post" action="addActivity.php">
        <div id="add-activity-popup">
          <div>
            <span>Add Activity to Project</span>
            <button type="button" onclick="closeAddActivity()">Close</button>
          </div>

          <br>
          <!-- Project dropdown -->
          <label for="projectDropdown">Select Project:</label>
          <select id="projectDropdown" name="projectListID" style="width: 400px;" required data-clearable></select>
          <ul id="projectListID"></ul>

          <!-- Activity employee Dropdown -->
          <label for="activityEmployee">Select Employee:</label>
          <select id="activityEmployee" name="activityEmployee[]" style="width: 400px;" required data-clearable multiple></select>
          <ul id="activityEmployeeID"></ul>

          <!-- Activity title -->
          <label for="activityTitle">Title:</label>
          <input type="text" id="activityTitle" name="activityTitle" required>

          <!-- Activity description -->
          <label>
            <span>Description:</span>
            <textarea id="activityDescription" name="activityDescription" required></textarea>
          </label>

          <!-- Activity deadline -->
          <label for="activityDeadline">Deadline:</label>
          <input type="date" id="activityDeadline" name="activityDeadline" required>

          <!-- Weight of subactivity dropdown -->
          <label for="subActivityWeight">Weight of Subactivity:</label>
          <select id="subActivityWeight" name="subActivityWeight" required>
            <option value=1>Lowest Priority</option>
            <option value=2>Low Priority</option>
            <option value=3>Medium Priority</option>
            <option value=4>High Priority</option>
            <option value=5>Very High Priority</option>
          </select>

          <!-- Submit button -->
          <button type="submit">Submit</button>
        </div>
      </form>
      <button id='add-activity-button' onclick="openAddActivity()">Add Activity</button>
      <!-- make this a button that opens the form -->

      <form id="removeActivityForm" method="post" action="removeActivity.php">
        <div id="remove-activity-popup">
          <div>
            <span>Remove Activity from Project</span>
            <button type="button" onclick="closeRemoveActivity()">Close</button>
          </div>

          <!-- Project dropdown -->
          <label for="projectDropdown2">Select Project:</label>
          <select id="projectDropdown2" name="ProjectID2" style="width: 400px;" required></select>
          <ul id="ProjectID2"></ul>

          <!-- Activity dropdown -->
          <label for="activityDropdown">Select Activity:</label>
          <select id="activityDropdown" name="ActivityID" style="width: 400px;" required></select>
          <ul id="ActivityID"></ul>

          <input type="hidden" id="project-id">

          <!-- Submit button -->
          <button type="submit">Remove</button>
        </div>
      </form>
      <button id='remove-activity-button' onclick="openRemoveActivity()">Remove Activity</button>
      <!-- make this a button that opens the form -->

      <form id="deleteProjectForm" method="post" action="deleteProject.php">
        <div id="delete-project-popup">
          <div>
            <span>Delete Project</span>
            <button type="button" onclick="closeDeleteProject()">Close</button>
          </div>

          <!-- Project dropdown -->
          <label for="deleteProjectDropdown">Select Project:</label>
          <select id="deleteProjectDropdown" name="deleteProjectID" style="width: 400px;" required></select>
          <ul id="deleteProjectID"></ul>

          <!-- Submit button -->
          <button type="submit">Delete</button>
        </div>
      </form>
      <button id='delete-project-button' onclick="DeleteProject()">Delete Project</button>
      <!-- make this a button that opens the form -->

    </div>
  </div>
</body>

<script>

  //openRemoveActivity
  function openRemoveActivity() {
    document.getElementById("remove-activity-popup").style.display = "block";
    document.getElementById("removeActivityForm").style.display = "block";
    document.getElementById("remove-activity-popup").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }

  //CloseRemoveActivity
  function closeRemoveActivity() {
    document.getElementById("remove-activity-popup").style.display = "none";
    document.getElementById("remove-activity-popup").style.backdropFilter = "";
    document.getElementById("removeActivityForm").style.display = "none";
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }

  //DeleteProject
  function DeleteProject() {
    document.getElementById("delete-project-popup").style.display = "block";
    document.getElementById("deleteProjectForm").style.display = "block";
    document.getElementById("delete-project-popup").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }

  //CloseDeleteProject
  function closeDeleteProject() {
    document.getElementById("delete-project-popup").style.display = "none";
    document.getElementById("delete-project-popup").style.backdropFilter = "";
    document.getElementById("deleteProjectForm").style.display = "none";
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }

  //addActivity
  function openAddActivity() {
    document.getElementById("add-activity-popup").style.display = "block";
    document.getElementById("addActivityForm").style.display = "block";
    document.getElementById("add-activity-popup").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }

  //closeAddActivity
  function closeAddActivity() {
    document.getElementById("add-activity-popup").style.display = "none";
    document.getElementById("add-activity-popup").style.backdropFilter = "";
    document.getElementById("addActivityForm").style.display = "none";

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }

  //CreateTeam
  function createTeam() {
    document.getElementById("create-team-popup").style.display = "block";
    document.getElementById("createTeam").style.display = "block";
    document.getElementById("create-team-popup").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }

  //CloseCreateTeam
  function closeCreateTeam() {
    document.getElementById("create-team-popup").style.display = "none";
    document.getElementById("create-team-popup").style.backdropFilter = "";
    document.getElementById("createTeam").style.display = "none";
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }

  //CreatePost
  function createProject() {
    document.getElementById("create-project-popup").style.display = "block";
    document.getElementById("projectForm").style.display = "block";
    document.getElementById("create-project-popup").style.backdropFilter = "blur(8px)";
    // document.getElementById("contentForm").style.backdropFilter = "blur(8px)";       
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }

  function closeCreateProject() {
    document.getElementById("create-project-popup").style.display = "none";
    document.getElementById("create-project-popup").style.backdropFilter = "";
    document.getElementById("projectForm").style.display = "none";
    // document.getElementById("contentForm").style.backdropFilter = ""; 
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }


  // AJAX for getting employeeList from database
  $(document).on('click', '#create-project-button', function () {
    $.ajax({
      url: 'employeeList.php',
      type: 'post',
      success: function (response) {
        // Store the response in the DropdownEmployeeList variable
        var DropdownEmployeeList = JSON.parse(response);
        console.log(DropdownEmployeeList);

        // Clear the dropdown
        $('#employeeListDropdown').empty();

        // Populate the dropdown with the data
        for (var i = 0; i < DropdownEmployeeList.length; i++) {
          var option = new Option('ID: ' + DropdownEmployeeList[i].employeeID + ' - ' + DropdownEmployeeList[i].employeeFirstName + ' ' + DropdownEmployeeList[i].employeeSurname, DropdownEmployeeList[i].employeeID);
          console.log(option); // Check each option
          $('#employeeListDropdown').append($(option));
        }

        // Initialize Select2 on the dropdown
        $('#employeeListDropdown').select2({
          placeholder: 'Select employees',
          allowClear: true
        });
      }
    });
  });

  // AJAX for getting employeeList from database
  $(document).on('click', '#create-team-button2', function () {
    $.ajax({
      url: 'employeeList.php',
      type: 'post',
      success: function (response) {
        // Store the response in the DropdownEmployeeList variable
        var DropdownEmployeeList = JSON.parse(response);
        console.log(DropdownEmployeeList);

        // Clear the dropdown
        $('#teamMemberDropdown').empty();

        // Populate the dropdown with the data
        for (var i = 0; i < DropdownEmployeeList.length; i++) {
          var option = new Option('ID: ' + DropdownEmployeeList[i].employeeID + ' - ' + DropdownEmployeeList[i].employeeFirstName + ' ' + DropdownEmployeeList[i].employeeSurname, DropdownEmployeeList[i].employeeID);
          console.log(option); // Check each option
          $('#teamMemberDropdown').append($(option));
        }

        // Initialize Select2 on the dropdown
        $('#teamMemberDropdown').select2({
          placeholder: 'Select team members',
          allowClear: true
        });
      }
    });
  });

  // AJAX for getting teamList from database
  $(document).on('click', '#create-project-button', function () {
    $.ajax({
      url: 'teamList.php',
      type: 'post',
      success: function (response) {
        // Store the response in the DropdownTeamList variable
        var DropdownTeamList = JSON.parse(response);
        console.log(DropdownTeamList);

        // Clear the dropdown
        $('#teamListDropdown').empty();

        // Populate the dropdown with the data
        for (var i = 0; i < DropdownTeamList.length; i++) {
          var option = new Option('ID: ' + DropdownTeamList[i].teamID + ' - ' + DropdownTeamList[i].teamName, DropdownTeamList[i].teamID);
          console.log(option); // Check each option
          $('#teamListDropdown').append($(option));
        }

        // Initialize Select2 on the dropdown
        $('#teamListDropdown').select2({
          placeholder: 'Select team',
          allowClear: true
        });
      }
    });
  });

  // AJAX for getting projectList from database
  $(document).on('click', '#add-activity-button', function () {
    $.ajax({
      url: 'projectList.php',
      type: 'post',
      success: function (response) {
        // Store the response in the projectDropdown variable
        var projectDropdown = JSON.parse(response);
        console.log(projectDropdown);

        // Clear the dropdown
        $('#projectDropdown').empty();

        // Populate the dropdown with the data
        for (var i = 0; i < projectDropdown.length; i++) {
          var option = new Option(projectDropdown[i].title + ' - ID: ' + projectDropdown[i].projectID, projectDropdown[i].projectID);
          console.log(option); // Check each option
          $('#projectDropdown').append($(option));
        }

        // Initialize Select2 on the dropdown
        $('#projectDropdown').select2({
          placeholder: 'Select Project',
          allowClear: true
        });
      }
    });
  });

  // AJAX for getting projectList from database
  $(document).on('click', '#delete-project-button', function () {
    $.ajax({
      url: 'projectList.php',
      type: 'post',
      success: function (response) {
        // Store the response in the projectDropdown variable
        var projectDropdown = JSON.parse(response);
        console.log(projectDropdown);

        // Clear the dropdown
        $('#deleteProjectDropdown').empty();

        // Populate the dropdown with the data
        for (var i = 0; i < projectDropdown.length; i++) {
          var option = new Option(projectDropdown[i].title + ' - ID: ' + projectDropdown[i].projectID, projectDropdown[i].projectID);
          console.log(option); // Check each option
          $('#deleteProjectDropdown').append($(option));
        }

        // Initialize Select2 on the dropdown
        $('#deleteProjectDropdown').select2({
          placeholder: 'Select Project',
          allowClear: true
        });
      }
    });
  });

  // AJAX for getting projectList from database
  $(document).on('click', '#remove-activity-button', function () {
    $.ajax({
      url: 'projectList.php',
      type: 'post',
      success: function (response) {
        // Store the response in the projectDropdown variable
        var projectDropdown = JSON.parse(response);
        console.log(projectDropdown);

        // Clear the dropdown
        $('#projectDropdown2').empty();

        // Populate the dropdown with the data
        for (var i = 0; i < projectDropdown.length; i++) {
          var option = new Option(projectDropdown[i].title + ' - ID: ' + projectDropdown[i].projectID, projectDropdown[i].projectID);
          console.log(option); // Check each option
          $('#projectDropdown2').append($(option));
        }

        // Initialize Select2 on the dropdown
        $('#projectDropdown2').select2({
          placeholder: 'Select Project',
          allowClear: true
        });
      }
    });
  });

  $('#projectDropdown2').change(function () {
    var projectId = $(this).val();
    console.log(projectId);

    $.ajax({
      url: 'activityList.php',
      type: 'post',
      data: {
        'project-id': projectId
      },
      success: function (response) {
        console.log(response);
        var activityList = JSON.parse(response);

        // Clear the activity dropdown
        $('#activityDropdown').empty();

        // Populate the activity dropdown with the data
        for (var i = 0; i < activityList.length; i++) {
          var option = new Option('ID: ' + activityList[i].subActivityID + ' - ' + activityList[i].subActivityTitle, activityList[i].subActivityID);
          $('#activityDropdown').append($(option));
        }
        // Initialize Select2 on the dropdown
        $('#activityDropdown').select2({
          placeholder: 'Select Activity',
          allowClear: true
        });
      }
    });
  });

  // select employee(s) for activity Dropdown
  $('#projectDropdown').change(function () {
    var projectId = $(this).val();
    console.log(projectId);

    $.ajax({
      url: 'activityEmployeeList.php',
      type: 'post',
      data: {
        'projectListID': projectId
      },
      success: function (response) {
        console.log(response);
        var activityEmployeeList = JSON.parse(response);

        // Clear the activity dropdown
        $('#activityEmployee').empty();

        // Populate the activity dropdown with the data
        for (var i = 0; i < activityEmployeeList.length; i++) {
          var option = new Option('ID: ' + activityEmployeeList[i].employeeID + ' - ' + activityEmployeeList[i].employeeFirstName + ' ' + activityEmployeeList[i].employeeSurname, activityEmployeeList[i].employeeID);
          $('#activityEmployee').append($(option));
        }
        // Initialize Select2 on the dropdown
        $('#activityEmployee').select2({
          placeholder: 'Select Employee(s)',
          allowClear: true
        });
      }
    });
  });
</script>

<script>
  function openForm() {
    document.getElementById("myForm").style.display = "block";
    document.getElementById("Pop-up").style.display = "block";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }

  function closeForm() {
    document.getElementById("myForm").style.display = "none";
    document.getElementById("pop-up").style.display = "none";

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }
</script>

</html>