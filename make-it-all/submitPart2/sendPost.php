<?php

$edit = isset($_POST['edit']) ? $_POST['edit'] : 0; //Get the start value
$userID = isset($_POST['userID']) ? $_POST['userID'] : 0; //Get the start value
$postID = isset($_POST['postID']) ? $_POST['postID'] : 0; //Get the start value
$title = isset($_POST['title']) ? $_POST['title'] : 0; //Get the start value
$text = isset($_POST['text']) ? $_POST['text'] : 0; //Get the start value
$date = isset($_POST['date']) ? $_POST['date'] : 0; //Get the start value
$mainTag = isset($_POST['mainTag']) ? $_POST['mainTag'] : 0; //Get the start value
$subTag1 = isset($_POST['subTag1']) ? $_POST['subTag1'] : 0; //Get the start value
$subTag2 = isset($_POST['subTag2']) ? $_POST['subTag2'] : 0; //Get the start value
$draft = isset($_POST['draft']) ? $_POST['draft'] : 0; //Get the start value

//Separate database details, use include to get them
include "config.php";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($edit == "true"){
    // Prepare the SQL statement with placeholders
    $editPost = "UPDATE postTable SET title = ?, mainTag = ?, text = ?, posted = ? WHERE postID = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $editPost);

    // Bind the parameters to the placeholders
    mysqli_stmt_bind_param($stmt, "sssii", $title, $mainTag, $text, $draft, $postID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $sqlDeleteSubTag = "DELETE FROM subTagTable WHERE postID = $postID";
    mysqli_query($conn, $sqlDeleteSubTag);
    if($subTag1){
        $sqlPostSubTag1 = "INSERT INTO subTagTable (subTag, postID) VALUES ('$subTag1', $postID)";
        $resultPostSubTag1 = mysqli_query($conn,$sqlPostSubTag1);
    }
    if($subTag2){
        $sqlPostSubTag2 = "INSERT INTO subTagTable (subTag, postID) VALUES ('$subTag2', $postID)";
        $resultPostSubTag2 = mysqli_query($conn,$sqlPostSubTag2);
    }
}else{
    $sqlGetID = "SELECT MIN(t1.postID) + 1 AS nextID
                    FROM postTable t1
                    LEFT JOIN postTable t2 ON t1.postID + 1 = t2.postID
                    WHERE t2.postID IS NULL;";
    $resultGetID = mysqli_query($conn,$sqlGetID);
    if ($resultGetID) {
        $row = mysqli_fetch_assoc($resultGetID);
        $nextID = $row['nextID'];
    } else {
        // Handle query error
    }
    
    // Convert $title to a string
    $title = strval($title);

    // Convert $userID to an integer
    $userID = intval($userID);

    // Convert $date to a valid date string
    $date = date("Y-m-d", strtotime($date));

    // Convert $nextID to an integer
    $nextID = intval($nextID);

    // Convert $mainTag to a string
    $mainTag = strval($mainTag);

    // Convert $text to a string
    $text = strval($text);

    

    $sqlPost = "INSERT INTO postTable (title, employeeID, date, postID, mainTag, text, posted) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $sqlPost);

    if ($stmt) {
        // Bind the parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "sisissi", $title, $userID, $date, $nextID, $mainTag, $text, $draft);

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Post inserted successfully
        } else {
            // Error executing the statement
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Error preparing the statement
    }
    
    if($subTag1){
        $sqlPostSubTag1 = "INSERT INTO subTagTable (subTag, postID) VALUES ('$subTag1', $nextID)";
        $resultPostSubTag1 = mysqli_query($conn,$sqlPostSubTag1);
    }
    if($subTag2){
        $sqlPostSubTag2 = "INSERT INTO subTagTable (subTag, postID) VALUES ('$subTag2', $nextID)";
        $resultPostSubTag2 = mysqli_query($conn,$sqlPostSubTag2);
    }
    
    
}

// Close the connection
mysqli_close($conn);
?>