<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$servername = "localhost";
$dbname = "team018";
$username = "team018";
$password = "nkAfiVuTsC4Yw9LvLEgP";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    die ("" . mysqli_connect_error());
}

//get all task details
$heading = $_GET['heading'];
$desc = $_GET['description'];
$priority = $_GET['priority'];
$deadlineDate = $_GET['deadlineDate'];
$deadlineTime = $_GET['deadlineTime'];
$noOfSubtasks = $_GET['no-of-subtasks'];

//gets employeeID from the logged in user's session
$query0 = "SELECT employeeID FROM employeeTable WHERE employeeEmail = '{$_SESSION["username"]}'";
$employeeResult = mysqli_query($conn, $query0);
$row = mysqli_fetch_assoc($employeeResult);
$_SESSION['employeeID'] = $row['employeeID'];
$employeeID = $_SESSION['employeeID'];

echo "$employeeID";

//if subtasks were added
if (isset ($_GET['subtasks'])) {
    $subtasks = $_GET['subtasks'];

    //add task to toDoList table in database
    $query = "INSERT INTO toDoList (employeeID, heading, description, priority, deadlineDate, deadlineTime, status) VALUES ('$employeeID','$heading', '$desc', '$priority', '$deadlineDate', '$deadlineTime', '0')";


    if (mysqli_query($conn, $query)) {
        echo "Task Added!";
    } else {
        echo "Error!";
    }

    $toDoId;

    //get the toDoID of the task that was just added - auto-incrementer in table
    $query2 = "SELECT toDoID FROM toDoList WHERE heading LIKE '$heading' AND description LIKE '$desc'";
    $result = mysqli_query($conn, $query2);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        echo "<br>";
        echo "$row[0]";
        $toDoId = $row[0];
    }

    //add subtasks to subtask table in database
    foreach ($subtasks as $row) {
        $insert_query = "INSERT INTO subtask (toDoID, description, status) VALUES ('$toDoId', '$row', '0')";
        if (mysqli_query($conn, $insert_query)) {
            echo "<br>";
            echo "Subtask Added!";
        } else {
            echo "Error!";
        }

    }
    echo "<script>alert('Task Added!')</script>";
    header("Location: ToDoList.php"); //Change when pushed to GCP

//if no subtasks inputted
} else {

    $query = "INSERT INTO toDoList (employeeID, heading, description, priority, deadlineDate, deadlineTime, status) VALUES ('$employeeID','$heading', '$desc', '$priority', '$deadlineDate', '$deadlineTime', '0')";


    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Task Added!')</script>";
        header("Location: ToDoList.php"); //Change when pushed to GCP


    } else {
        echo "Error!";
    }
}


?>