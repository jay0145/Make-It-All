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

    // Escape the text to prevent SQL injection and handle special characters
    $escapedText = mysqli_real_escape_string($conn, $text);

    // Prepare the SQL statement with placeholders
    $sqlDeleteComment = "DELETE FROM commentTable WHERE postID = ? AND authorID = ? AND text LIKE ?";

    // Append wildcards to the escaped text variable
    $textWithWildcards = '%' . $escapedText . '%';

    // Prepare the statement
    $stmtDeleteComment = mysqli_prepare($conn, $sqlDeleteComment);

    // Bind the parameters to the prepared statement
    mysqli_stmt_bind_param($stmtDeleteComment, "iis", $postID, $userID, $textWithWildcards);

    // Execute the statement
    mysqli_stmt_execute($stmtDeleteComment);

?>