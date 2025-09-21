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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/threeColumnStyles.css">
  <link rel="stylesheet" href="css/generalStyles.css">
  <link rel="stylesheet" href="css/communityposts.css">
  <link rel="stylesheet" href="css/pop-upstyles.css">
  <link rel="stylesheet" href="css/createPostsCSS.css">
  <link rel="stylesheet" href="css/homePageStyles.css">
  <title>Community posts</title>

  <!-- jQuery and jQuery UI script code-->
  <script src=https://code.jquery.com/jquery-3.7.1.min.js></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
  <!-- TinyMCE script code -->
  <script src="https://cdn.tiny.cloud/1/2b6gf3ueg306zz04ur6kgakjjk3rbtso5om8sj1scygjcvqv/tinymce/6/tinymce.min.js"
    referrerpolicy="origin"></script>
  <!-- CSS for Select2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
  <!-- JS for Select2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>


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
      <img src="Images/communitypostsIconImage.png" alt="community post icon image">
    </div>
    <h2>Community Posts</h2>
    <div class="center-content" >
      <img src="Images/makeItAllLogoImage.png" alt="make it all logo image">
    </div>
	  <div class="left-content" style="opacity: 0;">
      <img src="Images/communitypostsIconImage.png" alt="community post icon image">
    </div>
	  <h2 style="opacity: 0;">Community Posts</h2>
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

  <div class="overlay" id="overlay"></div>
  <div class="container" id="container">

    <div class="left-sidebar" id="postsLeftSidebar">
      <!-- add to checkboxes java coe that de/Selecting non/Technical de/selects all sub filters, and selecting a sub then selects the master one-->
      <h2>Sort By: </h2>
      <label for="newest">
        <input type="radio" id="newest" name="sort" value="newest" checked>
        Newest
      </label><br>
      <label for="popular">
        <input type="radio" id="popular" name="sort" value="popular">
        Most Popular
      </label><br>
      <h2>Filter By: </h2>
      <form>
        <label for="technicalBox">
          <input type="checkbox" id="technicalBox" name="tech" value="technical" checked>
          Technical
        </label><br>
        <label for="techsub1">
          <input type="checkbox" id="techsub1" name="techsub" class="technical-sub" value="Software" checked>
          Software
        </label><br>
        <label for="techsub2">
          <input type="checkbox" id="techsub2" name="techsub" class="technical-sub" value="Hardware" checked>
          Hardware
        </label><br>

        <label for="non-technicalBox">
          <input type="checkbox" id="non-technicalBox" name="nontech" value="non-technical" checked>
          Non-Technical
        </label><br>
        <label for="nontechsub1">
          <input type="checkbox" id="nontechsub1" class="nontechnical-sub" name="nontechsub" value="Printing" checked>
          Printing
        </label><br>
        <label for="nontechsub2">
          <input type="checkbox" id="nontechsub2" class="nontechnical-sub" name="nontechsub" value="Admin" checked>
          Admin
        </label><br>
      </form>
      <button class="clickable" id="applyButton">Apply</button>
    </div>

    <div class="left-sidebar" id="myPostsLeftSidebar" style="display: none;">

    </div>


    <!--
    <div class="myPosts" id="myPostsID">
      <h2>Your Posts</h2> 
      <div class="pop-up" id="deletePost">
        <h2> Are you sure you want to delete this post?</h2>
        <button id="yesDelete" onclick="deletePostConfirm()">Yes</button>
        <button id="noDelete" onclick="deletePostCancel()">No</button>
      </div>
      <div class="post" id="myPost1">
        <p>30th October 2023</p>
        <h2><u>How to order printing Papers </u><img align="right" src="Profile.jpg"></h2> 
        <p><b>Tags:</b> Non-Technical, Printing</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sit amet ligula varius, iaculis orci ut, viverra velit. Proin arcu metus, gravida ut aliquam sed, fringilla ut augue. Vivamus porttitor mauris et varius facilisis. Etiam condimentum sapien sapien, a gravida urna vehicula nec. Mauris pharetra orci at congue aliquam. Nam semper nisi urna, et dictum enim elementum vel. Maecenas dui sapien, luctus ut sapien ut, porta pretium turpis. Aliquam condimentum risus sed tortor dapibus tristique. Maecenas pharetra purus vel ipsum finibus, finibus ullamcorper magna luctus. Donec aliquet sed lacus vel ultricies. Sed congue lorem sed est sodales, malesuada rutrum velit congue.</p>
        <div id="postDetails" class="postDetails">
          <div><p><b></b> </p> </div>
          <div><p class="likeCount"><img src="edit.png" onclick="editPost()"></p></div>
          <div><p class="likeCount"><img src="delete.jpg" onclick="deletePost()"> </p></div>
        </div>
      </div>
      <p align="center"> Page 1/2</p>
    </div> -->

    <form id="editContentForm" action="" method="post">
      <div id="edit-post-popup">
        <div>
          <span>Edit Post</span>
          <button class="clickable" type="button" onClick="closeEditPost()">X</button>
        </div>
        <label for="title">Title:</label>
        <input type="text" id="titleEdit" name="title">
        <br> <br>
        <!--Content area-->
        <label>
          <textarea class="textField" id="textEdit" name="content"></textarea>
        </label>

        <!-- Tags List -->
        <div id="tags-select-section">
          <div id="mainTagsEdit">
            <p>Main Tag: (Only One)</p>
            <form>
              <label for="technicalBoxEdit">
                <input type="checkbox" id="technicalBoxEdit" name="techEdit" value="technical">
                Technical
              </label>
              <label for="non-technicalBoxEdit">
                <input type="checkbox" id="non-technicalBoxEdit" name="nontechEdit" value="non-technical">
                Non-Technical
              </label>
              <br>
              <div id="techSubTagsEdit" style="display: none;">
                <p>Sub Tag: (At least one)</p>
                <label for="softwareBoxEdit">
                  <input type="checkbox" id="softwareBoxEdit" name="softwareEdit" value="Software">
                  Software
                </label>
                <label for="hardwareBoxEdit">
                  <input type="checkbox" id="hardwareBoxEdit" name="hardwareEdit" value="Hardware">
                  Hardware
                </label>
              </div>
              <div id="non-techSubTagsEdit" style="display: none;">
                <p>Sub Tag: (At least one)</p>
                <label for="printingBoxEdit">
                  <input type="checkbox" id="printingBoxEdit" name="printingEdit" value="Printing">
                  Printing
                </label>
                <label for="adminBoxEdit">
                  <input type="checkbox" id="adminBoxEdit" name="adminEdit" value="Admin">
                  Admin
                </label>
              </div>
            </form>
            <br>
          </div>
        </div>

        <div>
        <button class="clickable" type="button" onClick="sendPost(true, 1)">Post</button>
          <button class="clickable" type="button" onClick="sendPost(true, 0)">Save as Draft</button>
          <button class="clickable" type="button" onClick="closeEditPost()">Cancel</button>
          <p id="editPostErrorText"></p>
        </div>
        <p id="editPostID" style="display: none;"></p>
      </div>
    </form>


    <form id="contentForm" action="" method="post">
      <div id="create-post-popup">
        <br>
        <div>
          <span>Create Post</span>
          <button class="clickable" type="button" onClick="closeCreatePost()">X</button>
        </div>
        <label for="title">Title:</label>
        <input type="text" id="titleCreate" name="title">
        <br><br>
        <!--Content area-->
        <label>
          <textarea class="textField" id="textCreate" name="content"></textarea>
        </label>

        <!-- Tags List -->
        <div id="tags-select-section">
          <div id="mainTagsCreate">
            <p>Main Tag: (Only One)</p>
            <form>
              <label for="technicalBoxCreate">
                <input type="checkbox" id="technicalBoxCreate" name="techCreate" value="technical">
                Technical
              </label>
              <label for="non-technicalBoxCreate">
                <input type="checkbox" id="non-technicalBoxCreate" name="nontechCreate" value="non-technical">
                Non-Technical
              </label>
              <br>
              <div id="techSubTagsCreate" style="display: none;">
                <p>Sub Tag: (At least one)</p>
                <label for="softwareBoxCreate">
                  <input type="checkbox" id="softwareBoxCreate" name="softwareCreate" value="Software">
                  Software
                </label>
                <label for="hardwareBoxCreate">
                  <input type="checkbox" id="hardwareBoxCreate" name="hardwareCreate" value="Hardware">
                  Hardware
                </label>
              </div>
              <div id="non-techSubTagsCreate" style="display: none;">
                <p>Sub Tag: (At least one)</p>
                <label for="printingBoxCreate">
                  <input type="checkbox" id="printingBoxCreate" name="printingCreate" value="Printing">
                  Printing
                </label>
                <label for="adminBoxCreate">
                  <input type="checkbox" id="adminBoxCreate" name="adminCreate" value="Admin">
                  Admin
                </label>
              </div>
            </form>
            <br>
          </div>
        </div>

        <div>
          <button class="clickable" type="button" onClick="sendPost(false, 1)">Post</button>
          <button class="clickable" type="button" onClick="sendPost(false, 0)">Save as Draft</button>
          <button class="clickable" type="button" onClick="closeCreatePost()">Cancel</button>
          <p id="createPostErrorText"></p>
        </div>
      </div>
    </form>


    <div class="main" id="mainID">

    </div>
    <div class="pop-up" id="deletePost">
      <h2> Are you sure you want to delete this post?</h2> <span id="postIDHidden" style="display: none;"></span>
      <button id="yesDelete" onclick="deletePostConfirm()">Yes</button>
      <button id="noDelete" onclick="deletePostCancel()">No</button>
    </div>

    <div class="right-sidebar" id="postsRightSidebar">
      <input type="text" id="searchText" name="searchText" placeholder="Search...">
      <input class="clickable" type="submit" id="searchButton" onClick="searchPost(0, 0)" value="Search">
      <br><br>
      <input class="clickable" type="submit" value="Create Post" onclick="createPost()">
      <p id="viewSavedPosts" class="clickable" onclick="viewSavedPosts(0)"> <u> View Saved Posts </u></p>
      <p id="viewPostsP" class="clickable" onclick="viewMyPosts(0)"> <u> View My Posts </u></p>
    </div>
    <div class="right-sidebar" id="myPostsRightSidebar" style="display: none;">
      <br>
      <br>
      <br><br>
      <input class="clickable" type="submit" value="Create Post" onclick="createPost()">
      <p id="viewSavedPostsH" class="clickable" onclick="viewSavedPosts(0)"> <u> View Saved Posts </u></p>
      <p id="viewPostsH" class="clickable" onclick="viewMyPosts(0)"> <u> View My Posts </u></p>
      <p id="viewPosts" class="clickable"> <u> View Community Posts </u></p>
    </div>
  </div>
  <div id="newDiv" style="display: none;">This is the new content.</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  //For updating main Div
  $(document).ready(function () {
    // Function to handle the AJAX request
    function updateResult(start) {
      var sortValue = $('input[name="sort"]:checked').val(); // Get the value of the selected radio button
      var technicalChecked = $('#technicalBox').is(':checked'); // Check if the technical checkbox is checked
      var softwareChecked = $('#techsub1').is(':checked'); // Check if the software checkbox is checked
      var hardwareChecked = $('#techsub2').is(':checked'); // Check if the hardware checkbox is checked
      var nontechnicalChecked = $('#non-technicalBox').is(':checked'); // Check if the non-technical checkbox is checked
      var printingChecked = $('#nontechsub1').is(':checked'); // Check if the printing checkbox is checked
      var adminChecked = $('#nontechsub2').is(':checked'); // Check if the admin checkbox is checked
      var userID = '<?php echo $employeeID; ?>';
      // Send AJAX request PHP script
      $.ajax({
        type: "POST",
        url: "CommunityPosts.php",
        data: {
          sort: sortValue,
          technical: technicalChecked,
          software: softwareChecked,
          hardware: hardwareChecked,
          nontechnical: nontechnicalChecked,
          printing: printingChecked,
          admin: adminChecked,
          start: start,
          userID: userID
        },
        success: function (response) {
          $('#mainID').html(response); // Update the result div with the response from the PHP script
        }
      });
    }

    // Event listener for the button click
    $('#applyButton').click(function () {
      $('#mainID').html("");
      // Call the updateResult function with the desired start parameter
      updateResult(0);
    });

    // Event listener for the button click
    $('#viewPosts').click(function () {
      document.getElementById("myPostsLeftSidebar").style.display = "none";
      document.getElementById("postsLeftSidebar").style.display = "block";
      document.getElementById("myPostsRightSidebar").style.display = "none";
      document.getElementById("postsRightSidebar").style.display = "block";
      $('#mainID').html("");
      // Call the updateResult function with the desired start parameter
      updateResult(0);
    });

    // Event listener for the button click
    $(document).on('click', '#forwardPage', function () {
      // Get the span element by its ID
      var currentPage = document.getElementById("currentPage");
      // Read the text content of the span and parse it as an integer
      var currentPageInt = parseInt(currentPage.innerHTML);
      // Get the span element by its ID
      var totalPages = document.getElementById("totalPages");
      // Read the text content of the span and parse it as an integer
      var totalPagesInt = parseInt(totalPages.innerHTML);
      if (currentPageInt < totalPagesInt) {
        // Call the updateResult function with the desired start parameter
        updateResult(currentPageInt * 3);
      }
    });

    // Event listener for the button click
    $(document).on('click', '#backPage', function () {
      // Get the span element by its ID
      var currentPage = document.getElementById("currentPage");
      // Read the text content of the span and parse it as an integer
      var currentPageInt = parseInt(currentPage.innerHTML);
      if (currentPageInt > 1) {
        // Call the updateResult function with the desired start parameter
        updateResult((currentPageInt - 1) * 3 - 3);
      }
    });
    // Call the updateResult function initially to populate the result div with default content
    updateResult(0);
  });
  var technicalBox = document.getElementById("technicalBox");
  var subTechnicalBox = document.querySelectorAll(".technical-sub");

  technicalBox.addEventListener("change", function () {
    var isChecked = technicalBox.checked;

    // Set the state of sub-checkboxes to match the main checkbox
    subTechnicalBox.forEach(function (subTechCheckbox) {
      subTechCheckbox.checked = isChecked;
    });

  });

  // Event listener for the button click
  $(document).on('click', '#forwardPageMyPosts', function () {
    // Get the span element by its ID
    var currentPage = document.getElementById("currentPage");
    // Read the text content of the span and parse it as an integer
    var currentPageInt = parseInt(currentPage.innerHTML);
    // Get the span element by its ID
    var totalPages = document.getElementById("totalPages");
    // Read the text content of the span and parse it as an integer
    var totalPagesInt = parseInt(totalPages.innerHTML);
    if (currentPageInt < totalPagesInt) {
      // Call the updateResult function with the desired start parameter
      viewMyPosts(currentPageInt * 3);
    }
  });

  // Event listener for the button click
  $(document).on('click', '#backPageMyPosts', function () {
    // Get the span element by its ID
    var currentPage = document.getElementById("currentPage");
    // Read the text content of the span and parse it as an integer
    var currentPageInt = parseInt(currentPage.innerHTML);
    if (currentPageInt > 1) {
      // Call the updateResult function with the desired start parameter
      viewMyPosts((currentPageInt - 1) * 3 - 3);
    }
  });

  var technicalSubBox1 = document.getElementById("techsub1");
  var technicalSubBox2 = document.getElementById("techsub2");
  technicalSubBox1.addEventListener("change", function () {
    if (technicalSubBox1.checked) {
      document.getElementById("technicalBox").checked = true;
    }
    if (!technicalSubBox1.checked && !technicalSubBox2.checked) {
      document.getElementById("technicalBox").checked = false;
    }
  });
  technicalSubBox2.addEventListener("change", function () {
    if (technicalSubBox2.checked) {
      document.getElementById("technicalBox").checked = true
    }
    if (!technicalSubBox1.checked && !technicalSubBox2.checked) {
      document.getElementById("technicalBox").checked = false;
    }
  });

  var nonTechnicalBox = document.getElementById("non-technicalBox");
  var subNonTechnicalBox = document.querySelectorAll(".nontechnical-sub");

  nonTechnicalBox.addEventListener("change", function () {
    var isChecked = nonTechnicalBox.checked;

    // Set the state of sub-checkboxes to match the main checkbox
    subNonTechnicalBox.forEach(function (subCheckbox) {
      subCheckbox.checked = isChecked;
    });
    /*
        if (isChecked){
            document.getElementById("post1").style.display = "block"
            document.getElementById("post3").style.display = "block"
          }else{
            document.getElementById("post1").style.display = "none"
            document.getElementById("post3").style.display = "none"
          }
          */
  });

  var nonTechnicalSubBox1 = document.getElementById("nontechsub1");
  var nonTechnicalSubBox2 = document.getElementById("nontechsub2");
  nonTechnicalSubBox1.addEventListener("change", function () {
    if (nonTechnicalSubBox1.checked) {
      document.getElementById("non-technicalBox").checked = true;
    }
    if (!nonTechnicalSubBox1.checked && !nonTechnicalSubBox2.checked) {
      document.getElementById("non-technicalBox").checked = false;
    }
  });
  nonTechnicalSubBox2.addEventListener("change", function () {
    if (nonTechnicalSubBox2.checked) {
      document.getElementById("non-technicalBox").checked = true;
    }
    if (!nonTechnicalSubBox1.checked && !nonTechnicalSubBox2.checked) {
      document.getElementById("non-technicalBox").checked = false;
    }
  });

  function openPost1() {
    document.getElementById("Pop-up-post1").style.display = "block";
    document.getElementById("Pop-up-post1").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }
  function openPost2() {
    document.getElementById("Pop-up-post2").style.display = "block";
    document.getElementById("Pop-up-post2").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }
  function openPost3() {
    document.getElementById("Pop-up-post3").style.display = "block";
    document.getElementById("Pop-up-post3").style.backdropFilter = "blur(8px)";
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }
  function closeForm() {
    if (document.getElementById("Pop-up-post1")) {
      document.getElementById("Pop-up-post1").style.display = "none";
      document.getElementById("Pop-up-post1").style.backdropFilter = "";
    }
    if (document.getElementById("Pop-up-post2")) {
      document.getElementById("Pop-up-post2").style.display = "none";
      document.getElementById("Pop-up-post2").style.backdropFilter = "";
    }
    if (document.getElementById("Pop-up-post3")) {
      document.getElementById("Pop-up-post3").style.display = "none";
      document.getElementById("Pop-up-post3").style.backdropFilter = "";
    }
    if (document.getElementById("Comments1")) {
      if (document.getElementById("Comments1").style.display == "block") {
        document.getElementById("Comments1").style.display = "none";
      }
    }
    if (document.getElementById("Comments2")) {
      if (document.getElementById("Comments2").style.display == "block") {
        document.getElementById("Comments2").style.display = "none";
      }
    }
    if (document.getElementById("Comments3")) {
      if (document.getElementById("Comments3").style.display == "block") {
        document.getElementById("Comments3").style.display = "none";
      }
    }
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }
  function openComments() {
    if (document.getElementById("Comments1")) {
      if (document.getElementById("Comments1").style.display == "block") {
        document.getElementById("Comments1").style.display = "none";
      } else if (document.getElementById("Comments1").style.display == "none") {
        document.getElementById("Comments1").style.display = "block";
      }
    }
    if (document.getElementById("Comments2")) {
      if (document.getElementById("Comments2").style.display == "block") {
        document.getElementById("Comments2").style.display = "none";
      } else if (document.getElementById("Comments2").style.display == "none") {
        document.getElementById("Comments2").style.display = "block";
      }
    }
    if (document.getElementById("Comments3")) {
      if (document.getElementById("Comments3").style.display == "block") {
        document.getElementById("Comments3").style.display = "none";
      } else if (document.getElementById("Comments3").style.display == "none") {
        document.getElementById("Comments3").style.display = "block";
      }
    }
  }
  function interactPost(userID, postID, table, view) {
    var popupPost = 0;
    if (document.getElementById("Pop-up-post1")) {
      if (document.getElementById("Pop-up-post1").style.display == "block") {
        popupPost = 1;
      } else if (document.getElementById("Pop-up-post2")) {
        if (document.getElementById("Pop-up-post2").style.display == "block") {
          popupPost = 2;
        } else if (document.getElementById("Pop-up-post3")) {
          if (document.getElementById("Pop-up-post3").style.display == "block") {
            popupPost = 3;
          }
        }
      }
    }
    console.log(popupPost);
    var popupComment = 0;
    if (document.getElementById("Comments1")) {
      if (document.getElementById("Comments1").style.display == "block") {
        popupComment = 1;
      } else if (document.getElementById("Comments2")) {
        if (document.getElementById("Comments2").style.display == "block") {
          popupComment = 2;
        } else if (document.getElementById("Comments3")) {
          if (document.getElementById("Comments3").style.display == "block") {
            popupComment = 3;
          }
        }
      }
    }


    // Send AJAX request PHP script
    $.ajax({
      type: "POST",
      url: "interactPost.php",
      data: {
        userID: userID,
        postID: postID,
        table: table
      },
      success: function (response) {
        var currentPageElement = document.getElementById("currentPage");
        console.log(popupPost);
        if (currentPageElement == null) {
          searchPost(popupPost, popupComment);

        } else if (view == "s"){
          viewSavedPosts(0, popupPost, popupComment);
        }else {
          var start = parseInt(document.getElementById("currentPage").innerHTML) * 3 - 3;
          var sortValue = $('input[name="sort"]:checked').val(); // Get the value of the selected radio button
          var technicalChecked = $('#technicalBox').is(':checked'); // Check if the technical checkbox is checked
          var softwareChecked = $('#techsub1').is(':checked'); // Check if the software checkbox is checked
          var hardwareChecked = $('#techsub2').is(':checked'); // Check if the hardware checkbox is checked
          var nontechnicalChecked = $('#non-technicalBox').is(':checked'); // Check if the non-technical checkbox is checked
          var printingChecked = $('#nontechsub1').is(':checked'); // Check if the printing checkbox is checked
          var adminChecked = $('#nontechsub2').is(':checked'); // Check if the admin checkbox is checked
          var userID = '<?php echo $employeeID; ?>';
          // Send AJAX request PHP script
          $.ajax({
            type: "POST",
            url: "CommunityPosts.php",
            data: {
              sort: sortValue,
              technical: technicalChecked,
              software: softwareChecked,
              hardware: hardwareChecked,
              nontechnical: nontechnicalChecked,
              printing: printingChecked,
              admin: adminChecked,
              start: start,
              userID: userID
            },
            success: function (response) {
              $('#mainID').html(response); // Update the result div with the response from the PHP script
              if (popupPost == 1) {
                openPost1();
              } else if (popupPost == 2) {
                openPost2();
              } else if (popupPost == 3) {
                openPost3();
              }
              if (popupComment > 0) {
                openComments();
              }
            }
          });
        }
      }
    });
  }
  function addComment(postID, userID, incrementer, view) {
    var text = document.getElementById("commentText" + incrementer).value;
    console.log(text);
    console.log("text");
    var popupPost = 0;
    if (document.getElementById("Pop-up-post1")) {
      if (document.getElementById("Pop-up-post1").style.display == "block") {
        popupPost = 1;
      } else if (document.getElementById("Pop-up-post2")) {
        if (document.getElementById("Pop-up-post2").style.display == "block") {
          popupPost = 2;
        } else if (document.getElementById("Pop-up-post3")) {
          if (document.getElementById("Pop-up-post3").style.display == "block") {
            popupPost = 3;
          }
        }
      }
    }
    var popupComment = 0;
    if (document.getElementById("Comments1")) {
      if (document.getElementById("Comments1").style.display == "block") {
        popupComment = 1;
      } else if (document.getElementById("Comments2")) {
        if (document.getElementById("Comments2").style.display == "block") {
          popupComment = 2;
        } else if (document.getElementById("Comments3")) {
          if (document.getElementById("Comments3").style.display == "block") {
            popupComment = 3;
          }
        }
      }
    }
      // Send AJAX request PHP script
    $.ajax({
      type: "POST",
      url: "addComment.php",
      data: {
        userID: userID,
        postID: postID,
        text: text
      },
      success: function (response) {
        var currentPageElement = document.getElementById("currentPage");
        if (currentPageElement == null) {
          searchPost(popupPost, popupComment);

        } else if (view == "s"){
          viewSavedPosts(0, popupPost, popupComment);
        }else {
          var start = parseInt(document.getElementById("currentPage").innerHTML) * 3 - 3;
          var sortValue = $('input[name="sort"]:checked').val(); // Get the value of the selected radio button
          var technicalChecked = $('#technicalBox').is(':checked'); // Check if the technical checkbox is checked
          var softwareChecked = $('#techsub1').is(':checked'); // Check if the software checkbox is checked
          var hardwareChecked = $('#techsub2').is(':checked'); // Check if the hardware checkbox is checked
          var nontechnicalChecked = $('#non-technicalBox').is(':checked'); // Check if the non-technical checkbox is checked
          var printingChecked = $('#nontechsub1').is(':checked'); // Check if the printing checkbox is checked
          var adminChecked = $('#nontechsub2').is(':checked'); // Check if the admin checkbox is checked
          var userID = '<?php echo $employeeID; ?>';
          // Send AJAX request PHP script
          $.ajax({
            type: "POST",
            url: "CommunityPosts.php",
            data: {
              sort: sortValue,
              technical: technicalChecked,
              software: softwareChecked,
              hardware: hardwareChecked,
              nontechnical: nontechnicalChecked,
              printing: printingChecked,
              admin: adminChecked,
              start: start,
              userID: userID
            },
            success: function (response) {
              $('#mainID').html(response); // Update the result div with the response from the PHP script
              if (popupPost == 1) {
                openPost1();
              } else if (popupPost == 2) {
                openPost2();
              } else if (popupPost == 3) {
                openPost3();
              }
              if (popupComment > 0) {
                openComments();
              }
            }
          });
        }
      }
    });
    }
    
  function deleteComment(postID, userID, text, view) {
    var popupPost = 0;
    if (document.getElementById("Pop-up-post1")) {
      if (document.getElementById("Pop-up-post1").style.display == "block") {
        popupPost = 1;
      } else if (document.getElementById("Pop-up-post2")) {
        if (document.getElementById("Pop-up-post2").style.display == "block") {
          popupPost = 2;
        } else if (document.getElementById("Pop-up-post3")) {
          if (document.getElementById("Pop-up-post3").style.display == "block") {
            popupPost = 3;
          }
        }
      }
    }

    var popupComment = 0;
    if (document.getElementById("Comments1")) {
      if (document.getElementById("Comments1").style.display == "block") {
        popupComment = 1;
      } else if (document.getElementById("Comments2")) {
        if (document.getElementById("Comments2").style.display == "block") {
          popupComment = 2;
        } else if (document.getElementById("Comments3")) {
          if (document.getElementById("Comments3").style.display == "block") {
            popupComment = 3;
          }
        }
      }
    }

    // Send AJAX request PHP script
    $.ajax({
      type: "POST",
      url: "deleteComment.php",
      data: {
        userID: userID,
        postID: postID,
        text: text
      },
      success: function (response) {
        var currentPageElement = document.getElementById("currentPage");
        if (currentPageElement == null) {
          searchPost(popupPost, popupComment);
        } else if (view == "s"){
          viewSavedPosts(0, popupPost, popupComment);
        } else {
          var start = parseInt(document.getElementById("currentPage").innerHTML) * 3 - 3;
          var sortValue = $('input[name="sort"]:checked').val(); // Get the value of the selected radio button
          var technicalChecked = $('#technicalBox').is(':checked'); // Check if the technical checkbox is checked
          var softwareChecked = $('#techsub1').is(':checked'); // Check if the software checkbox is checked
          var hardwareChecked = $('#techsub2').is(':checked'); // Check if the hardware checkbox is checked
          var nontechnicalChecked = $('#non-technicalBox').is(':checked'); // Check if the non-technical checkbox is checked
          var printingChecked = $('#nontechsub1').is(':checked'); // Check if the printing checkbox is checked
          var adminChecked = $('#nontechsub2').is(':checked'); // Check if the admin checkbox is checked
          var userID = '<?php echo $employeeID; ?>';
          // Send AJAX request PHP script
          $.ajax({
            type: "POST",
            url: "CommunityPosts.php",
            data: {
              sort: sortValue,
              technical: technicalChecked,
              software: softwareChecked,
              hardware: hardwareChecked,
              nontechnical: nontechnicalChecked,
              printing: printingChecked,
              admin: adminChecked,
              start: start,
              userID: userID
            },
            success: function (response) {
              $('#mainID').html(response); // Update the result div with the response from the PHP script
              if (popupPost == 1) {
                openPost1();
              } else if (popupPost == 2) {
                openPost2();
              } else if (popupPost == 3) {
                openPost3();
              }
              if (popupComment > 0) {
                openComments();
              }
            }
          });
        }
      }
    });
  }
  function viewMyPosts(start) {
    document.getElementById("postsLeftSidebar").style.display = "none";
    document.getElementById("myPostsLeftSidebar").style.display = "block";
    document.getElementById("postsRightSidebar").style.display = "none";
    document.getElementById("myPostsRightSidebar").style.display = "block";
    document.getElementById("viewSavedPostsH").style.display = "block";
    document.getElementById("viewPostsH").style.display = "none";
    var userID = '<?php echo $employeeID; ?>';
    // Send AJAX request PHP script
    $.ajax({
      type: "POST",
      url: "MyPosts.php",
      data: {
        userID: userID,
        start: start
      },
      success: function (response) {
        $('#mainID').html(response); // Update the result div with the response from the PHP script
      }
    });


  }
function viewSavedPosts(start, popupPost, popupComment){
    document.getElementById("postsLeftSidebar").style.display = "none";
    document.getElementById("myPostsLeftSidebar").style.display = "block";
    document.getElementById("postsRightSidebar").style.display = "none";
    document.getElementById("myPostsRightSidebar").style.display = "block";
    document.getElementById("viewSavedPostsH").style.display = "none";
    document.getElementById("viewPostsH").style.display = "block";
    var userID = '<?php echo $employeeID; ?>';
    // Send AJAX request PHP script
    $.ajax({
      type: "POST",
      url: "savedPosts.php",
      data: {
        userID: userID,
        start: start
      },
      success: function (response) {
        $('#mainID').html(response); // Update the result div with the response from the PHP script
        if (popupPost == 1) {
          openPost1();
        } else if (popupPost == 2) {
          openPost2();
        } else if (popupPost == 3) {
          openPost3();
        }
        if (popupComment > 0) {
          openComments();
        }
      }
    });
}

  function deletePost(postID) {
    document.getElementById("deletePost").style.display = "block";
    document.getElementById("deletePost").style.backdropFilter = "blur(8px)";
    document.getElementById("postIDHidden").innerHTML = postID;
    // The above line makes sure that the form itself is not darkened, only the background
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }
  function deletePostConfirm() {
    var postID = document.getElementById("postIDHidden").innerHTML;
    //Could add in userID as a way of extra authentication
    $.ajax({
      type: "POST",
      url: "deletePost.php",
      data: {
        //userID: userID, 
        postID: postID
      },
      success: function (response) {
        $('#mainID').html(response); // Update the result div with the response from the PHP script
        viewMyPosts(0);
      }
    });
    document.getElementById("deletePost").style.display = "none";
    document.getElementById("deletePost").style.backdropFilter = "";
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }
  function deletePostCancel() {
    document.getElementById("deletePost").style.display = "none";
    document.getElementById("deletePost").style.backdropFilter = "";

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }
  function searchPost(popupPost, popupComment) {
    var text = document.getElementById("searchText").value;
    var userID = '<?php echo $employeeID; ?>';
    if (!text.trim()) {
      var start = 0;
      var sortValue = $('input[name="sort"]:checked').val(); // Get the value of the selected radio button
      var technicalChecked = $('#technicalBox').is(':checked'); // Check if the technical checkbox is checked
      var softwareChecked = $('#techsub1').is(':checked'); // Check if the software checkbox is checked
      var hardwareChecked = $('#techsub2').is(':checked'); // Check if the hardware checkbox is checked
      var nontechnicalChecked = $('#non-technicalBox').is(':checked'); // Check if the non-technical checkbox is checked
      var printingChecked = $('#nontechsub1').is(':checked'); // Check if the printing checkbox is checked
      var adminChecked = $('#nontechsub2').is(':checked'); // Check if the admin checkbox is checked
      // Send AJAX request PHP script
      $.ajax({
        type: "POST",
        url: "CommunityPosts.php",
        data: {
          sort: sortValue,
          technical: technicalChecked,
          software: softwareChecked,
          hardware: hardwareChecked,
          nontechnical: nontechnicalChecked,
          printing: printingChecked,
          admin: adminChecked,
          start: start,
          userID: userID
        },
        success: function (response) {
          $('#mainID').html(response); // Update the result div with the response from the PHP script
        }
      });
    } else {
      // Send AJAX request PHP script
      $.ajax({
        type: "POST",
        url: "searchPost.php",
        data: {
          text: text,
          userID: userID
        },
        success: function (response) {
          $('#mainID').html(response); // Update the result div with the response from the PHP script
          if (popupPost == 1) {
            openPost1();
          } else if (popupPost == 2) {
            openPost2();
          } else if (popupPost == 3) {
            openPost3();
          }
          if (popupComment > 0) {
            openComments();
          }
        }
      });
    }
  }

  //CreatePost
  function createPost() {
    document.getElementById("create-post-popup").style.display = "block";
    document.getElementById("contentForm").style.display = "block";
    document.getElementById("create-post-popup").style.backdropFilter = "blur(8px)";
    // document.getElementById("contentForm").style.backdropFilter = "blur(8px)";       
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring
  }
  var technicalBoxCreate = document.getElementById("technicalBoxCreate");
  var nonTechnicalBoxCreate = document.getElementById("non-technicalBoxCreate");
  technicalBoxCreate.addEventListener("change", function () {
    if (technicalBoxCreate.checked) {
      document.getElementById("techSubTagsCreate").style.display = "block";
      document.getElementById("non-techSubTagsCreate").style.display = "none";
      document.getElementById("non-technicalBoxCreate").checked = false;
    } else if (!technicalBoxCreate.checked) {
      document.getElementById("techSubTagsCreate").style.display = "none";
    }

  });
  nonTechnicalBoxCreate.addEventListener("change", function () {
    if (nonTechnicalBoxCreate.checked) {
      document.getElementById("techSubTagsCreate").style.display = "none";
      document.getElementById("non-techSubTagsCreate").style.display = "block";
      document.getElementById("technicalBoxCreate").checked = false;
    } else if (!nonTechnicalBoxCreate.checked) {
      document.getElementById("non-techSubTagsCreate").style.display = "none";
    }

  });
  var technicalBoxEdit = document.getElementById("technicalBoxEdit");
  var nonTechnicalBoxEdit = document.getElementById("non-technicalBoxEdit");
  technicalBoxEdit.addEventListener("change", function () {
    if (technicalBoxEdit.checked) {
      document.getElementById("techSubTagsEdit").style.display = "block";
      document.getElementById("non-techSubTagsEdit").style.display = "none";
      document.getElementById("non-technicalBoxEdit").checked = false;
    } else if (!technicalBoxEdit.checked) {
      document.getElementById("techSubTagsEdit").style.display = "none";
    }

  });
  nonTechnicalBoxEdit.addEventListener("change", function () {
    if (nonTechnicalBoxEdit.checked) {
      document.getElementById("techSubTagsEdit").style.display = "none";
      document.getElementById("non-techSubTagsEdit").style.display = "block";
      document.getElementById("technicalBoxEdit").checked = false;
    } else if (!nonTechnicalBoxEdit.checked) {
      document.getElementById("non-techSubTagsEdit").style.display = "none";
    }

  });
  function sendPost(edit, draft) {
    var postID;
    if (edit) {
      console.log("edit");
      var userID = '<?php echo $employeeID; ?>';
      var title = document.getElementById("titleEdit").value;
      var text = document.getElementById("textEdit").value;
      postID = document.getElementById("editPostID").innerHTML;
      var mainTag;
      var subTag1;
      var subTag2;
      if (document.getElementById("technicalBoxEdit").checked) {
        mainTag = 'T';
        if (document.getElementById("softwareBoxEdit").checked) {
          subTag1 = "Software";
        }
        if (document.getElementById("hardwareBoxEdit").checked) {
          subTag2 = "Hardware";
        }
      } else if (document.getElementById("non-technicalBoxEdit").checked) {
        mainTag = 'NT';
        if (document.getElementById("printingBoxEdit").checked) {
          subTag1 = "Printing";
        }
        if (document.getElementById("adminBoxEdit").checked) {
          subTag2 = "Admin";
        }
      }
    } else {
      var userID = '<?php echo $employeeID; ?>';
      var title = document.getElementById("titleCreate").value;
      var text = document.getElementById("textCreate").value;
      // Create a new Date object
      var currentDate = new Date();

      // Get the current year, month, and day
      var year = currentDate.getFullYear();
      var month = currentDate.getMonth() + 1; // January is 0, so we add 1
      var day = currentDate.getDate();

      // Format the date as needed (YYYY-MM-DD)
      var date = year + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
      var mainTag;
      var subTag1;
      var subTag2;
      if (document.getElementById("technicalBoxCreate").checked) {
        mainTag = 'T';
        if (document.getElementById("softwareBoxCreate").checked) {
          subTag1 = "Software";
        }
        if (document.getElementById("hardwareBoxCreate").checked) {
          subTag2 = "Hardware";
        }
      } else if (document.getElementById("non-technicalBoxCreate").checked) {
        mainTag = 'NT';
        if (document.getElementById("printingBoxCreate").checked) {
          subTag1 = "Printing";
        }
        if (document.getElementById("adminBoxCreate").checked) {
          subTag2 = "Admin";
        }
      }
    }
    if ((title && text && mainTag && (subTag1 || subTag2)) || (draft == 0 && title && mainTag && (subTag1 || subTag2))) {
      // Send AJAX request PHP script
      $.ajax({
        type: "POST",
        url: "sendPost.php",
        data: {
          edit: edit,
          postID: postID,
          userID: userID,
          title: title,
          text: text,
          date: date,
          mainTag: mainTag,
          subTag1: subTag1,
          subTag2: subTag2,
          draft: draft
        },
        success: function (response) {
          if (edit == true) {
            closeEditPost();
            console.log("reload myposts");
            var start = parseInt(document.getElementById("currentPage").innerHTML) * 3 - 3;
            viewMyPosts(start);
          } else {
            closeCreatePost();
            var start = parseInt(document.getElementById("currentPage").innerHTML) * 3 - 3;
            var sortValue = $('input[name="sort"]:checked').val(); // Get the value of the selected radio button
            var technicalChecked = $('#technicalBox').is(':checked'); // Check if the technical checkbox is checked
            var softwareChecked = $('#techsub1').is(':checked'); // Check if the software checkbox is checked
            var hardwareChecked = $('#techsub2').is(':checked'); // Check if the hardware checkbox is checked
            var nontechnicalChecked = $('#non-technicalBox').is(':checked'); // Check if the non-technical checkbox is checked
            var printingChecked = $('#nontechsub1').is(':checked'); // Check if the printing checkbox is checked
            var adminChecked = $('#nontechsub2').is(':checked'); // Check if the admin checkbox is checked
            var userID = '<?php echo $employeeID; ?>';
            if (document.getElementById("myPostsRightSidebar").style.display == "block"){
              if (document.getElementById("mainHeaderID").innerHTML == "Saved Posts"){
                viewSavedPosts(0);
              }else{
                viewMyPosts(0);
              }
            }else{
            // Send AJAX request PHP script
            $.ajax({
              type: "POST",
              url: "CommunityPosts.php",
              data: {
                sort: sortValue,
                technical: technicalChecked,
                software: softwareChecked,
                hardware: hardwareChecked,
                nontechnical: nontechnicalChecked,
                printing: printingChecked,
                admin: adminChecked,
                start: start,
                userID: userID
              },
              success: function (response) {
                $('#mainID').html(response); // Update the result div with the response from the PHP script
              }
            });
          }
          }
        }
      });

    } else {
      if (draft == 0){
        document.getElementById("createPostErrorText").innerHTML = "Please ensure tags, and title have been filled. ";
      }else{
        document.getElementById("createPostErrorText").innerHTML = "Please ensure no field is empty, and tags have been appropriately selected. ";
        document.getElementById("editPostErrorText").innerHTML = "Please ensure no field is empty, and tags have been appropriately selected";
      }
      
    }

  }

  function closeCreatePost() {
    document.getElementById("create-post-popup").style.display = "none";
    document.getElementById("create-post-popup").style.backdropFilter = "";
    document.getElementById("contentForm").style.display = "none";
    document.getElementById("createPostErrorText").innerHTML = "";
    // document.getElementById("contentForm").style.backdropFilter = ""; 
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }

  function editPost(incrementer, mainTag, subTags, postID) {
    document.getElementById("edit-post-popup").style.display = "block";
    document.getElementById("editContentForm").style.display = "block";
    document.getElementById("edit-post-popup").style.backdropFilter = "blur(8px)";
    // document.getElementById("contentForm").style.backdropFilter = "blur(8px)";       
    // The above line makes sure that the form itself is not darkened, only the background

    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.display = "block";
    setTimeout(() => {
      backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    }, 10);
    // Above line makes the transition between dimming and brightened less jarring

    document.getElementById("titleEdit").value = document.getElementById("myTitle" + incrementer).innerText;
    document.getElementById("textEdit").value = document.getElementById("editText" + incrementer).innerHTML;
    document.getElementById("editPostID").innerHTML = postID;
    subTags = subTags + ", , ";
    const subTagsArr = subTags.split(',');
    if (mainTag.trim().toLowerCase() == "technical") {
      document.getElementById("technicalBoxEdit").checked = true;
      document.getElementById("techSubTagsEdit").style.display = "block";
      if (subTagsArr[0].trim().toLowerCase() == "software" || subTagsArr[1].trim().toLowerCase() == "software") {
        document.getElementById("softwareBoxEdit").checked = true;
      }
      if (subTagsArr[0].trim().toLowerCase() == "hardware" || subTagsArr[1].trim().toLowerCase() == "hardware") {
        document.getElementById("hardwareBoxEdit").checked = true;
      }
    } else if (mainTag.trim().toLowerCase() == "non-technical") {
      document.getElementById("non-technicalBoxEdit").checked = true;
      document.getElementById("non-techSubTagsEdit").style.display = "block";
      if (subTagsArr[0].trim().toLowerCase() == "printing" || subTagsArr[1].trim().toLowerCase() == "printing") {
        document.getElementById("printingBoxEdit").checked = true;
      }
      if (subTagsArr[0].trim().toLowerCase() == "admin" || subTagsArr[1].trim().toLowerCase() == "admin") {
        document.getElementById("adminBoxEdit").checked = true;
      }
    }

  }
  function closeEditPost() {
    document.getElementById("edit-post-popup").style.display = "none";
    document.getElementById("edit-post-popup").style.backdropFilter = "";
    document.getElementById("editContentForm").style.display = "none";
    document.getElementById("techSubTagsEdit").style.display = "none";
    document.getElementById("non-techSubTagsEdit").style.display = "none";
    document.getElementById("non-technicalBoxEdit").checked = false;
    document.getElementById("technicalBoxEdit").checked = false;
    document.getElementById("softwareBoxEdit").checked = false;
    document.getElementById("hardwareBoxEdit").checked = false;
    document.getElementById("printingBoxEdit").checked = false;
    document.getElementById("adminBoxEdit").checked = false;
    document.getElementById("editPostErrorText").innerHTML = "";
    // document.getElementById("contentForm").style.backdropFilter = ""; 
    let backgroundOverlay = document.getElementById("overlay");
    backgroundOverlay.style.backgroundColor = "rgba(0, 0, 0, 0)";
    setTimeout(() => {
      backgroundOverlay.style.display = "none";
    }, 300);
  }


</script>

</html>
