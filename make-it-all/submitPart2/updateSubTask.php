<?php 
    $subtaskID = $_GET['parameter'];
    $check = $_GET['check'];
    echo "$check"; //
    $servername = "localhost";
    $username = "team018";
    $password = "nkAfiVuTsC4Yw9LvLEgP";
    $dbname = "team018";

    if ($check=="1"){
        $query_str = "UPDATE `subtask`
        SET status = 1
        WHERE subTaskID = $subtaskID"; 
        echo "changed-1";   
    } elseif ($check=="0") {
        $query_str = "UPDATE `subtask`
        SET status = 0
        WHERE subTaskID = $subtaskID";
        echo "changed-0";
    }
    

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn){
    die("Connection failed:" . mysqli_connect_error());
    }

    mysqli_query($conn, $query_str);
    

    mysqli_close($conn);

    

?>