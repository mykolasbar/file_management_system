
<?php
    session_start();
    $_SESSION['showemployees'] = true;

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


    // <Navigation>

    echo "<div style = 'display:inline-flex'><form action='' method='POST' enctype='multipart/form-data'><button type='submit' name = 'employees'>Employees</button></form>
          <form action='' method='POST' enctype='multipart/form-data'><button type='submit' name = 'projects'>Projects</button></form></div></br>";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST['employees'])) {
            $_SESSION['showemployees'] = true;
        }
    }

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST['projects'])) {
            $_SESSION['showemployees'] = false;
        }
    }

    // </Navigation>

    // <Delete, rename>

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST["delete"])) {
            if (isset($_POST["deletion"])) {
                $delete = "DELETE FROM employees WHERE id=".$_POST["deletion"]."";
                $resulte = mysqli_query($conn, $delete);
                $resulte = mysqli_query($conn, $employees);
            }
        }
    }

    if($_SERVER["REQUEST_METHOD"] === "POST"){ 
        if (isset($_POST['updatebutton'])) {
            if (isset($_POST['updatestring'])) {
                if (isset($_POST['updateid'])){
                $update = "UPDATE projects SET projectname = '".$_REQUEST["updatestring"]."' WHERE projectID=".$_POST["updateid"]."";
                $resultp = mysqli_query($conn, $update);
                $resultp = mysqli_query($conn, $projects);
                $_SESSION['showemployees'] = false;
                }
            }
        }
    }

    // </Delete, rename>

    // <Add employee with assigned projects>

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST['submitemployee'])) {
            if (isset($_POST['addfirstname']) && isset($_POST['addlastname'])){ 
                    $insert = "INSERT INTO employees (firstname, lastname)
                               VALUES ('".$_POST['addfirstname']."', '".$_POST['addlastname']."')";
                    $resulte = mysqli_query($conn, $insert);
                    $resulte = mysqli_query($conn, $employees);

                    $nextincrementquery = "SHOW TABLE STATUS LIKE 'Employees'";
                    $nextincrementresult = mysqli_query($conn, $nextincrementquery);
                    $nextincrementdata = mysqli_fetch_assoc($nextincrementresult);
                    $nextincrement = $nextincrementdata['Auto_increment'] - 1;
                    
                    // echo $nextincrement;

                    if (!empty($_POST['checkitems'])){
                        foreach ($_POST['checkitems'] as $project) {
                            echo $project . '&nbsp';
                            
                            $assign = "INSERT INTO employees_projects
                            VALUES ($nextincrement, $project)";

                            $resultep = mysqli_query($conn, $assign);
                            $resultep = mysqli_query($conn, $employees_projects);
                }}
            }   
        }
    }

    // </Add employee with assigned project>

    // <Display employees table>

    if ($_SESSION['showemployees'] === true) {

        echo "Add new employee</br>";
        echo "<form action='' method='POST' enctype='multipart/form-data'><label for = 'addfirstname'>First name </label><input name = 'addfirstname' type = 'text'></input><label for = 'addlastname'> Last name </label><input name = 'addlastname' type = 'text'></input><label for = 'selectproject'> Assign project </label>";
            if (mysqli_num_rows($resultp) > 0) {
                while($row = mysqli_fetch_assoc($resultp)) {
                    echo "
                        <input type ='checkbox' name = 'checkitems[]' value = '" . $row["projectID"]. "'>".$row["projectname"]."";
                }
            } else {
                echo "No projects added";
            }
        echo "</input> <input name = 'submitemployee' type='submit' value='Submit'></form>";

        echo "<table style = 'border: 1px solid; margin:10px; border-collapse: collapse'>
                    <th>Employees</th>";

                if (mysqli_num_rows($resulte) > 0) {
                    while($row = mysqli_fetch_assoc($resulte)) {
                            echo "<tr>
                                    <td style = 'border:1px solid; border-collapse: collapse; padding:5px'>id: <b>" . $row["id"]. "</b></td>
                                    <td style = 'border:1px solid; padding:5px; width:250px'> First name: <b>" . $row["firstname"]. "</b></td> 
                                    <td style = 'border:1px solid; padding:5px; width:250px'>Last name: <b>" . $row["lastname"]. "</b></td>
                                    <td style = 'border:1px solid; padding:5px; width:250px'>Assigned projects:<br>";
                                    $showprojects = "SELECT id, projectname FROM employees_projects LEFT JOIN projects ON employees_projects.projectID = projects.projectID WHERE id = " . $row["id"]. "";
                                    $showprojectsresults = mysqli_query($conn, $showprojects);
                                    // $resultp = mysqli_query($conn, $projects);
                                    if (mysqli_num_rows($showprojectsresults) > 0) {
                                        while($projectsrow = mysqli_fetch_assoc($showprojectsresults)) {
                                            echo '<b>' . $projectsrow["projectname"] . '</b><br>';
                                        }}
                                    else echo "none";
                                    echo "</td>
                                    <td style = 'border:1px solid; padding:5px; width:100px'><form action='' method='POST' enctype='multipart/form-data'><input type='hidden' name='deletion' value=" . $row['id']. " /><button type='submit' name = 'delete'>Delete</button></form></td>
                                </tr>";
                        }
                } else {
                            // echo "0 results";
                        }
            echo "</table>";
    }

    // </Display employees table>

    // <Display projects table>

    if ($_SESSION['showemployees'] === false) {
            echo "<table style = 'border: 1px solid; margin:10px; border-collapse: collapse'>
                    <th>Projects</th>";

                if (mysqli_num_rows($resultp) > 0) {
                    while($row = mysqli_fetch_assoc($resultp)) {
                        echo "<tr>
                                <td style = 'border:1px solid; border-collapse: collapse; padding:5px'>Project ID: </b>" . $row["projectID"]. "</b></td>
                                <td style = 'border:1px solid; border-collapse: collapse; padding:5px; width:250px'>Project name: <b>" . $row["projectname"]. "</b></td>
                                <td style = 'border:1px solid; padding:5px; width:250px'><form action='' method='POST' enctype='multipart/form-data'><input type='hidden' name='updateid' value=" . $row['projectID']. " /><button type='submit' for='updatestring' name = 'updatebutton'>Update</button><input type = 'text' name='updatestring' /></form></td>
                            </tr>";
                    }
                } else {
                    echo "0 results";
                }

            echo "</table>"; 
        }
    
    // </Display projects table>

    mysqli_close($conn);

?>