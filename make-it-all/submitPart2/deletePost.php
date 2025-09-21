<?php
$postID = isset($_POST['postID']) ? $_POST['postID'] : 0; //Get the start value

//Separate database details, use include to get them
include "config.php";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}

// Delete from postTable
$sqlDeletePost = "DELETE FROM postTable WHERE postID = $postID";
if(mysqli_query($conn, $sqlDeletePost)) {
    // Delete from commentTable
    $sqlDeleteComment = "DELETE FROM commentTable WHERE postID = $postID";
    if(mysqli_query($conn, $sqlDeleteComment)) {
        // Delete from subTag
        $sqlDeleteSubTag = "DELETE FROM subTagTable WHERE postID = $postID";
        if(mysqli_query($conn, $sqlDeleteSubTag)) {
            $sqlDeleteLikes = "DELETE FROM likeTable WHERE postID = $postID";
            if(mysqli_query($conn, $sqlDeleteLikes)) {
                $sqlDeleteDislikes = "DELETE FROM dislikeTable WHERE postID = $postID";
                if(mysqli_query($conn, $sqlDeleteDislikes)) { //Hasnt deleted dislikes!!
                    echo "Post successfully deleted.";
                }
            }
            
        } else {
            echo "Error deleting records: " . mysqli_error($conn);
        }
    } else {
        echo "Error deleting records: " . mysqli_error($conn);
    }
} else {
    echo "Error deleting records: " . mysqli_error($conn);
}

?>