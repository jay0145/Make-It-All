<?php 

session_start();

// Connect to MySQL database
$servername = "sci-project.lboro.ac.uk";
$dbusername = "team018";
$dbpassword = "nkAfiVuTsC4Yw9LvLEgP";
$dbname = "team018";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    die("". mysqli_connect_error());
}

$query = "SELECT employeeID, employeeFirstName, employeeSurname
FROM employeeTable
WHERE manager = 0
ORDER BY employeeID ASC";

$result = mysqli_query($conn, $query);

$data = array(); // Create an empty array to hold your data

while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row; // Add each row to the array
}

$json = json_encode($data); // Convert the array to a JSON string

echo $json; // Output the JSON string



mysqli_close($conn);


?>