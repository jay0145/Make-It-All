
<?php
    // Retrieve filter values from the AJAX request
    $sort = $_POST['sort']; // Sort value from radio button
    $technical = isset($_POST['technical']) ? $_POST['technical'] : false; // Check if technical checkbox is checked
    $software = isset($_POST['software']) ? $_POST['software'] : false; // Check if software checkbox is checked
    $hardware = isset($_POST['hardware']) ? $_POST['hardware'] : false; // Check if hardware checkbox is checked
    $nontechnical = isset($_POST['nontechnical']) ? $_POST['nontechnical'] : false; // Check if non-technical checkbox is checked
    $printing = isset($_POST['printing']) ? $_POST['printing'] : false; // Check if printing checkbox is checked
    $admin = isset($_POST['admin']) ? $_POST['admin'] : false; // Check if admin checkbox is checked
    $start = isset($_POST['start']) ? $_POST['start'] : 0; //Get the start value
    $userID = isset($_POST['userID']) ? $_POST['userID'] : 0; //Get the start value
    
    //Separate database details, use include to get them
    include "config.php";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }
    

    $mainTagsList = array(); 
    if($technical == "true"){
        $mainTagsList[] = "T";
        if($software == "false" && $hardware == "true"){
            $software = "true";
            $hardware = "true";
        }
    }
    if($nontechnical == "true"){
        $mainTagsList[] = "NT";
        if($printing == "false" && $admin == "true"){
            $printing = "true";
            $admin = "true";
        }
    }
    
    $subTagsList = array();
    //Can add if's to remove redundant if searches for if not NT or not T
    if($software == "true"){
        $subTagsList[] = "Software";
    }
    if($hardware == "true"){
        $subTagsList[] = "Hardware";
    }
    if($printing == "true"){
        $subTagsList[] = "Printing";
    }
    if($admin == "true"){
        $subTagsList[] = "Admin";
    }
    
    if($sort == "newest"){
        $sort = "p.date";
    }else if($sort == "popular"){
        $sort = "likeCount";
    }
    

    // Convert the PHP list into a comma-separated string for SQL
    $mainTagsString = "'" . implode("', '", $mainTagsList) . "'";
    $subTagsString = "'" . implode("', '", $subTagsList) . "'";
    
    // Prepare the SQL statement with placeholders
    $sqlPosts = "SELECT p.*, 
                    GROUP_CONCAT(st.subTag) AS subTags, 
                    COALESCE(c.commentCount, 0) AS commentCount,
                    COALESCE(l.likeCount, 0) AS likeCount,
                    COALESCE(d.dislikeCount, 0) AS dislikeCount
                FROM postTable p 
                JOIN subTagTable st ON p.postID = st.postID 
                JOIN savedPostTable sp on sp.postID = p.postID
                LEFT JOIN (SELECT postID, COUNT(*) AS commentCount FROM commentTable GROUP BY postID) c 
                    ON p.postID = c.postID 
                LEFT JOIN (SELECT postID, COUNT(*) AS likeCount FROM likeTable GROUP BY postID) l
                    ON p.postID = l.postID
                LEFT JOIN (SELECT postID, COUNT(*) AS dislikeCount FROM dislikeTable GROUP BY postID) d
                    ON p.postID = d.postID
                WHERE p.mainTag IN ($mainTagsString) 
                AND st.subTag IN ($subTagsString) 
                AND posted = 1
                GROUP BY p.postID 
                ORDER BY $sort DESC";

    
    // Prepare the statement
    $stmtPosts = mysqli_prepare($conn, $sqlPosts);

    // Execute the statement
    mysqli_stmt_execute($stmtPosts);
    
    
    // Get the result set
    $resultPosts = mysqli_stmt_get_result($stmtPosts);
    
    echo '<h2>Community Posts</h2>';
    
    $amountOfPosts = mysqli_num_rows($resultPosts);
    // Fetch data from the result set
    if ($amountOfPosts <= 0) {
        echo 'No results with those filters.';
        echo '<br>';
    } else {
        //Just echo first three
        // Process each row 
        mysqli_data_seek($resultPosts, $start);
        $incrementer = 1;
        while ($rowPosts = mysqli_fetch_array($resultPosts)) {
            if ($incrementer > 3) {
                break; // Exit the loop if $incrementer is greater than 3
            }
            if($rowPosts['title'] == NULL){
                if ($incrementer == 1){
                    echo 'No results.';
                    echo '<br>';
                }
                break;
            }
            $mainTag;
            if($rowPosts['mainTag'] == 'T'){
                $mainTag = "Technical";
            }elseif($rowPosts['mainTag'] == 'NT'){
                $mainTag = "Non-Technical";
            }
            
            $sqlLikes = "SELECT * FROM likeTable WHERE postID = {$rowPosts['postID']} AND userID = $userID";
            $resultLikes = mysqli_query($conn,$sqlLikes);
            $likeColour = 'black';
            if (mysqli_num_rows($resultLikes)>0){
                $likeColour = '#00F078';
            }

            $sqlDislikes = "SELECT * FROM dislikeTable WHERE postID = {$rowPosts['postID']} AND userID = $userID";
            $resultdisLikes = mysqli_query($conn,$sqlDislikes);
            $dislikeColour = 'black';
            if (mysqli_num_rows($resultdisLikes)>0){
                $dislikeColour = 'red';
            }
            $likeTable = 'likeTable';
            echo '<div class="pop-up" id="Pop-up-post' . $incrementer . '">
                    <p>'. $rowPosts["date"] . '<img align="right" src="Images/close.png" onclick="closeForm()" ></p>
                    <h2>' . $rowPosts["title"] . ' <img align="right" src="Images/Profile.jpg"></h2> 
                    <p><b>Tags:</b> ' . $mainTag . ', ' . $rowPosts["subTags"] . '</p>
                    <p>' . $rowPosts['text'] . '</p>
                    <div id="postDetails" class="postDetails">
                        <div><p onClick="openComments()"><b>Comments (' . $rowPosts['commentCount'] . ')</b> </p> </div>
                        <div><p class="likeCount" style="color: ' . $likeColour . ';"><img src="Images/Like.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'likeTable\')">' . $rowPosts["likeCount"] . '</p></div>
                        <div><p class="likeCount" style="color: ' . $dislikeColour . ';"><img src="Images/dislike.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'dislikeTable\')">' . $rowPosts["dislikeCount"] . '</p></div>
                    </div>
                    <div id="Comments' . $incrementer . '" style="display: none;">';
                    if ($rowPosts['commentCount']>0){
                        $sqlComments = "SELECT authorID, text, postID FROM commentTable WHERE postID = {$rowPosts['postID']}";
                        $resultComments = mysqli_query($conn,$sqlComments);
                        while ($rowComments = mysqli_fetch_array($resultComments)){
                            $escapedCommentText = addslashes($rowComments['text']);
                            if ($userID == $rowComments['authorID']){
                                echo '<div class="comment" id="comment">
                                    <div id="profpic">
                                    <img src="Images/Profile.jpg" style="padding-top: 10px;"> 
                                    </div>
                                    <div id="commenttext"> 
                                    <h3> User' . $rowComments['authorID'] . '</h3>
                                    <p>' . $rowComments['text'] . ' </p>
                                    </div>
                                    <input type="submit" onclick="deleteComment(\'' . $rowComments['postID'] . '\', \'' . $userID . '\',\'' . $escapedCommentText . '\')" value="Delete">
                                  </div>';
                            }else{
                               echo '<div class="comment" id="comment">
                                    <div id="profpic">
                                    <img src="Images/Profile.jpg" style="padding-top: 10px;"> 
                                    </div>
                                    <div id="commenttext"> 
                                    <h3> User' . $rowComments['authorID'] . '</h3>
                                    <p>' . $escapedCommentText . ' </p>
                                    </div>
                                  </div>'; 
                            }
                            
                        }
                    }
                    echo '<div class="comment" id="addComment">
                            <div id="profpic">
                                <img src="Images/Profile.jpg" style="padding-top: 10px;"> 
                            </div>
                            <div id="writeComment" style="display: flex; align-items: center;">
                                <input type="text" id="commentText" placeholder="Add Comment...">
                                <input type="submit" onclick="addComment(\'' . $rowPosts['postID'] . '\', \'' . $userID . '\')" value="Add">
                            </div>
                        </div>';

            echo    '</div>
                </div>';
            $text = $rowPosts['text'];
            $textPreview = strlen($text) > 100 ? substr($text, 0, 100) . '...' : $text;
            echo '<div class="post" id="post' . $incrementer . '">
                <p>'. $rowPosts["date"] . '</p>
                <h2 onclick="openPost' . $incrementer . '()"><u>' . $rowPosts["title"] . '</u></h2> 
                <p><b>Tags:</b> ' . $mainTag . ', ' . $rowPosts["subTags"] . '</p>
                <p>' . $textPreview . '</p>
                <div id="postDetails" class="postDetails">
                <div><p><b>Comments (' . $rowPosts['commentCount'] . ')</b> </p> </div>
                <div><p class="likeCount" style="color: ' . $likeColour . ';"><img src="Images/Like.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'likeTable\')">' . $rowPosts["likeCount"] . '</p></div>
                <div><p class="likeCount" style="color: ' . $dislikeColour . ';"><img src="Images/dislike.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'dislikeTable\')">' . $rowPosts["dislikeCount"] . '</p></div>
                </div>
            </div>';
            $incrementer = $incrementer + 1;
            
        }

        if ($amountOfPosts < 4){
            echo '<p align="center"> <span id="backPage"><u> < </u></span><span id="currentPage">1</span>/<span id="totalPages">1</span><span id="forwardPage"><u> > </u></span></p>';
        }else{
            $amountOfPages = intval(($amountOfPosts + 2) / 3);
            $currentPage = intval($start/3 + 1);
            echo '<p align="center"> <span id="backPage"><u> < </u></span><span id="currentPage">' . $currentPage . '</span>/<span id="totalPages">' . $amountOfPages . '</span><span id="forwardPage"><u> > </u></span></p>';
        }


        
    }
    // Free the result set and close the statement
   // mysqli_stmt_close($stmt);
    ?>

