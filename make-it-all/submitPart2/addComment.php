<?php
$userID = isset($_POST['userID']) ? $_POST['userID'] : 0; //Get the start value
$postID = isset($_POST['postID']) ? $_POST['postID'] : 0; //Get the start value
$text = isset($_POST['text']) ? $_POST['text'] : 0; //Get the start value

//Separate database details, use include to get them
include "config.php";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}

// Prepare and bind the SQL statement
$sqlAddComment = "INSERT INTO commentTable (authorID, postID, text) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sqlAddComment);

// Bind parameters
mysqli_stmt_bind_param($stmt, "iis", $userID, $postID, $text);

// Execute the statement
mysqli_stmt_execute($stmt);

// Check if the execution was successful
if (mysqli_stmt_affected_rows($stmt) > 0) {
    // Comment added successfully
} else {
    // Error occurred
}

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>