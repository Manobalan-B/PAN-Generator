<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/PHPMailer/src/Exception.php';
require './PHPMailer/PHPMailer/src/PHPMailer.php';
require './PHPMailer/PHPMailer/src/SMTP.php';

session_start();
function sendmail($name, $email, $send, $sub, $body){ 
    $mailsender = $send;
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'manobalan003@gmail.com';                    
    $mail->Password   = 'usgn pqsw zuep ouqv';  
    $mail->Port       = 465;
    $mail->SMTPSecure = 'ssl';            
    $mail->isHTML(true); 
    $mail->setFrom($mail->Username, $mailsender); 
    $mail->addAddress($email);                                   
    $mail->Subject = $sub;
    $mail->Body    = $body;
    $mail->send();
}

function pangen(){

}

$name = $lname = $type = $email = $aadhaar = $phone = $otp = $message = $errmsg = "";
$val = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['otpgen'])) {
        $name = $_POST["uname"];
        $lname = $_POST["lname"];
	    $type = $_POST["type"];
        $email = $_POST["email"];
        $aadhaar = $_POST["aadhaar"];
        $phone = $_POST["phone"];
        $val = random_int(100000, 999999);
        $loop = rand(1, 9);
        for ($i = 1; $i <= $loop; $i++) {
            $val = str_shuffle($val);
        }
        $_SESSION['otp'] = $val;
        $send = "PAN Verification";
        $sub = "OTP for PAN Validation";
        $body = "Hi! ".$name." Your OTP for PAN Card Verification is ".$val." Do not share it with anyone";
        sendmail($name, $email, $send, $sub ,$body);
        $message = "*OTP has been successfully generated to the specified email";
    }

    if (isset($_POST['pangen'])) {
        $name = $_POST["uname"];
        $lname = $_POST["lname"];
	    $type = $_POST["type"];
        $email = $_POST["email"];
        $aadhaar = $_POST["aadhaar"];
        $phone = $_POST["phone"];
	    $otp = $_POST["otp"];

        if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname  = "pancard_db";

            $conn = new mysqli($servername,$username,$password,$dbname);
            if($conn->connect_error){
                die("Connection Failed:".$conn->connect_error);
            }
            $letters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            do{
                $firstpart = $spart = $tpart = $fpart = '';
                $lpart =0;
                $ind=1;
                
                for($i=0;$i<3;$i++){
                    $temp = rand(10,25);
                    $lpart = $lpart+($ind*$temp); 
                    $ind++;
                    $firstpart .= $letters[$temp];
                }
                
                $lpart=$lpart+($ind*strpos($letters,$type));
                $ind++;
                $spart = $type;
                
                $lpart=$lpart+($ind*strpos($letters,substr($lname,0,1)));
                $ind++;
                $tpart = substr($lname,0,1);

                $fpartsub = rand(1,9999);
                if($fpartsub<=9){
                    $fpart = "000".strval($fpartsub);
                }
                else if($fpartsub>=10 && $fpartsub<=99){
                    $fpart = "00".strval($fpartsub);
                }
                else if($fpartsub>=100 && $fpartsub<=999){
                    $fpart = "0".strval($fpartsub);
                }
                else{
                    $fpart = strval($fpartsub);
                }
                for($i=0;$i<4;$i++){
                    $lpart = $lpart+($ind*strpos($letters,$fpart[$i]));
                    $ind++;
                }

                $finpan = $firstpart.$spart.$tpart.$fpart.$letters[$lpart%36];
                $query = $conn->prepare("select count(*) from pan_details where pan_num=?");
                $query->bind_param("s",$finpan);
                $query->execute();
                $query->bind_result($count);
                $query->fetch();
                $query->close();
            }while($count>0);

            $ins = $conn->prepare("insert into pan_details(f_name,l_name,type,aadhaar_num,phone,email,pan_num) values(?,?,?,?,?,?,?)");
            $ins->bind_param("sssiiss",$name,$lname,$type,$aadhaar,$phone,$email,$finpan);
            $ins->execute();
            $ins->close();
            $conn->close();
            $send = "PAN Number Issued";
            $sub = "Get Your PAN Number";
            $body = "Hi! ".$name." Your Details has been Verified and PAN number is isseued. \n\n\n Your PAN Card Number is ".$finpan;
            sendmail($name, $email, $send, $sub ,$body);
            header("Location:success.php");
            exit;
        } 

        else {
            $errmsg="OTP is incorrect. Please, ensure the email id then resend and verify the OTP";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: peachpuff;
        }
        #heading {
            text-align: center;
            color: rgb(255, 82, 2);
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-weight: 600;
        }
        label {
            color: rgb(255, 82, 2);
            font-size: large;
            font-weight: bolder;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
        }
        label:hover {
            font-size: larger;
        }
        .form-text {
            color: rgb(255, 51, 0);
            font-weight: 550;                    
        }
        .container {
            background-color: rgb(245, 184, 130);
            box-shadow: 5px 5px rgb(252, 143, 47);
            border-radius: 15px;
        }
        .btn {
            background-color: rgb(252, 143, 47);
            color: black;
            border-color: grey;
        }
        .btn:hover {
            background-color: rgb(255, 82, 2);
            color: white;
            border-color: grey;
        }
    </style>
    <title>PAN Generation</title>
</head>
<body>
    <header>
        <h1 id="heading">PAN Number Generator</h1>
    </header>
    <br><br>
    <div class="container">
        <form name="" action="" method="post">
            <br>
            <div class="form-group">
                <label for="name" class="form-label">First Name</label>
                <input type="text" class="form-control" placeholder="Enter Your Full Name" name="uname" value="<?php echo htmlspecialchars($name); ?>" required>
                <div id="namehelp" class="form-text">*As Specified in Aadhaar.</div>
            </div><br>
            
            <div class="form-group">
                <label for="name" class="form-label">Last Name</label>
                <input type="text" class="form-control" placeholder="Enter Your Last Name" name="lname" value="<?php echo htmlspecialchars($lname); ?>" required>
            </div><br>

            <label for="type" class="form-label">PAN Applying for</label>
            <select class="form-select" aria-label="Default select example" name="type" required>
                <option>Select</option>
                <option value="A" <?php if($type=="A") echo'selected';?>>Association of Persons (AOP)</option>
                <option value="B" <?php if($type=="B") echo'selected';?>>Body of Individuals (BOI)</option>
                <option value="C" <?php if($type=="C") echo'selected';?>>Company</option>
                <option value="F" <?php if($type=="F") echo'selected';?>>Firm</option>
                <option value="G" <?php if($type=="G") echo'selected';?>>Government</option>
                <option value="H" <?php if($type=="H") echo'selected';?>>HUF (Hindu Undivided Family)</option>
                <option value="L" <?php if($type=="L") echo'selected';?>>Local Authority</option>
                <option value="J" <?php if($type=="J") echo'selected';?>>Artificial Judicial Person</option>
                <option value="P" <?php if($type=="P") echo'selected';?>>Individual</option>
                <option value="T" <?php if($type=="T") echo'selected';?>>Trust</option>
            </select><br>

            <div class="form-group">
                <label for="aadhaar" class="form-label">Aadhaar Number</label>
                <input type="number" class="form-control" placeholder="Enter Your Aadhaar Number" name="aadhaar" value="<?php echo htmlspecialchars($aadhaar); ?>" required>
            </div><br>

            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="number" class="form-control" placeholder="Enter Your Phone Number" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                <div id="numberhelp" class="form-text">*Linked with Your Aadhaar.</div>
            </div><br>

            <div class="form-group">
                <label for="phone" class="form-label">Email Address</label>
                <input type="email" class="form-control" placeholder="Enter Your Email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <br>
                <div class="input-group-append">
                    <button type="submit" class="btn" name="otpgen">Generate OTP</button>
                </div>
                <div id="message" class="form-text"><?php echo htmlspecialchars($message);?></div>
            </div><br>

            <div class="form-group">
                <input type="number" class="form-control" placeholder="Enter OTP" name="otp">
                <br>
            </div>

            <center><div id="error" class="form-text"><?php echo htmlspecialchars($errmsg);?></div></center><br>
            <center><button type="submit" class="btn" name="pangen">Generate PAN</button></center><br>
        </form>
    </div>
    <br><br>
</body>
</html>
