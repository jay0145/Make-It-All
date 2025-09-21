<?php 
    //session_start();

    $sort = $_GET['sortBy']; 
    /*
    $sort can be one of the following:
    Recently Added (default) - Queue-like order of tasks added
    Most Recent - Earliest date task set for
    Most Important - Descending priority 
    Least Important - Ascending priority
    */

    //Filter results by priority
    $lowBox = $_GET['lowPriority'];
    $highBox = $_GET['highPriority'];
    $veryHighBox = $_GET['veryHighPriority'];
    
    //security concern - put in separate file later
    $servername = "localhost";
    $username = "team018";
    $password = "nkAfiVuTsC4Yw9LvLEgP";
    $dbname = "team018";

    //Default query - outputs all tasks in order of Recently Added 
    $query_str = "SELECT employeeID, toDoID, heading, description, priority, deadlineTime, deadlineDate, status
    FROM `toDoList` WHERE employeeID = $employeeID ORDER BY toDoID DESC";

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn){
    die("Connection failed:" . mysqli_connect_error());
    }

    //gets employeeID from the logged in user's session
    $query0 = "SELECT employeeID FROM employeeTable WHERE employeeEmail = '{$_SESSION["username"]}'";
    $employeeResult = mysqli_query($conn, $query0);
    $_SESSION['employeeID'] = $row['employeeID'];
    $employeeID = $_SESSION['employeeID'];

    //sorting statements for the tasks - changes query based on chosen sorting/filters
    if ($sort=="recentAdded"){
        $query_str = "SELECT employeeID, toDoID, heading, description, priority, deadlineTime, deadlineDate, status
        FROM `toDoList` WHERE employeeID = $employeeID " . addFilters($lowBox, $highBox, $veryHighBox) . "ORDER BY toDoID DESC";

    } else if ($sort=="mostRecent"){
        $query_str = "SELECT employeeID, toDoID, heading, description, priority, deadlineTime, deadlineDate, status
        FROM `toDoList` WHERE employeeID = $employeeID " . addFilters($lowBox, $highBox, $veryHighBox) . "ORDER BY deadlineDate, deadlineTime; ";

    } else if ($sort=="mostImportant") {
        $query_str = "SELECT employeeID, toDoID, heading, description, priority, deadlineTime, deadlineDate, status
        FROM `toDoList` WHERE employeeID= $employeeID " . addFilters($lowBox, $highBox, $veryHighBox) . "ORDER BY 
        CASE priority 
            WHEN 'veryhigh' THEN 1
            WHEN 'high' THEN 2
            WHEN 'low' THEN 3
        END;";
    } else if ($sort="leastImportant") {
        $query_str = "SELECT employeeID, toDoID, heading, description, priority, deadlineTime, deadlineDate, status
        FROM `toDoList` WHERE employeeID = $employeeID " . addFilters($lowBox, $highBox, $veryHighBox) . "ORDER BY 
        CASE priority 
            WHEN 'veryhigh' THEN 1
            WHEN 'high' THEN 2
            WHEN 'low' THEN 3
        END DESC;";
    }
    //end of sorting statements

    //filtering statements for the tasks - ! - used in the sorting statements
    function addFilters($lowBox, $highBox, $veryHighBox){
        $filterStatement = ""; // will be concatentated with the query string if any of the checkboxes are checked
        if ($lowBox || $highBox || $veryHighBox) {
            $filterStatement = "AND (";

            //Check each checkbox individually and construct the filter statement
            if ($lowBox) {
                $filterStatement .= " priority = 'low' OR ";
            }
            if ($highBox) {
                $filterStatement .= " priority = 'high' OR ";
            }
            if ($veryHighBox) {
                $filterStatement .= " priority = 'veryhigh' OR ";
            }
    
            //Get rid of the trailing "OR" and close the brackets
            $filterStatement = rtrim($filterStatement, " OR ");
            $filterStatement .= ") ";
        }
        
        return $filterStatement;
    }
    //end of filtering statements
    
    //query database and receive results
    $result = mysqli_query($conn, $query_str);
    $resultArray = array();
    if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_array($result)){
        $resultArray[] = $row;
    }

    //Sub-query to get all subtasks dependent on a task
    function getSubTasks($conn, $toDoId){
        $query_str2 = "SELECT `subtask`.subTaskID, `subtask`.description, `subtask`.status
        FROM `subtask` 
        INNER JOIN `toDoList`
        ON `toDoList`.toDoID = `subtask`.toDoID
        WHERE `toDoList`.toDoID = $toDoId";

        $subTaskResult = mysqli_query($conn, $query_str2);
        $subTaskResultArray= array();
        if (mysqli_num_rows($subTaskResult)>0){
            while ($subTask = mysqli_fetch_array($subTaskResult)){
                $subTaskResultArray[] = $subTask;
            }
        }
        return printSubTasks($subTaskResultArray);
    }

    //HTML string construction for subtasks - returns echo-ready string
    function printSubTasks($subTaskResultArray){
        $str = "";
        foreach ($subTaskResultArray as $subTask){
            if ($subTask['status']==0){
                $str = $str . "<li value=\"".$subTask['subTaskID']."\"><input type=\"checkbox\" ><label>".$subTask['description']."</label></li>";
            } else{
                //'value' is used to update ticked status on database 
                $str = $str . "<li class='ticked' value=\"".$subTask['subTaskID']."\"><input type=\"checkbox\" checked><label>".$subTask['description']."</label></li>";
            }
            
        }
        return $str;
    }

    }
    
    //HTML contruction for every task
    foreach ($resultArray as $row){
        $taskStatus;
        //used to classify complete/incomplete tasks in HTML - hide incomplete tasks
        if ($row['status']==1){ 
            $taskStatus = "complete";
        } else {
            $taskStatus = "incomplete";
        }
        echo "
        <li id=\"".$row["toDoID"]."\" class=\"".$row['priority']." ". $taskStatus ."\">
            <!--entire task encompassed in this-->
            <div class=\"grid-container\"> 
            <!-- Title -->
            <div class = \"title-container\">
                <button type=\"button\" class=\"title\">
                    <div>".$row["heading"]."</div>
                </button>
            </div>
            <!-- Content -->
            <div class=\"content-container\"> 
                <div class = \"description\">
                    Description
                    <p>".$row["description"]."</p>
                </div>
                <div class=\"subtask\">
                    Sub-tasks
                    <ul>". getSubTasks($conn, $row['toDoID']) ."
                    </ul>
                </div>
                <div class = \"deadline\">
                    Deadline
                    <p>".$row["deadlineDate"]."</p>
                    <p>".$row["deadlineTime"]."</p>
                </div>
                    <button type=\"button\" onclick=\"completeTask()\" class=\"markComplete\">Mark as Complete</button>
                    <button type=\"button\" onclick=\"deleteTask()\" class=\"edit\">Delete</button>
                    
                </div> 
            </div>
            <br>  
        </li>
        
        ";
    }

    mysqli_close($conn);
?>