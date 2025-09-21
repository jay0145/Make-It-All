<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); // output buffering

session_start();

// Connect to MySQL database
$servername = "sci-project.lboro.ac.uk";
$dbusername = "team018";
$dbpassword = "nkAfiVuTsC4Yw9LvLEgP";
$dbname = "team018";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    die("". mysqli_connect_error());
}

// Store the session employee ID in a variable
$query0 = "SELECT employeeID FROM employeeTable WHERE employeeEmail = '{$_SESSION["username"]}'";

$result = mysqli_query($conn, $query0);
$row = mysqli_fetch_assoc($result);
$_SESSION["employeeID"] = $row['employeeID'];

$employeeDepartment=$_POST['employeeDepartment'];
$projectName=$_POST['projectName'];
$projectDescription=$_POST['projectDescription'];
$projectDeadline=$_POST['projectDeadline'];
$employeeIDs = $_POST['employeeList'];
$teamIDs = $_POST['teamList'];

$query1 = "
INSERT INTO projectTable (title, description, deadline, employeeDepartment, managerID) 
VALUES ('$projectName', '$projectDescription', '$projectDeadline', '$employeeDepartment', '{$_SESSION["employeeID"]}')
";

if(mysqli_query($conn, $query1)){
    echo "Row inserted successfully.";
}
else{
    echo "Error: " . $query1 . "<br>" . mysqli_error($conn);
}

$query2 = "
SELECT projectID FROM projectTable WHERE title = '$projectName' AND description = '$projectDescription' AND deadline = '$projectDeadline' AND employeeDepartment = '$employeeDepartment' AND managerID = '{$_SESSION["employeeID"]}'
";
$result = mysqli_query($conn, $query2);
$row = mysqli_fetch_assoc($result);
$projectID = $row['projectID'];

foreach ($employeeIDs as $employeeID) {
    $query3 = "
    INSERT INTO projectEmployeeTable (projectID, employeeID)
    VALUES ('$projectID', '$employeeID')
    ";

    // Execute the SQL query
    if (mysqli_query($conn, $query3)) {
        echo "Row inserted successfully.";
    } else {
        echo "Error: " . $query3 . "<br>" . mysqli_error($conn);
    }
}

foreach ($teamIDs as $teamID) {
    $query4 = "
    INSERT INTO projectTeamTable (projectID, teamID)
    VALUES ('$projectID', '$teamID')
    ";

    // Execute the SQL query
    if (mysqli_query($conn, $query4)) {
        echo "Row inserted successfully.";
    } else {
        echo "Error: " . $query4 . "<br>" . mysqli_error($conn);
    }
}

header("Location: managerProjectView.php");
exit(); //ensure no other code is executed

mysqli_close($conn);

ob_end_flush(); // output buffering

?>