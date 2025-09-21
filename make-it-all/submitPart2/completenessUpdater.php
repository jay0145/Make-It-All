<?php
  // Receive the parameter values from the request
  $newCompleteness = $_GET['newCompleteness'];
  $subActivityID = $_GET['subActivityID'];

  $servername = "localhost";
  $username = "team018";
  $password = "nkAfiVuTsC4Yw9LvLEgP";
  $dbname = "team018";
  $query_str = 'UPDATE subActivityTable
  SET completeness = '. $newCompleteness .'
  WHERE subActivityID = '. $subActivityID .';';

  $conn = mysqli_connect($servername, $username, $password, $dbname);
  mysqli_query($conn, $query_str);

  mysqli_close($conn);
?>