<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <script type='text/javascript' src='jsitems.js' defer></script>
    <title>Document</title>
</head>
<body class="bg-info text-white">
    
<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $staffdb = "staff";

    error_reporting(E_ALL ^ E_WARNING);
    $conn = mysqli_connect($servername, $username, $password, $staffdb);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $employees = "SELECT id, firstname, lastname FROM employees";
    $resulte = mysqli_query($conn, $employees);

    $projects = "SELECT projectID, projectname FROM projects";
    $resultp = mysqli_query($conn, $projects);

    $employees_projects = "SELECT id, projectID FROM employees_projects";
    $resultep = mysqli_query($conn, $employees_projects);

    // <Add employee with assigned projects>

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST['submitemployee'])) {
            if (isset($_POST['addfirstname']) && isset($_POST['addlastname'])){ 
                    $insert = $conn->prepare("INSERT INTO employees (firstname, lastname)
                               VALUES (?, ?)");
                    $insert->bind_param("ss", $_POST['addfirstname'], $_POST['addlastname']);
                    $insert->execute();
                    $insert->close();

                    $nextincrementquery = "SHOW TABLE STATUS LIKE 'Employees'";
                    $nextincrementresult = mysqli_query($conn, $nextincrementquery);
                    $nextincrementdata = mysqli_fetch_assoc($nextincrementresult);
                    $nextincrement = $nextincrementdata['Auto_increment'] - 1;
                    
                    header('Location: ' . $_SERVER['PHP_SELF']);
    
                    if (!empty($_POST['checkitems'])){
                        foreach ($_POST['checkitems'] as $project) {
                            echo $project . '&nbsp';
                                
                            $assign = "INSERT INTO employees_projects
                                       VALUES ($nextincrement, $project)";
    
                            $resultep = mysqli_query($conn, $assign);
                            $resultep = mysqli_query($conn, $employees_projects);
                        
                            header('Location: ' . $_SERVER['PHP_SELF']);
                    }}
                }   
            }
        }
    
    // </Add employee with assigned project>

    // <Update employee>

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST['updateemployee'])) {
            if (isset($_POST['employeeid'])) {
                if (isset($_POST['updatefirstname']) && isset($_POST['updatelastname'])){
                    $update = $conn->prepare("UPDATE employees SET firstname = ?, lastname = ? WHERE id = ?;");
                    $update->bind_param("ssi", $_POST['updatefirstname'], $_POST['updatelastname'], $_POST['employeeid']);
                    $update->execute();
                    $update->close();
                    
                    header('Location: ' . $_SERVER['PHP_SELF']);
    
                    if (!empty($_POST['updatecheckitems'])){
                        foreach ($_POST['updatecheckitems'] as $updateproject) {
                                
                            // $remove = "UPDATE employees_projects SET projectID = NULL WHERE id =".$_POST['employeeid']."";
                            // $resultep = mysqli_query($conn, $remove);

                            $readd  = "INSERT INTO employees_projects
                                       VALUES (".$_POST['employeeid'].", $updateproject)";

                            // $removenulls = "DELETE FROM employees_projects WHERE projectID IN (SELECT ".$_POST['employeeid']." = NULL)";
    
                            $resultep = mysqli_query($conn, $readd);
                            $resultep = mysqli_query($conn, $employees_projects);
                        
                        }
                    }
                    else if (empty($_POST['updatecheckitems'])) {
                                $emptyprojects = "DELETE FROM employees_projects WHERE id = ".$_POST['employeeid']."";
                                $resultep = mysqli_query($conn, $emptyprojects);
                                $resultep = mysqli_query($conn, $employees_projects);
                    }
                } 
            }
            }
        }        

    // </Update employee>

    // <Employee delete>

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST["delete"])) {
            if (isset($_POST["deletion"])) {
                $delete = "DELETE FROM employees WHERE id=".$_POST["deletion"]."";
                $resulte = mysqli_query($conn, $delete);
                $resulte = mysqli_query($conn, $employees);
            }
        }
    }

    // </Employee delete>

    // <Display employees table>

    echo "<div class='btn-group m-2 d-flex justify-content-center' role='group'><form action='' method='POST' enctype='multipart/form-data'><button type='submit' name = 'employees' style='background-color:rgb(255, 127, 80); border:1px solid; padding:10px; width:120px; border-radius:5px'>Employees</button></form>
    <form action='projects.php' method='POST' enctype='multipart/form-data'><button type='submit' name = 'projects' style='background-color:rgb(255, 127, 80); border:1px solid; padding:10px; width:120px; border-radius:5px'>Projects</button></form></div>";

    echo "<div class='d-flex justify-content-center m-3'><b>Add new employee</b></div>
        <div class='d-flex justify-content-center m-3'><form action='' method='POST' enctype='multipart/form-data'><label for = 'addfirstname' class='m-2'>First name </label><input name = 'addfirstname' type = 'text'></input><label for = 'addlastname' class='m-2'> Last name </label><input name = 'addlastname' type = 'text'></input><label for = 'selectproject' class='m-2'> Assign projects: </label>";
        if (mysqli_num_rows($resultp) > 0) {
            while($row = mysqli_fetch_assoc($resultp)) {
                echo "<input type ='checkbox' name = 'checkitems[]' value = '" . $row["projectID"]. "'>".$row["projectname"]."";
            }
        } else {
            echo "No projects added";
        }
    echo "</input> <input name = 'submitemployee' type='submit' value='Submit' class='btn btn-danger m-2'></form></div>";

    echo "<div class='d-flex justify-content-center mw-75' >
    <div style = 'background-color:rgb(25,25,112); opacity: 0.9; color: rgba(240,230,140); border:1px solid; width:75%'>
    <table class = 'w-100' style = 'background-color:rgb(25,25,112)'>
                <th class = 'm-5 p-3'>Employees</th>";

        if (mysqli_num_rows($resulte) > 0) {
            while($row = mysqli_fetch_assoc($resulte)) {
                echo "<tr class = 'm-5 p-3 w-75'>
                        <td class = 'p-3 '>id: <b>" . $row["id"]. "</b></td>
                        <td class = 'p-3'> First name: <b>" . $row["firstname"]. "</b></td> 
                        <td class = 'p-3'>Last name: <b>" . $row["lastname"]. "</b></td>
                        <td class = 'p-3'>Assigned projects:<br>";
                        $showprojects = "SELECT id, projectname FROM employees_projects LEFT JOIN projects ON employees_projects.projectID = projects.projectID WHERE id = " . $row["id"]. "";
                        $showprojectsresults = mysqli_query($conn, $showprojects);
                        if (mysqli_num_rows($showprojectsresults) > 0) {
                            while($projectsrow = mysqli_fetch_assoc($showprojectsresults)) {
                                echo '<b>' . $projectsrow["projectname"] . '</b><br>';
                                }
                            }
                        else echo "none";
                            echo "</td>
                            <td><button class='btn btn-warning' onclick='updateemployees(event)'>Update</button></td>
                            <td style = 'padding:5px; width:100px'><form action='' method='POST' enctype='multipart/form-data'><input type='hidden' name='deletion' value=" . $row['id']. " /><button type='submit' name = 'delete' class='btn btn-warning'>Delete</button></form></td>
                    </tr>
                    <tr style = 'background-color:rgb(25,25,150); display:none'>
                        <td class = 'm-5 p-3' colspan = '6'><form action='' method='POST' enctype='multipart/form-data'><label for = 'employeeid' class='m-2'>" . $row['id']. " </label><input type='hidden' name='employeeid' value=" . $row['id']. " /><label for = 'updatefirstname' class='m-2'>First name </label><input name = 'updatefirstname' type = 'text'></input><label for = 'updatelastname' class='m-2'> Last name </label><input name = 'updatelastname' type = 'text'></input><label for = 'updatecheckitems[]' class='m-2'> Assign projects: </label>";
                            $projectstoupdate = "SELECT projectID, projectname FROM projects";
                            $resultptu = mysqli_query($conn, $projectstoupdate);
                            if (mysqli_num_rows($resultp) > 0) {
                                while($prrow = mysqli_fetch_assoc($resultptu)) {
                                    echo "<input type ='checkbox' name = 'updatecheckitems[]' value = '" . $prrow["projectID"]. "'>".$prrow["projectname"]."";

                                    }
                            } else {
                                echo "No projects added";
                            }
                            echo "</input> <button name = 'updateemployee' type='submit' class='btn btn-danger m-2'>Update</button></form>
                        </td>
                    </tr>";
        }
        } else {
            echo "0 results";
                        }
            echo "</table></div></div>";


    mysqli_close($conn);

?>
</body>
</html>