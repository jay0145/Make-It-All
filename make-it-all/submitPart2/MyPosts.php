<?php
$userID = isset($_POST['userID']) ? $_POST['userID'] : 0; //Get the start value
$start = isset($_POST['start']) ? $_POST['start'] : 0; //Get the start value


//Separate database details, use include to get them
include "config.php";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}

$sqlMyPosts = "SELECT p.*, GROUP_CONCAT(st.subTag) AS subTags 
                FROM postTable p 
                JOIN subTagTable st ON p.postID = st.postID 
                WHERE p.employeeID = $userID GROUP BY p.postID ORDER BY p.date DESC;";
$resultMyPosts = mysqli_query($conn,$sqlMyPosts);

$amountOfPosts = mysqli_num_rows($resultMyPosts);
echo '<h2 id="mainHeaderID">Your Posts</h2> ';
    // Fetch data from the result set
    if ($amountOfPosts <= 0) {
        echo 'You have no posts.';
        echo '<br>';
    } else {
        //Just echo first three
        // Process each row 
        mysqli_data_seek($resultMyPosts, $start);
        $incrementer = 1;
        while ($rowPosts = mysqli_fetch_array($resultMyPosts)) {
            if ($incrementer > 3) {
                break; // Exit the loop if $incrementer is greater than 3
            }

        echo '<div class="pop-up" id="deletePost' . $rowPosts['postID'] . '">
                <h2> Are you sure you want to delete this post?</h2>
                <button id="yesDelete" onclick="deletePostConfirm()">Yes</button>
                <button id="noDelete" onclick="deletePostCancel()">No</button>
              </div>';

        $mainTag;
        if($rowPosts['mainTag'] == 'T'){
            $mainTag = "Technical";
        }elseif($rowPosts['mainTag'] == 'NT'){
            $mainTag = "Non-Technical";
        }
        $draft = "";
        if($rowPosts['posted'] == 0){
            $draft = "(Draft)";
        }else{
            $draft = "";
        }
        $text = $rowPosts['text'];
        $textPreview = strlen($text) > 150 ? substr($text, 0, 100) . '...' : $text;

        echo '<div class="post" id="myPost' . $incrementer . '">
                <p>'. $rowPosts["date"] . '</p>
                <h2 id="myTitle' . $incrementer .'"><u>' . $rowPosts["title"] . '</u></h2> <h3>' . $draft .' </h3>
                <p><b>Tags:</b> ' . $mainTag . ', ' . $rowPosts["subTags"] . '</p>
                <p>' . $textPreview . '</p>
                <div id="postDetails" class="postDetails">
                    <div><p><b></b> </p> </div>
                    <div></div>
                    <div><p class="likeCount"><img src="Images/edit.png" onclick="editPost(' . $incrementer . ',\'' . $mainTag . '\',\'' . $rowPosts["subTags"] . '\',\'' . $rowPosts['postID'] .'\')"></p></div>
                    <div><p class="likeCount"><img src="Images/delete.jpg" onclick="deletePost(' . $rowPosts['postID'] . ')"> </p></div>
                </div>
                <p id="editText' . $incrementer . '" style="display: none;">' . $text . ' </p>
              </div>';
            $incrementer = $incrementer + 1;

        }


        //FIX FOR MYPOSTS WHEN I HAVE ENOUGH, MAY have to combine php files and add an input paramter to determine whether I'm in myPosts or Community using if to surround each php file
        if ($amountOfPosts < 4){
            echo '<p align="center"> <span id="backPage"><u> < </u></span><span id="currentPage">1</span>/<span id="totalPages">1</span><span id="forwardPage"><u> > </u></span></p>';
        }else{
            $amountOfPages = intval(($amountOfPosts + 2) / 3);
            $currentPage = intval($start/3 + 1);
            echo '<p align="center"> <span id="backPageMyPosts"><u> < </u></span><span id="currentPage">' . $currentPage . '</span>/<span id="totalPages">' . $amountOfPages . '</span><span id="forwardPageMyPosts"><u> > </u></span></p>';
        }
        
    }
?>