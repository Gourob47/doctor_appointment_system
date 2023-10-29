<?php
session_start();
$id = $_SESSION['id'];
if (!isset($_SESSION["role"])) {
    header("Location: ../login.php");
}
else if($_SESSION['role']==1)
{
  header("Location: ../login.php");
}


include_once('../database/database.php');



$sqlPat = "SELECT * FROM patient WHERE id='$id'";
$resultPat = mysqli_query($conn, $sqlPat);
$pat = mysqli_fetch_array($resultPat);


$docId = $_GET['id'];
$sqlDoc = "SELECT * FROM doctor WHERE id='$docId'";
$resultDoc = mysqli_query($conn, $sqlDoc);
$doc = mysqli_fetch_array($resultDoc, MYSQLI_ASSOC);



date_default_timezone_set('Asia/Dhaka');
$schedule = array("10AM-11AM", "11AM-12PM", "12PM-1PM", "1PM-2PM", "3PM-4PM", "4PM-5PM", "6PM-7PM", "8PM-9PM");
$temp_name=$temp_email=$temp_phone=$temp_msg='';

$value = $value1 = $value2 = '';
if (isset($_POST['submit'])) {


    $errors = array('name' => '', 'email' => '', 'phone' => '', 'date' => '', 'slot' => '');

    $_SESSION['temp_name']=$_POST['name'];
    $_SESSION['temp_email']=$_POST['email'];
    $_SESSION['temp_phone']=$_POST['phone'];
    $_SESSION['temp_msg']=$_POST['msg'];
   
     $temp_name=$_SESSION['temp_name'];
     $temp_email =  $_SESSION['temp_email'];
     $temp_phone=  $_SESSION['temp_phone'];
     $temp_msg=  $_SESSION['temp_msg'];
    


    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $date = htmlspecialchars($_POST['date']);
    $slot = htmlspecialchars($_POST['slot']);
    $msg = htmlspecialchars($_POST['msg']);
    $doctorId = $docId;
    $patientId = $pat['id'];



    $ok = 1;
    if (empty($_POST['name'])) {
        $errors['name'] = 'Name is required';
        $ok = 0;
    }
    if (empty($_POST['phone'])) {
        $errors['phone'] = 'Phone is required';
        $ok = 0;
    }
    if (empty($_POST['email'])) {
        $errors['email'] = 'An email is required';
        $ok = 0;
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be a valid email address';
            $ok = 0;
        }
    }
    if (empty($_POST['date'])) {
        $errors['date'] = 'Date is required';
        $ok = 0;
    }
    if (empty($_POST['slot'])) {
        $errors['slot'] = 'Slot is required';
        $ok = 0;
    }

    if ($ok == 1) {
        $query = "SELECT * FROM appointment WHERE doctorId='$docId' and date='$date' and not slot<>'$slot'";
        $result = mysqli_query($conn, $query);
        $rowCount = mysqli_num_rows($result);

        if ($rowCount == 0) {
            $sql = "INSERT INTO appointment (name,email,phone, date, slot, msg,doctorId,patientId) VALUES (?,?,?,?,?,?,?,?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "ssssssss", $name, $email, $phone, $date, $slot, $msg, $doctorId, $patientId);
                mysqli_stmt_execute($stmt);

                $doc_fname = $doc['fname'];
                $doc_lname = $doc['lname'];
                $doc_designation = $doc['designation'];
                $app_date = $date;
                $app_time = $slot;

                $_SESSION['temp_name']='';
                $_SESSION['temp_email']='';
                $_SESSION['temp_phone']='';
                $_SESSION['temp_msg']='';
                $value = "Appointment Creation Successful";

               
            }
        } else {
            $value = "Time Slot Already Fillup";


        }

    } else if ($ok == 0) {
        $value = "Data Inserted Failed";
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor_Details</title>
    <link rel="stylesheet" href="../styling/doctorDetails.css">
    <link rel="stylesheet" href="../styling/header.css">
    <link rel="stylesheet" href="../styling/common.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>


    <?php
    include('../header.php');
    ?>

    <div class="container1">
        <div class='main'>
        <div class='menu-container'>
        <div class="form-btn menu1">
            <a href="patient.php" type="submit" class="ok" value="" name="submit">Doctor List</a>

            </div>
            </div>
            <div>
                <p>Doctor Details</p>
            </div>
            <div class="menu-container">
                <div class="menu">
                    <button class="dropbtn">
                        <?php echo $pat['fname'] . " " . $pat['lname'] ?>
                    </button>
                    <div class="dropdown-content">
                        <a href="myAppointment.php">My Appointment</a>
                        <a href="../logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class='details'>

            <div>

            </div>

     
        <div class="card">              
                <img src="../<?php echo $doc['image'] ?>" alt="">
                <h2 class='text'>
                    <?php  echo "DR."." ".$doc['fname'] . " " . $doc['lname'] ?>
                </h2>              
                <p>
                    <?php echo $doc['designation'] ?>
                </p>
                <p>
                    <?php echo $doc['description'] ?>
                </p>
            </div>
     

            <div class="bio">
                <div class="appointment-container">
                    <p>Doctor Appointment Request</p>
                    <form class="appointment-form" method="POST">
                        <div class="form-group">
                            <label for="name">Your Name:</label>

                            <input type="text" id="name" name="name" value="<?php if($_SESSION['temp_name']!=''){echo $_SESSION['temp_name'];}?>" required>
                            <?php
                            if (isset($errors['name']) && !empty($errors['name'])) {
                                echo "<div class='txt-red '>" . $errors['name'] . "</div>";
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                      
                            <input type="email" id="email" name="email"  value="<?php if($_SESSION['temp_email']!=''){echo $_SESSION['temp_email'];}?>" required>
                            <?php
                            if (isset($errors['email']) && !empty($errors['email'])) {
                                echo "<div class='txt-red '>" . $errors['email'] . "</div>";
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number:</label>
                        
                            <input type="tel" id="phone" name="phone"  value="<?php if($_SESSION['temp_phone']!=''){echo $_SESSION['temp_phone'];} ?>" required>
                            <?php
                            if (isset($errors['phone']) && !empty($errors['phone'])) {
                                echo "<div class='txt-red '>" . $errors['phone'] . "</div>";
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <label for="date">Appointment Date:</label>

                            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                                min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class='form-group'>
                            <label for="slot">Select Slot:</label>
                            <select id="slot" name="slot" class="custom-select">
                                <?php
                                foreach ($schedule as $sch): ?>
                                    <option value="<?php echo $sch ?>">

                                    <p> <?php echo $sch ?></p>
                                       
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="msg">Additional Message (Optional):</label>
                            
                            <textarea id="msg" name="msg" rows="4" ><?php if($_SESSION['temp_msg']!=''){echo $$_SESSION['temp_msg'];}?></textarea>
                        </div>


                        <div class="form-btn">
                            <input type="submit" class="" value="Resquest Appointment" name="submit">

                        </div>

                        <div>
                         <p><?php 
                           if($value=='Appointment Creation Successful'){
                            $page=$doc['id'];
                            echo "<script>
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                  toast.addEventListener('mouseenter', Swal.stopTimer)
                                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                              })                             
                              Toast.fire({
                                icon: 'success',
                                title: 'Appointment Request Success'
                              }).then(() => {
                                window.location.href = 'doctorDetails.php?id=$page';
                            });
                          </script>";

                           }
                           else if($value=='Time Slot Already Fillup')
                           {
                            $page=$doc['id'];
                            echo "<script>
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                  toast.addEventListener('mouseenter', Swal.stopTimer)
                                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                              })                            
                              Toast.fire({
                                icon: 'info',
                                title: 'Slot Already Fillup'
                              }).then(() => {
                                window.location.href = 'doctorDetails.php?id=$page';
                            });
                          </script>";

                           }
                         
                         ?></p>

                        </div>



                    </form>
                </div>
            </div>

            <div></div>


        </div>
       
    </div>

    <?php include('../footer.php') ?>





 
</body>

</html>