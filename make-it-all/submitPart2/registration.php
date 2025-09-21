<html>
<?php
$servername = "localhost";
$dbname = "team018";
$username = "team018";
$password = "nkAfiVuTsC4Yw9LvLEgP";

$fname = $_GET['firstName'];
$lname = $_GET['lastName'];
$email = $_GET['email'];
$address = $_GET['Address'];
$userPassword = $_GET['password'];
$DOB = $_GET['DOB'];
$dept = $_GET['dept'];

$conn = mysqli_connect($servername, $username, $password, $dbname);

$characterPassword = str_split($userPassword);
$newUserPassword;
for($i = 0 ; $i < sizeof($characterPassword) ; $i++)
{
    echo "$characterPassword[$i]";
    for($j = 0 ; $j < 4 ; $j++)
    $characterPassword[$i] = ++$characterPassword[$i];
    $newUserPassword .= $characterPassword[$i];
}
echo "<br>";
echo "$newUserPassword";


if (mysqli_connect_errno()) {
   die("". mysqli_connect_error());
}


$query = "INSERT INTO employeeTable (employeeEmail, employeePassword, employeeFirstName, employeeSurname, employeeDOB, employeeAddress, employeeDepartment)
VALUES ('$email', '$newUserPassword','$fname', '$lname', '$DOB', '$address', '$dept')";

if(mysqli_query($conn, $query)){
    echo "Registration Successful";
    header("Location: http://team018.sci-project.lboro.ac.uk/finalCWForGCP/Login_Page.html");
    exit;
}
else{
    echo "Registration Failed";
    exit;
}

?>
</html>