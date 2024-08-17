<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Intern Database</title>
</head>
<body>
<?php
    $servername = "localhost";
    $uname = "root";
    
    $conn = new mysqli($servername,$uname);
    $sql = "create database pancard_db";

    if($conn->query($sql)===true){
        echo "Database Created Successfully";
    }
    else{
        echo "There is some issue in creating Database";
                
    }
    ?>
</body>
</html>