<?php 
    $toDoID = $_GET['parameter'];
    $servername = "localhost";
    $username = "team018";
    $password = "nkAfiVuTsC4Yw9LvLEgP";
    $dbname = "team018";

    $query_str1 = "DELETE FROM `subtask`
    WHERE toDoID = $toDoID;";
    
    $query_str2 ="DELETE FROM `toDoList` WHERE toDoID = $toDoID;";
    

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn){
    die("Connection failed:" . mysqli_connect_error());
    }

    mysqli_query($conn, $query_str1);
    mysqli_query($conn, $query_str2);

    echo "Record deleted successfully";
    mysqli_close($conn);

    

?>