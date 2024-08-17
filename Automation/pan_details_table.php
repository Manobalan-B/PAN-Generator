<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        $servername = "localhost";
        $uname = "root";
        $pwd = "";
        $dbname = "pancard_db";

        $conn = new mysqli($servername,$uname,$pwd,$dbname);
        $sql = "create table pan_details(
                    ref_id int(2) unsigned auto_increment primary key,
                    f_name varchar(20),
		    l_name varchar(20),
		    type varchar(35),
                    aadhaar_num int(12),
                    phone int(10),
		    email varchar(35),
                    pan_num int(10)
                )";

        if($conn->query($sql)===true){
            echo "Table Created Successfully";
        }
        else{
            echo "There is some issue in Table Creation";
            
        }
    ?>
</body>
</html>