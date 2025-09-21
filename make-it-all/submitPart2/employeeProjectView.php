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
  <title>Employee Project View</title>
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
      <img src="Images/homePageIconImage.png" alt="home image">
    </div>
    <h2>Home</h2>
    <div class="center-content" >
      <img src="Images/makeItAllLogoImage.png" alt="make it all logo image">
    </div>
	<div class="left-content" style="opacity: 0;">
      <img src="Images/homePageIconImage.png" alt="home image">
    </div>
	<h2 style="opacity: 0;">Home</h2>
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

  <div class="container" style="grid-template-columns: 1fr;">
    <div class="left-sidebar">
      <h1>Projects</h1>

      <?php
        $query = "SELECT employeeID FROM employeeTable WHERE employeeEmail = '". $email ."';";

        $query_result = mysqli_query($conn, $query);

        $currentEmployeeID = mysqli_fetch_all($query_result)[0][0];

        $conn = mysqli_connect("localhost", "team018", "nkAfiVuTsC4Yw9LvLEgP", "team018");

        $query = "SELECT projectTable.projectID, title, description, deadline, projectTable.employeeDepartment
        FROM projectTable, employeeTable, projectEmployeeTable
        WHERE employeeTable.employeeID = projectEmployeeTable.employeeID
        AND projectTable.projectID = projectEmployeeTable.projectID
        AND employeeTable.employeeID = ". $currentEmployeeID ."
        
        UNION
        
        SELECT projectTable.projectID, title, description, deadline, projectTable.employeeDepartment
        FROM projectTable, employeeTable, teamEmployeeTable, projectTeamTable
        WHERE employeeTable.employeeID = teamEmployeeTable.employeeID
        AND projectTable.projectID = projectTeamTable.projectID
        AND employeeTable.employeeID = ". $currentEmployeeID .";";

        $query_result = mysqli_query($conn, $query);

        $projects = mysqli_fetch_all($query_result);

        $query = "SELECT DISTINCT employeeDepartment FROM projectTable;";
        $query_result = mysqli_query($conn, $query);

        $departments = mysqli_fetch_all($query_result);

        // Outer Loop to go through each department
        foreach ($departments as $department) {
          echo'<h2 id="'. $department[0] .'" style="display: inline; margin-right: 10px;">'. $department[0] .'</h2>
          <button id="'. $department[0] .'Button" class="bottomMarginOnly">Hide Complete Projects</button>';
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
              AND projectTeamTable.projectID = ". $projectID ."
              
              UNION
              
              SELECT employeeFirstName, employeeSurname 
              FROM employeeTable, projectEmployeeTable 
              WHERE employeeTable.employeeID = projectEmployeeTable.employeeID 
              AND projectEmployeeTable.projectID = ". $projectID .";";

              $query_result = mysqli_query($conn, $query);

              $teamMembers = mysqli_fetch_all($query_result);

              if (count($teamMembers) == 1) {
                $projectTeam = $projectTeam ." (Individual) ". $teamMembers[0][0] .' '. $teamMembers[0][1];
              } else {
                foreach ($teamMembers as $teamMember) {
                  if ($projectTeam == "Project Team: ") {
                    $projectTeam = $projectTeam . $teamMember[0] .' '. $teamMember[1];
                  } else {
                    $projectTeam = $projectTeam .", " . $teamMember[0] .' '. $teamMember[1];
                  }
                }
              }

              echo '<div class="projectContainer" id="'. $projectHTMLName .'">
                <div>
                  <h3 class="bottomMarginOnly">'. $projectTitle .'</h3>
                  '. $projectDesc .'<br>
                  <h5 style="margin-top: 10px;
                margin-bottom: 0px;">'. $projectTeam .'<br>Due: '. $dueDate .'</h5>

                </div>
                <div class="progress-bar-container">
                  <div class="progress-bar ' . $projectHTMLName . '">
                  </div>
                </div>
              </div>';

              $totalSubActivityWeight = 0;
              $totalCompletedSubActivityWeight = 0;

              $query = "SELECT subActivityID, subActivityTitle, description, deadline, completeness, employeeID, weight FROM subActivityTable WHERE projectID = ". $projectID .";";
              $query_result = mysqli_query($conn, $query);

              $subActivities = mysqli_fetch_all($query_result);

              echo '<div class="pop-up" id="Pop-up'. $projectID .'">
              <div class="myForm" id="myForm'. $projectID .'">
                <form action="">
                  <h1 class="bottomMarginOnly">'. $projectTitle .'</h1>
                  <h3 style="display: inline; margin-right: 10px;">Activities</h3>
                  <button id="toggleVisibility'. $projectID .'"type="button" class="bottomMarginOnly" onclick="toggleVisibility'. $projectID .'OtherEmp()">View My Activities</button>';

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
                WHERE employeeTable.employeeID = subActivityTable.employeeID AND subActivityTable.subActivityID = ". $subActivityID .";";
                $query_result = mysqli_query($conn, $query);

                $subActivityDoerArray = mysqli_fetch_all($query_result);
                $subActivityDoer = $subActivityDoerArray[0][0] ." ". $subActivityDoerArray[0][1];

                if ($currentEmployeeID != $subActivityEmployeeID) {
                  echo '<div class="subAcitity'. $projectID .'OtherEmp">';
                  if ($subActivityCompleteness == 1) {
                    $completenessHTML = '<p id="subActivityCompleteness'. $subActivityID .'" class="longBottomMarginOnly" style="color: green;">Complete</p>';
                  } else {
                    $completenessHTML = '<p id="subActivityCompleteness'. $subActivityID .'" class="longBottomMarginOnly" style="color: red;">Incomplete</p>';
                  }
                } else {
                  echo '<div class="subAcitity'. $projectID .'ThisEmp">';
                  if ($subActivityCompleteness == 1) {
                    $completenessHTML = '<p id="subActivityCompleteness'. $subActivityID .'" style="color: green; display: inline; margin-right: 10px;">Complete</p>
                    <button id="subActivity'. $subActivityID .'Button" class="longBottomMarginOnly" type="button">Mark as Complete</button>';
                  } else {
                    $completenessHTML = '<p id="subActivityCompleteness'. $subActivityID .'" style="color: red; display: inline; margin-right: 10px;">Incomplete</p>
                    <button id="subActivity'. $subActivityID .'Button" class="longBottomMarginOnly" type="button">Mark as Complete</button>';
                  }
                }
                
                echo '<h4 class="noMargin">'. $subActivityTitle .'</h4>
                <br class="doubleBreak">
                <p class="noMargin">'. $subActivityDescription .'</p>
                <br class="doubleBreak">
                <p class="noMargin">Activity assigned to: '. $subActivityDoer .'.</p>
                <p class="smallBottomMarginSmallText">Due: '. $subActivityDueDate .'</p>
                '. $completenessHTML .'
                </div>';

                if ($currentEmployeeID == $subActivityEmployeeID) {
                  echo '<script>
                    document.getElementById("subActivity'. $subActivityID .'Button").addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent default form submission behavior
                
                    var content = document.getElementById("subActivityCompleteness'. $subActivityID .'").textContent;
                
                    var newCompleteness = "0"
                    if (content == "Incomplete") {
                      newCompleteness = "1"
                      document.getElementById("subActivityCompleteness'. $subActivityID .'").textContent = "Complete"
                      document.getElementById("subActivityCompleteness'. $subActivityID .'").style = "color: green; display: inline; margin-right: 10px;"
                      document.getElementById("subActivity'. $subActivityID .'Button").textContent = "Mark as Incomplete"
                    } else {
                      document.getElementById("subActivityCompleteness'. $subActivityID .'").textContent = "Incomplete"
                      document.getElementById("subActivityCompleteness'. $subActivityID .'").style = "color: red; display: inline; margin-right: 10px;"
                      document.getElementById("subActivity'. $subActivityID .'Button").textContent = "Mark as Complete"
                    }
                
                    // Send an AJAX request to the hello.php file with a parameter
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "completenessUpdater.php?newCompleteness=" + newCompleteness + "&subActivityID='. $subActivityID .'", true);
                    xhr.onreadystatechange = function() {};
                    xhr.send();
                    });
                  </script>';
                }
              }

              echo '<h5 class="topMarginOnly">'. $projectTeam .'<br>Due: '. $dueDate .'</h5><br>
              <button type="submit" onclick="closeForm'. $projectID .'()">Close</button>
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
                  --progress-value: '. $overallCompletenessPercent .';
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
                function openForm'. $projectID .'() {
                  document.getElementById("myForm'. $projectID .'").style.display = "block";
                  document.getElementById("Pop-up'. $projectID .'").style.display = "block";
                  // The above line makes sure that the form itself is not darkened, only the background
              
                  let backgroundOverlay = document.getElementById("overlay");
                  backgroundOverlay.style.display = "block";
                  setTimeout(() => {
                    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
                  }, 10);
                  // Above line makes the transition between dimming and brightened less jarring
                }

                function closeForm'. $projectID .'() {
                  document.getElementById("myForm'. $projectID .'").style.display = "none";
                  document.getElementById("pop-up'. $projectID .'").style.display = "none";
              
                  let backgroundOverlay = document.getElementById("overlay");
                  backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
                  setTimeout(() => {
                    backgroundOverlay.style.display = "none";
                  }, 300);
                }

                var project'. $projectID .'Div = document.getElementById("project'. $projectID .'");

                project'. $projectID .'Div.addEventListener("click", openForm'. $projectID .');
              </script>';

              echo '<script>
                function toggleVisibility'. $projectID .'OtherEmp() {
                  var elements = document.getElementsByClassName("subAcitity'. $projectID .'OtherEmp");
                  var visable = 0;
                  for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    if (element.style.display === "none") {
                      element.style.display = "block";
                      visable = 1
                    } else {
                      element.style.display = "none";
                    }
                  }
                  if (visable == 0) {
                    document.getElementById("toggleVisibility'. $projectID .'").textContent = "View All Activities"
                  } else {
                    document.getElementById("toggleVisibility'. $projectID .'").textContent = "View My Activities"
                  }
                }
            </script>';
            }
          }
          if ($hide == true) {
            echo '<script>
              var departmentHeader = document.getElementById("'. $department[0] .'");
              departmentHeader.style.display = "none";
              var departmentButton = document.getElementById("'. $department[0] .'Button");
              departmentButton.style.display = "none";
            </script>';
          }

          echo '<script>
            document.getElementById("'. $department[0] .'Button").addEventListener("click", function(event) {
            event.preventDefault(); // Prevent default form submission behavior
        
            var content = document.getElementById("'. $department[0] .'Button").textContent;
        
            var newCompleteness = "0"
            if (content == "Hide Complete Projects") {
              document.getElementById("'. $department[0] .'Button").textContent = "Show Complete Projects"
            } else {
              document.getElementById("'. $department[0] .'Button").textContent = "Hide Complete Projects"
            }
            });
          </script>';
        }
        
      ?>
    </div>

    
  </div>
</body>

<script>

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

  // Add employee to project
  function addEmployee() {
    var employeeIDInput = document.getElementById("employeeID");
    var employeeID = employeeIDInput.value;

    var employeeList = document.getElementById("employeeList");
    var listItem = document.createElement("li");
    listItem.textContent = "Employee ID: " + employeeID;
    employeeList.appendChild(listItem);

    employeeIDInput.value = ""; // Clear the input field after adding an employee
  }

  // TinyMCE content box
  tinymce.init({
    selector: '#projectDescription',
    plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
    toolbar: 'undo redo | bold italic underline strikethrough | link image media mergetags | tinycomments | checklist numlist bullist | emoticons | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
    menubar: false
  });

  // Content Form
  $('#projectForm').on('submit', function (e) {
    // Update the textarea content with TinyMCE content
    tinyMCE.triggerSave();
    e.preventDefault(); // Prevent the actual form submission
    return false;

    // Gather your form data
    let formData = $(this).serialize();

    // Send it using AJAX
    $.post('', formData, function (response) {
      // handle server response
    });

    // Form submits with latest content from TinyMCE.
  });

  // Schedule Post calendar
  $(document).ready(function () {
    $("#datepicker").datepicker({
      changeMonth: true,
      changeYear: true
    });
  });

</script>

</html>
