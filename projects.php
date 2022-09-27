<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <title>Document</title>
    <script type='text/javascript' src='jsitems.js' defer></script>
</head>
<body class="bg-info text-white">
<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $staffdb = "staff";

    error_reporting(E_ALL ^ E_WARNING);
    $conn = mysqli_connect($servername, $username, $password, $staffdb);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $projects = "SELECT projectID, projectname FROM projects";
    $resultp = mysqli_query($conn, $projects);

    $employees_projects = "SELECT id, projectID FROM employees_projects";
    $resultep = mysqli_query($conn, $employees_projects);

    // <Project update>

    if($_SERVER["REQUEST_METHOD"] === "POST"){ 
        if (isset($_POST['updatebutton'])) {
            if (isset($_POST['updatestring'])) {
                if (isset($_POST['updateid'])){
                $update = $conn->prepare("UPDATE projects SET projectname = '".$_REQUEST["updatestring"]."' WHERE projectID=?");
                $update->bind_param("i", $_POST["updateid"]);
                $update->execute();
                header('Location: ' . $_SERVER['PHP_SELF']);

                }
            }
        }
    }

    // </Project update>

    // <Add new project>

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["submitproject"])) {
            if (isset($_POST["projectname"])) {
                $insertproject = $conn->prepare("INSERT INTO projects (projectname)
                                  VALUES (?)");
                $insertproject->bind_param("s", $_POST["projectname"]);
                $insertproject->execute();

                header('Location: ' . $_SERVER['PHP_SELF']);
            }
        }
    }

    // </Add new project>

    // <Delete project>

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST["projectdelete"])) {
            if (isset($_POST["projectdeletion"])) {
                $deleteproject = "DELETE FROM projects WHERE projectID=".$_POST["projectdeletion"]."";
                $resultp = mysqli_query($conn, $deleteproject);
                $resultp = mysqli_query($conn, $projects);
            }
        }
    }

    // </Delete project>

    // <Print table>

    echo "<div class='btn-group m-2 d-flex justify-content-center' role='group'><form action='index.php' method='POST' enctype='multipart/form-data'><button type='submit' name = 'employees' style='background-color:rgb(255, 127, 80); border:1px solid; padding:10px; width:120px; border-radius:5px'>Employees</button></form>
    <form action='' method='POST' enctype='multipart/form-data'><button type='submit' name = 'projects' style='background-color:rgb(255, 127, 80); border:1px solid; padding:10px; width:120px; border-radius:5px'>Projects</button></form></div>";

    echo "<div class='d-flex justify-content-center m-2'><form action='' method='POST' enctype='multipart/form-data'><label for = 'addproject' class='m-2'><b>Add new project</b> </label><input name = 'projectname' type = 'text'></input><input name = 'submitproject' type='submit' value='Submit' class='btn btn-danger m-2'></form></div>
      
    <div class='d-flex justify-content-center' >
    <div class = 'mw-75' style = 'background-color:rgb(25,25,112); opacity: 0.9; color: rgba(240,230,140); border:1px solid'>
    <table class = 'w-100' style = 'margin:10px; border-collapse: collapse'><th class='p-2'>Projects</th>";

    if (mysqli_num_rows($resultp) > 0) {
        while($row = mysqli_fetch_assoc($resultp)) {
            echo "<tr>
                    <td style = 'padding:10px'>Project ID: </b>" . $row["projectID"]. "</b></td>
                    <td style = 'padding:10px; width:250px'>Project name: <b>" . $row["projectname"]. "</b></td>
                    <td style = 'padding:10px; width:320px'><form action='' method='POST' enctype='multipart/form-data'><input type='hidden' name='updateid' value=" . $row['projectID']. " /><button id='renameaction' class='btn btn-warning' onclick='displayaction(event)'>Rename</button><div style = 'display:none'><input type = 'text' name='updatestring' /><button type='submit' for='updatestring' name = 'updatebutton' class='btn btn-warning m-2'>Submit</button></div></form></td>
                    <td style = 'padding:10px; width:100px'><form action='' method='POST' enctype='multipart/form-data'><input type='hidden' name='projectdeletion' value=" . $row['projectID']. " /><button type='submit' name = 'projectdelete' class='btn btn-warning'>Delete</button></form></td>
                </tr>";
                    }
                } else {
                    echo "0 results";
                }

    echo "</table></div></div>";

    // </Print table>

    mysqli_close($conn);
    ?>