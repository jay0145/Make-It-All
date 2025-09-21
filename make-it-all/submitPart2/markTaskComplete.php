<?php 
    $toDoID = $_GET['parameter'];
    $complete = $_GET['complete'];
    $servername = "localhost";
    $username = "team018";
    $password = "nkAfiVuTsC4Yw9LvLEgP";
    $dbname = "team018";

    if ($complete=="complete"){
        $query_str = "UPDATE `toDoList`
        SET status = 0
        WHERE toDoID = $toDoID"; 
        echo "changed-1"; //check if status changed to 1 - viewable in console
    } elseif ($complete=="incomplete") {
        $query_str = "UPDATE `toDoList`
        SET status = 1
        WHERE toDoID = $toDoID";
        echo "changed-0"; //check if status changed to 0 - viewable in console
    }
    
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn){
    die("Connection failed:" . mysqli_connect_error());
    }

    if (mysqli_query($conn, $query_str)){
        echo "done!";
    }
    

    mysqli_close($conn);

    

?>