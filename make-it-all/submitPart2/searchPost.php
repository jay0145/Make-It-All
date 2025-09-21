<?php
    // Retrieve filter values from the AJAX request
    $text = isset($_POST['text']) ? $_POST['text'] : 0; //Get the userID value
    $userID = isset($_POST['userID']) ? $_POST['userID'] : 0; //Get the userID value
    
    //Separate database details, use include to get them
    include "config.php";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }
    
    // Prepare the SQL statement with placeholders
    $sqlPosts = "SELECT p.*, 
                    GROUP_CONCAT(st.subTag) AS subTags, 
                    COALESCE(c.commentCount, 0) AS commentCount,
                    COALESCE(l.likeCount, 0) AS likeCount,
                    COALESCE(d.dislikeCount, 0) AS dislikeCount
                FROM postTable p 
                JOIN subTagTable st ON p.postID = st.postID 
                LEFT JOIN (SELECT postID, COUNT(*) AS commentCount FROM commentTable GROUP BY postID) c 
                    ON p.postID = c.postID 
                LEFT JOIN (SELECT postID, COUNT(*) AS likeCount FROM likeTable GROUP BY postID) l
                    ON p.postID = l.postID
                LEFT JOIN (SELECT postID, COUNT(*) AS dislikeCount FROM dislikeTable GROUP BY postID) d
                    ON p.postID = d.postID
                WHERE p.title LIKE ? AND p.posted = 1
                GROUP BY p.postID;";

    
    // Prepare the statement
    $stmtPosts = mysqli_prepare($conn, $sqlPosts);

    // Bind the parameter to the prepared statement
    $searchText = '%' . $text . '%';
    mysqli_stmt_bind_param($stmtPosts, "s", $searchText);
    // Execute the statement
    mysqli_stmt_execute($stmtPosts);
    
    
    // Get the result set
    $resultPosts = mysqli_stmt_get_result($stmtPosts);
    
    echo '<h2>Community Posts</h2>';
    
    $amountOfPosts = mysqli_num_rows($resultPosts);
    // Fetch data from the result set
    if ($amountOfPosts <= 0) {
        echo 'No results.';
        echo '<br>';
    } else {
        //Just echo first three
        $incrementer = 1;
        mysqli_data_seek($resultPosts, 0);
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

            $sqlSaved = "SELECT * FROM savedPostTable WHERE postID = {$rowPosts['postID']} AND employeeID = $userID";
            $resultSaved = mysqli_query($conn,$sqlSaved);
            $savedImg = 'Images/notBookmarked.jpg';
            if (mysqli_num_rows($resultSaved)>0){
                $savedImg = 'Images/bookmarked.jpg';
            }

            $sqlAuthorName = "SELECT employeeFirstName, employeeSurname FROM `employeeTable` WHERE employeeID = {$rowPosts['employeeID']};";
            $resultAuthorName = mysqli_query($conn,$sqlAuthorName);
            if ($resultAuthorName) {
                $rowAuthor = mysqli_fetch_assoc($resultAuthorName);
                // $row now contains the associative array representing the fetched row
            
                // Accessing column values:
                $authFirstName = $rowAuthor['employeeFirstName'];
                $authSurname = $rowAuthor['employeeSurname'];
            
                // Now you can use $columnValue or other values from $row as needed
            
                mysqli_free_result($resultAuthorName); // Free the result set
            } else {
                // Handle query error
                $authFirstName = "No name found.";
                $authSurname = "";
            }
            
            echo '<div class="pop-up" id="Pop-up-post' . $incrementer . '">
                    <p>'. $rowPosts["date"] . '<img align="right" src="Images/close.png" class="clickable" onclick="closeForm()" ></p>
                    <h2>' . $rowPosts["title"] . '</h2><p align="right" > Written By: ' . $authFirstName . ' ' . $authSurname . '</p> 
                    <p><b>Tags:</b> ' . $mainTag . ', ' . $rowPosts["subTags"] . '</p>
                    <p>' . $rowPosts['text'] . '</p>
                    <div id="postDetails" class="postDetails">
                        <div><p class="clickable" onClick="openComments()"><b>Comments (' . $rowPosts['commentCount'] . ')</b> </p> </div>
                        <div><p class="likeCount" style="color: ' . $likeColour . ';"><img src="Images/Like.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'likeTable\')">' . $rowPosts["likeCount"] . '</p></div>
                        <div><p class="likeCount" style="color: ' . $dislikeColour . ';"><img src="Images/dislike.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'dislikeTable\')">' . $rowPosts["dislikeCount"] . '</p></div>
                        <div><p class="likeCount"><img src="' . $savedImg . '" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'savedPostTable\')"> </p></div>
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
                                    <input class="clickable" type="submit" onclick="deleteComment(\'' . $rowComments['postID'] . '\', \'' . $userID . '\',\'' . $escapedCommentText . '\')" value="Delete">
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
                                <input type="text" id="commentText' . $incrementer . '" placeholder="Add Comment...">
                                <input class="clickable" type="submit" onclick="addComment(\'' . $rowPosts['postID'] . '\', \'' . $userID . '\',' . $incrementer . ')" value="Add">
                            </div>
                        </div>';

            echo    '</div>
                </div>';
            $text = $rowPosts['text'];
            $textPreview = strlen($text) > 100 ? substr($text, 0, 100) . '...' : $text;
            echo '<div class="post" id="post' . $incrementer . '">
                <p>'. $rowPosts["date"] . '</p>
                <h2 class="clickable" onclick="openPost' . $incrementer . '()"><u>' . $rowPosts["title"] . '</u></h2> 
                <p><b>Tags:</b> ' . $mainTag . ', ' . $rowPosts["subTags"] . '</p>
                <p>' . $textPreview . '</p>
                <div id="postDetails" class="postDetails">
                <div><p><b>Comments (' . $rowPosts['commentCount'] . ')</b> </p> </div>
                <div><p class="likeCount" style="color: ' . $likeColour . ';"><img src="Images/Like.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'likeTable\')">' . $rowPosts["likeCount"] . '</p></div>
                <div><p class="likeCount" style="color: ' . $dislikeColour . ';"><img src="Images/dislike.png" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'dislikeTable\')">' . $rowPosts["dislikeCount"] . '</p></div>
                <div><p class="likeCount"><img src="' . $savedImg . '" onclick = "interactPost(\'' . $userID . '\', \''. $rowPosts['postID'] . '\', \'savedPostTable\')"> </p></div>
                </div>
            </div>';
            $incrementer = $incrementer + 1;
            
        }     
        
    }
    
    // Free the result set and close the statement
    //mysqli_stmt_close($stmtPosts);
    ?>