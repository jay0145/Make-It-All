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

$subActivityID = $_POST['ActivityID'];

$query = "DELETE FROM subActivityTable WHERE subActivityID = '$subActivityID'"; 

if(mysqli_query($conn, $query)){
    echo "Row inserted successfully.";
}
else{
    echo "Error: " . $query1 . "<br>" . mysqli_error($conn);
}

//  Redirect to managerProjectView.php
header("Location: managerProjectView.php");
exit(); //ensure no other code is executed

mysqli_close($conn);

?>