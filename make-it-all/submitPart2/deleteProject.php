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

$projectID = $_POST['deleteProjectID'];

$query1 = "DELETE FROM projectTable WHERE projectID = '$projectID'";

if(mysqli_query($conn, $query1)){
    echo "Project deleted successfully.";
}
else{
    echo "Error: " . $query1 . "<br>" . mysqli_error($conn);
}

$query2 = "DELETE FROM projectEmployeeTable WHERE projectID = '$projectID'";

if(mysqli_query($conn, $query2)){
    echo "Project employees deleted successfully.";
}
else{
    echo "Error: " . $query2 . "<br>" . mysqli_error($conn);
}

$query3 = "DELETE FROM projectTeamTable WHERE projectID = '$projectID'";

if(mysqli_query($conn, $query3)){
    echo "Project teams deleted successfully.";
}
else{
    echo "Error: " . $query3 . "<br>" . mysqli_error($conn);
}

header("Location: managerProjectView.php");
exit(); //ensure no other code is executed

mysqli_close($conn);

ob_end_flush(); // output buffering

?>