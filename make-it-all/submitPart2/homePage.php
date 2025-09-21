<?php
session_start();

if (isset($_SESSION["username"])) {
	$email = $_SESSION["username"];
} else {
	echo "Session variable not set.";
	exit;
}

$servername = "localhost";
$dbname = "team018";
$username = "team018";
$password = "nkAfiVuTsC4Yw9LvLEgP";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

function shortenText($text)
{
	$sentences = preg_split('/(?<=[.?!])\s+(?=[a-zA-Z])/u', $text, 3, PREG_SPLIT_DELIM_CAPTURE);
	$firstTwoSentences = implode(' ', array_slice($sentences, 0, 2));
	if (count($sentences) > 2) {
		$firstTwoSentences .= "...";
	}
	return $firstTwoSentences;
}

$query = "SELECT employeeID, employeeDepartment, employeeFirstName FROM employeeTable WHERE employeeEmail = ?";
$stmt1 = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt1, "s", $email);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1, $employeeID, $employeeDepartment, $employeeFirstName);
mysqli_stmt_fetch($stmt1);
mysqli_stmt_close($stmt1);

$ifManagerQuery = "SELECT employeeID FROM employeeTable WHERE manager = 1 AND employeeID = $employeeID";
$ifManagerResult = mysqli_query($conn, $ifManagerQuery);
$row = mysqli_fetch_assoc($ifManagerResult);
if ($row['employeeID'] == $employeeID) {
	$ifManager = true;
} else {
	$ifManager = false;
}

$toDoListTasks = "SELECT COUNT(CASE WHEN status = 1 THEN toDoID END) AS completed_tasks, COUNT(toDoID) AS total_tasks FROM toDoList 
WHERE employeeID = $employeeID";
$toDoListTasksResult = mysqli_query($conn, $toDoListTasks);
$noToDoList = false;

if ($toDoListTasksResult) {
	$row = mysqli_fetch_assoc($toDoListTasksResult);
	$completedTasks = $row['completed_tasks'];
	$totalTasks = $row['total_tasks'];
	if ($totalTasks == 0) {
		$noToDoList = true;
	} else {
		$progressPercentage = ($completedTasks / $totalTasks) * 100;
		$progressPercentageFormatted = number_format($progressPercentage, 2);
	}
} else {
	echo "Error executing query: " . mysqli_error($conn);
}

$savedPosts = "SELECT pT.title AS postTitle, pT.date AS postDate, pT.text AS postText FROM savedPostTable sPT 
JOIN postTable pT ON sPT.postID = pT.postID WHERE sPT.employeeID = $employeeID ORDER BY pT.date DESC LIMIT 2";
$savedPostsResult = mysqli_query($conn, $savedPosts);
$noSavedPosts = false;
$oneSavedPost = false;

if ($savedPostsResult) {
	if (mysqli_num_rows($savedPostsResult) == 1) {
		$row = mysqli_fetch_assoc($savedPostsResult);
		$savedPostTitle1 = $row['postTitle'];
		$savedPostDate1 = $row['postDate'];
		$savedPostText1 = $row['postText'];
		$savedPostText1 = shortenText($savedPostText1);

		$oneSavedPost = true;
	} else if (mysqli_num_rows($savedPostsResult) == 2) {
		$row = mysqli_fetch_assoc($savedPostsResult);
		$savedPostTitle1 = $row['postTitle'];
		$savedPostDate1 = $row['postDate'];
		$savedPostText1 = $row['postText'];
		$savedPostText1 = shortenText($savedPostText1);

		$row = mysqli_fetch_assoc($savedPostsResult);
		$savedPostTitle2 = $row['postTitle'];
		$savedPostDate2 = $row['postDate'];
		$savedPostText2 = $row['postText'];
		$savedPostText2 = shortenText($savedPostText2);
	} else {
		$noSavedPosts = true;
	}
} else {
	echo "Error executing query: " . mysqli_error($conn);
}

if ($ifManager) {
	$viewProjects = "SELECT title AS project_title, description AS project_description, deadline AS project_deadline
	FROM projectTable WHERE managerID = $employeeID ORDER BY deadline LIMIT 2";
	$projectsResult = mysqli_query($conn, $viewProjects);
} else if (!$ifManager) {
	$myProjects = "SELECT pT.title AS project_title, pT.description AS project_description, pT.deadline AS project_deadline 
	FROM projectEmployeeTable pET JOIN projectTable pT ON pET.projectID = pT.projectID WHERE pET.employeeID = $employeeID 
	ORDER BY pT.deadline LIMIT 2;";
	$projectsResult = mysqli_query($conn, $myProjects);
}
$noProjects = false;
$oneProject = false;

if ($projectsResult) {
	if (mysqli_num_rows($projectsResult) == 1) {
		$row = mysqli_fetch_assoc($projectsResult);
		$title1 = $row['project_title'];
		$description1 = $row['project_description'];
		$deadline1 = $row['project_deadline'];

		$oneProject = true;
	} else if (mysqli_num_rows($projectsResult) == 2) {
		$row = mysqli_fetch_assoc($projectsResult);
		$title1 = $row['project_title'];
		$description1 = $row['project_description'];
		$deadline1 = $row['project_deadline'];

		$row = mysqli_fetch_assoc($projectsResult);
		$title2 = $row['project_title'];
		$description2 = $row['project_description'];
		$deadline2 = $row['project_deadline'];
	} else {
		$noProjects = true;
	}
} else {
	echo "Error executing query: " . mysqli_error($conn);
}

$draftPosts = "SELECT title AS postTitle, text AS postText FROM postTable WHERE posted = 0 AND employeeID = $employeeID 
ORDER BY date DESC LIMIT 2;";
$draftPostsResult = mysqli_query($conn, $draftPosts);
$noDraftPosts = false;
$oneDraftPost = false;

if ($draftPostsResult) {
	if (mysqli_num_rows($draftPostsResult) == 1) {
		$row = mysqli_fetch_assoc($draftPostsResult);
		$draftPostTitle1 = $row['postTitle'];
		$draftPostText1 = $row['postText'];
		$draftPostText1 = shortenText($draftPostText1);

		$oneDraftPost = true;
	} else if (mysqli_num_rows($draftPostsResult) == 2) {
		$row = mysqli_fetch_assoc($draftPostsResult);
		$draftPostTitle1 = $row['postTitle'];
		$draftPostText1 = $row['postText'];
		$draftPostText1 = shortenText($draftPostText1);

		$row = mysqli_fetch_assoc($draftPostsResult);
		$draftPostTitle2 = $row['postTitle'];
		$draftPostText2 = $row['postText'];
		$draftPostText2 = shortenText($draftPostText2);
	} else {
		$noDraftPosts = true;
	}
} else {
	echo "Error executing query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/twoColumnStyles.css">
	<link rel="stylesheet" href="css/generalStyles.css">
	<link rel="stylesheet" href="css/homePageStyles.css">
	<title>Home Page</title>
	<style>
		@property --progress-value {
			syntax: '<number>';
			inherits: false;
			initial-value: 0;
		}

		.progress-bar {
			width: 100px;
			height: 100px;
			border-radius: 50%;
			display: flex;
			justify-content: center;
			align-items: center;
			background:
				radial-gradient(closest-side, white 79%, transparent 80% 100%),
				conic-gradient(rgba(217, 154, 1, 255), calc(var(--progress-value) * 1%), rgb(255, 255, 229) 0);
		}

		.bar1 {
			animation: bar1-progress 2s forwards;
		}

		.bar1::before {
			animation: bar1-progress 2s forwards;
		}

		.progress-bar::before {
			counter-reset: percentage var(data-progress);
			content: attr(data-progress) '%';
		}

		@keyframes bar1-progress {
			from {
				--progress-value: 0
			}

			to {
				--progress-value: <?php echo $progressPercentageFormatted; ?>;
			}
		}
	</style>
</head>

<body>
	<div class="icon-container" style="padding: 1px; align-items: center; display: flex; justify-content: space-between">
		<div class="employee-info-container" style="margin-left: 13px; font-size: 15px; color: white; text-align: left; font-weight: bold;">
			<?php
			echo "<p>Employee ID No: $employeeID<br>Department: $employeeDepartment</p>";
			?>
		</div>
		<a class="btn btn-dark" style="margin-right: 16px; font-size: 18px; color: black; text-decoration: none; padding: 5px 10px; 
	background-color: rgba(217,154,1,255); border-radius: 5px; transition: background-color 0.3s; font-weight: bold;" href="Login_Page.html">Log Out</a>
	</div>
	<div class="header homepage-header-top">
		<div class="left-content">
			<img src="Images/homePageIconImage.png" alt="home image">
		</div>
		<h2>Home</h2>
		<div class="center-content">
			<img src="Images/makeItAllLogoImage.png" alt="make it all logo image">
		</div>
		<div class="left-content" style="opacity: 0;">
			<img src="Images/homePageIconImage.png" alt="home image">
		</div>
		<h2 style="opacity: 0;">Home</h2>
	</div>
	<div class="header homepage-header-bottom">
		<div class="nav-bar" style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 0; width: 99%;">
			<a style="border-radius: 0" href="homePage.php">Home Page</a>
			<a style="border-radius: 0" href="ToDoList.php">My To-Do List</a>
			<?php
			if ($ifManager) {
				echo '<a style="border-radius: 0" href="managerProjectView.php">View Projects</a>';
			} else if (!$ifManager) {
				echo '<a style="border-radius: 0" href="employeeProjectView.php">My Projects</a>';
			}
			?>
			<a style="border-radius: 0" href="CommunityPostsMain.php">Community Posts</a>
		</div>
	</div>

	<div class="container">
		<div class="left-sidebar">
			<div class="employee-container">
				<div class="welcome-container">
					<?php
					echo "<h2>Hello, $employeeFirstName<br>Welcome to the System</h2>";
					?>
				</div>
				<div class="employee-info-container">
					<?php
					echo "<h3 style='color: black;'>Employee ID No: $employeeID<br>Department: $employeeDepartment</h3>";
					?>
				</div>
			</div>
			<?php
			if (!$noToDoList) {
				echo '<div class="to-do-list-container">';
				echo '<h3><a href="ToDoList.php" style="text-decoration: none; color: black;">To-Do List</a></h3>';
				echo '<hr>';
				echo '<div class="progress-container">';
				echo '<p><Strong> ' . $completedTasks . ' out of ' . $totalTasks . ' Tasks Complete</Strong></p>';
				echo '<div class="progress-bar-container">';
				echo '<div class="progress-bar bar1" data-progress=" ' . $progressPercentageFormatted . ' "></div>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
			} else {
				echo "<p>No To-Do List found.</p>";
			}
			?>
		</div>
		<div class="right-sidebar">
			<h2><a href="savedPosts.php" style="text-decoration: none; color: black;">Saved Posts</a></h2>
			<?php
			if (!$noSavedPosts && !$oneSavedPost) {
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block;"><h3> ' . $savedPostTitle1 . ' </h3></div>';
				echo '<div style="display: inline-block; float: right;"><h4> ' . $savedPostDate1 . ' </h4></div>';
				echo '<p> ' . $savedPostText1 . ' </p>';
				echo '</div>';
				echo '<br>';
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block;"><h3> ' . $savedPostTitle2 . ' </h3></div>';
				echo '<div style="display: inline-block; float: right;"><h4> ' . $savedPostDate2 . ' </h4></div>';
				echo '<p> ' . $savedPostText2 . ' </p>';
				echo '</div>';
			} else if ($oneSavedPost) {
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block;"><h3> ' . $savedPostTitle1 . ' </h3></div>';
				echo '<div style="display: inline-block; float: right;"><h4> ' . $savedPostDate1 . ' </h4></div>';
				echo '<p> ' . $savedPostText1 . ' </p>';
				echo '</div>';
				echo '<p>No more saved posts found.</p>';
			} else {
				echo '<p>No saved posts found.</p>';
			}
			?>
		</div>
		<div class="left-sidebar">
			<?php
			if ($ifManager) {
				echo '<h2><a href="managerProjectView.php" style="text-decoration: none; color: black;">View Projects</a></h2>';
			} else if (!$ifManager) {
				echo '<h2><a href="employeeProjectView.php" style="text-decoration: none; color: black;">My Projects</a></h2>';
			}
			if (!$noProjects && !$oneProject) {
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block; margin-left: 15px;"><h3> ' . $title1 . '</h3></div>';
				echo '<div style="display: inline-block; float: right; margin-right: 15px;"><h4>Deadline: ' . $deadline1 . ' </h4></div>';
				echo '<p style="margin-left: 15px;"> ' . $description1 . '</p>';
				echo '</div>';
				echo '<br>';
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block; margin-left: 15px;"><h3> ' . $title2 . '</h3></div>';
				echo '<div style="display: inline-block; float: right; margin-right: 15px;"><h4>Deadline: ' . $deadline2 . ' </h4></div>';
				echo '<p style="margin-left: 15px;"> ' . $description2 . '</p>';
				echo '</div>';
			} else if ($oneProject) {
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block; margin-left: 15px;"><h3> ' . $title1 . '</h3></div>';
				echo '<div style="display: inline-block; float: right; margin-right: 15px;"><h4>Deadline: ' . $deadline1 . ' </h4></div>';
				echo '<p style="margin-left: 15px;"> ' . $description1 . '</p>';
				echo '</div>';
				echo '<p>No more Projects found.</p>';
			} else {
				echo '<p>No Projects found.</p>';
			}
			?>
		</div>
		<div class="right-sidebar">
			<h2><a href="CommunityPostsMain.php" style="text-decoration: none; color: black;">Draft Posts</a></h2>
			<?php
			if (!$noDraftPosts && !$oneDraftPost) {
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block;"><h3>Draft Title: ' . $draftPostTitle1 . ' </h3></div>';
				echo '<p>Draft Text: ' . $draftPostText1 . ' </p>';
				echo '</div>';
				echo '<br>';
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block;"><h3>Draft Title: ' . $draftPostTitle2 . ' </h3></div>';
				echo '<p>Draft Text: ' . $draftPostText2 . ' </p>';
				echo '</div>';
			} else if ($oneDraftPost) {
				echo '<div class="inside-container" style="display: block">';
				echo '<div style="display: inline-block;"><h3>Draft Title: ' . $draftPostTitle1 . ' </h3></div>';
				echo '<p>Draft Text: ' . $draftPostText2 . ' </p>';
				echo '</div>';
				echo '<p>No more draft posts found.</p>';
			} else {
				echo '<p>No draft posts found.</p>';
			}
			?>
		</div>
	</div>
</body>

</html>