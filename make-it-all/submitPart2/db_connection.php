<html>
<?php 

session_start();

$servername = "localhost";
$dbname = "team018";
$username = "team018";
$password = "nkAfiVuTsC4Yw9LvLEgP";

if(isset($_GET['username']) && isset($_GET['password']))
{
$email = $_GET['username'];
$pass = $_GET['password'];  
}

$cPassword = str_split($pass);
$checkPassword;
for($i = 0 ; $i < sizeof($cPassword) ; $i++)
{
   for($j = 0 ; $j < 4 ; $j++)
   {
      $cPassword[$i] = ++$cPassword[$i];
   }
   $checkPassword .= $cPassword[$i];
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
   die("". mysqli_connect_error());
}
echo "Connection Successful";

$userQuery = "select employeeEmail from employeeTable where employeeEmail like '$email'";
$user_result = mysqli_query($conn, $userQuery);

if(mysqli_num_rows($user_result) > 0)
{
   $query = "select * from employeeTable where employeeEmail like '$email' and employeePassword like '$checkPassword'";
   $result = mysqli_query($conn, $query);
   if(mysqli_num_rows($result) == 1)
   {
      $_SESSION["username"] = $email;
      header("Location: homePage.php");
      exit;
   }
   else
      header("Location: Login_Page_Fail.html");
}
else
   header("Location: Login_Page_Fail.html");
?>
