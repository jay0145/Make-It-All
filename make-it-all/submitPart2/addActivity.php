<?php

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

$activityTitle = $_POST['activityTitle'];
$activityDescription = $_POST['activityDescription'];
$activityDeadline = $_POST['activityDeadline'];
$activityProject = $_POST['projectListID'];
$subActivityWeight = $_POST['subActivityWeight'];

$employeeIDs = $_POST['activityEmployee'];

foreach ($employeeIDs as $employeeID) {
    $query1 = "INSERT INTO subActivityTable (subActivityTitle, description, deadline, completeness, employeeID, projectID, weight) 
    VALUES ('$activityTitle','$activityDescription' ,'$activityDeadline','0','$employeeID', '$activityProject', '$subActivityWeight')";

    if(mysqli_query($conn, $query1)){
        echo "Row inserted successfully for employee ID: $employeeID.<br>";
    }
    else{
        echo "Error: " . $query1 . "<br>" . mysqli_error($conn);
    }
}

//  Redirect to managerProjectView.php
header("Location: managerProjectView.php");
exit(); //ensure no other code is executed

mysqli_close($conn);

?>