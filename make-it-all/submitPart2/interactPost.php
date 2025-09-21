<?php
$userID = isset($_POST['userID']) ? $_POST['userID'] : 0; //Get the start value
$postID = isset($_POST['postID']) ? $_POST['postID'] : 0; //Get the start value
$table = isset($_POST['table']) ? $_POST['table'] : 0; //Get the start value

//Separate database details, use include to get them
include "config.php";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}
if ($table == "savedPostTable"){
    $sqlCheck = "SELECT * FROM $table WHERE employeeID = $userID AND postID = $postID";
    $resultCheck = mysqli_query($conn,$sqlCheck);
    $sqlAction;
        if (mysqli_num_rows($resultCheck)>0){
            $sqlAction = "DELETE FROM $table WHERE employeeID = $userID AND postID = $postID";
            $resultAction = mysqli_query($conn,$sqlAction);
        }else{
            $sqlAction = "INSERT INTO $table (postID, employeeID) VALUES ($postID, $userID)";
            $resultAction = mysqli_query($conn,$sqlAction);
        }
}else{
    $sqlCheck = "SELECT * FROM $table WHERE userID = $userID AND postID = $postID";
    $resultCheck = mysqli_query($conn,$sqlCheck);
    $sqlAction;
        if (mysqli_num_rows($resultCheck)>0){
            $sqlAction = "DELETE FROM $table WHERE userID = $userID AND postID = $postID";
            $resultAction = mysqli_query($conn,$sqlAction);
        }else{
            $otherTable;
            if($table == "likeTable"){
                $otherTable = "dislikeTable";
            }else{
                $otherTable = "likeTable";
            }
            $sqlCheckOther = "SELECT * FROM $otherTable WHERE userID = $userID AND postID = $postID";
            $resultCheckOther = mysqli_query($conn,$sqlCheckOther);
            if (mysqli_num_rows($resultCheckOther)>0){
                $sqlAction = "INSERT INTO $table (postID, userID) VALUES ($postID, $userID)";
                $resultAction = mysqli_query($conn,$sqlAction);
                $sqlAction = "DELETE FROM $otherTable WHERE userID = $userID AND postID = $postID";
                $resultAction = mysqli_query($conn,$sqlAction);
            }else{
                $sqlAction = "INSERT INTO $table (postID, userID) VALUES ($postID, $userID)";
                $resultAction = mysqli_query($conn,$sqlAction);
            }
            
        }
}


?>