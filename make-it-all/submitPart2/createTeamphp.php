<?php 

print_r($_POST);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

//  Input team name into teamTable
$teamName=$_POST['teamName'];
$teamMembers = $_POST['teamList'];

$query1 = "INSERT INTO teamTable (teamID, teamName) VALUES ('', '$teamName')";

if(mysqli_query($conn, $query1)){
    echo "Row inserted successfully.";
}
else{
    echo "Error: " . $query1 . "<br>" . mysqli_error($conn);
}

//  Get teamID from teamTable
$query2 = "SELECT teamID FROM teamTable WHERE teamName = '$teamName'";
$result = mysqli_query($conn, $query2);
$row = mysqli_fetch_assoc($result);
$teamID = $row['teamID'];

//  Input team members into teamEmployeeTable
foreach ($teamMembers as $employeeID) {
    $query3 = "INSERT INTO teamEmployeeTable (teamID, employeeID) VALUES ('$teamID', '$employeeID')";
    if(mysqli_query($conn, $query3)){
        echo "Row inserted successfully.";
    }
    else{
        echo "Error: " . $query3 . "<br>" . mysqli_error($conn);
    }
}

//  Redirect to managerProjectView.php
header("Location: managerProjectView.php");
exit(); //ensure no other code is executed

mysqli_close($conn);

?>