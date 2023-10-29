<?php
session_start();
if (!isset($_SESSION["role"])) {
  header("Location: ../login.php");
} else if ($_SESSION['role'] == 0) {
  header("Location: ../login.php");
}

$user = array('fname' => '', 'lname' => '');


include('../database/database.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$id = $_SESSION["id"];
$sql = "SELECT * FROM doctor WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$doctor = mysqli_fetch_array($result);

$appointmentQuery = "SELECT * FROM appointment WHERE doctorId='$id'";
$resultAppointment = mysqli_query($conn, $appointmentQuery);
$appointment = mysqli_fetch_all($resultAppointment, MYSQLI_ASSOC);



$dateFilter = 'Filter By Date';
$dateArray = [];
foreach ($appointment as $row) {
  array_push($dateArray, $row['date']);
}
$uniquedate = array_unique($dateArray);

$selectDate = '';
if(isset($_SESSION['select_date']))
{
  $selectDate = $_SESSION['select_date'];
}

if (isset($_POST['date'])) {
 // echo $_POST['date'];
  $selectDate = $_POST['date'];

  $dateFilter = $_POST['date'];
  
  $_SESSION['select_date']=$_POST['date'];


  if ($selectDate !== '') {
    $appointmentQuery = "SELECT * FROM appointment WHERE doctorId='$id' and date='$selectDate'";
    $resultAppointment = mysqli_query($conn, $appointmentQuery);
    $appointment = mysqli_fetch_all($resultAppointment, MYSQLI_ASSOC);

   }
   // else if ($selectDate == 'Filter-By-Date') {
  //   $appointmentQuery = "SELECT * FROM appointment WHERE doctorId='$id'";
  //   $resultAppointment = mysqli_query($conn, $appointmentQuery);
  //   $appointment = mysqli_fetch_all($resultAppointment, MYSQLI_ASSOC);


  // }
}



//Function for Sending Email
function sendEmail($patientEmail, $options, $name, $desig, $date, $slot)
{
  require '../vendor/autoload.php';
  $mail = new PHPMailer(true);
  try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'mail.teamrabbil.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@teamrabbil.com';
    $mail->Password = '~sR4[bhaC[Qs';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->SMTPOptions = array(
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      )
    );

    $mail->setFrom('info@teamrabbil.com', 'Mailer');
    $mail->addAddress($patientEmail);
    $mail->isHTML(true);
    $mail->Subject = 'Here is the subject';
    if ($options == 'success') {
      $mail->Body = '
        <html>
        <body>
            <h1>Your Appointment Book Successfully</h1>          
            <p>
            Doctor Name: ' . $name . '<br>
            Doctor Designation: ' . $desig . '<br>
            Appointment Date: ' . $date . '<br>
            Appointment Time: ' . $slot . '<br>
                Status:"Success"
            </p>
    
            <p>Please be there before 10 minutes</p>
            <p>Thank You</p>
        </body>
        </html>';
    } else {
      $mail->Body = '
        <html>
        <body>
            <h1>Your Appointment Booking Cancelled</h1>
            
            <p>
                Doctor Name: ' . $name . '<br>
                Doctor Designation: ' . $desig . '<br>
                Appointment Date: ' . $date . '<br>
                Appointment Time: ' . $slot . ' <br>
                Status:"Cancelled"
            </p>
    
            <p>We Are very Sorry for Doctor is not able to take more Appointment for those day. Please try for another available Slot</p>
            <p>Thank You</p>
        </body>
        </html>';
    }


    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }

}

$updateSuccess = false;
$updateConfirm = false;
$updateCancel = false;
if (isset($_POST['confirm'])) {


  $value = 1;
  $appointId = $_GET['id'];


  $sqlUpdate = "UPDATE appointment SET status='$value' WHERE id='$appointId'";

  $doctorFullname = $doctor['fname'] . " " . $doctor['lname'];
  $doctorDesignation = $doctor['designation'];
  $appDate = $_GET['date'];
  $appSlot = $_GET['slot'];

  if (mysqli_query($conn, $sqlUpdate)) {
    $updateSuccess = true;
    $updateConfirm = true;
    $patientEmail = $_GET['email'];
    

    sendEmail($patientEmail, "success", $doctorFullname, $doctorDesignation, $appDate, $appSlot);
  } else {
    $updateSuccess = false;
    $updateConfirm = false;
  }
} else if (isset($_POST['cancel'])) {
  $value = 2;
  $appointId = $_GET['id'];
  $sqlUpdate = "UPDATE appointment SET status='$value' WHERE id='$appointId'";
  if (mysqli_query($conn, $sqlUpdate)) {
    $updateSuccess = true;
    $updateCancel = true;
    $patientEmail = $_GET['email'];
    sendEmail($patientEmail, "cancel", $doctorFullname, $doctorDesignation, $appDate, $appSlot);
  } else {
    $updateSuccess = false;
    $updateCancel = false;
  }
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor</title>
  <link rel="stylesheet" href="../styling/doctor.css">
  <link rel="stylesheet" href="../styling/common.css">
  <link rel="stylesheet" href="../styling/header.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php
  include('../header.php');
  ?>
  <div class='container'>

    <div class='main'>
      <div>
        <div class='dept'>
          <div class="select-container">

            <form class="form-group" method="POST">
                           
                  <input type="date" id="date" name="date"  class="calendar-input" value="<?php echo $selectDate;  ?>"  onchange="this.form.submit()">
              </form>
            <h1>
              <?php $dept ?>
            </h1>

          </div>
        </div>
      </div>
      <div>
        <p>Appointment List</p>
      </div>
      <div class="menu-container">
        <div class="menu">
          <button class="dropbtn">
            <?php echo $doctor['fname'] . " " . $doctor['lname'] ?>
          </button>
          <div class="dropdown-content">
            <a href="doctorEdit.php?id=<?php echo $doctor['id'] ?>">Edit Profile</a>
            <a href="../logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <div class='main1'>
    </div>
  </div>

  <div class='height'>
    <table class="item-list">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Date</th>
          <th>Slot</th>
          <th>Message</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($appointment as $app): ?>
          <tr>
            <td>
              <?php echo $app['name'] ?>
            </td>
            <td>
              <?php echo $app['email'] ?>
            </td>
            <td>
              <?php echo $app['phone'] ?>
            </td>
            <td>
              <?php echo $app['date'] ?>
            </td>
            <td>
              <?php echo $app['slot'] ?>
            </td>
            <td>
              <?php if ($app['msg'] == '') {
                echo "No Message";
              } else {
                echo $app['msg'];
              }
              ?>
            </td>

            <td>


              <div class='tab-display'>

                <?php if ($app['status'] == 0): ?>

                  <?php

                  $passId = $app['id'];
                  $passEmail = $app['email'];
                  $passDate = $app['date'];
                  $passSlot = $app['slot'];
                  $items = array($passId, $passEmail, $passDate, $passSlot);
                  ?>

                  <div>


                    <form
                      action="doctor.php?id=<?php echo $passId ?>&email=<?php echo $passEmail ?>&date=<?php echo $passDate ?>&slot=<?php echo $passSlot ?>"
                      method="POST">
                      <input type="submit" name="confirm" value="Confirm" class="styled-button1">
                      <?php

                      if ($updateConfirm == true) {
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
                        title: 'Appointment Confirmed'
                      }).then(() => {
                        window.location.href = 'doctor.php';
                    });
                  </script>";
                      }

                      ?>
                    </form>
                  </div>

                  <div>
                    <form
                      action="doctor.php?id=<?php echo $passId ?>&email=<?php echo $passEmail ?>&date=<?php echo $passDate ?>&slot=<?php echo $passSlot ?>"
                      method="POST">
                      <input type="submit" name="cancel" value="Cancel" class="styled-button">
                      <?php
                      if ($updateCancel == true) {
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
                        icon: 'error',
                        title: 'Appointment Canceled'
                      }).then(() => {
                        window.location.href = 'doctor.php';
                    });
                  </script>";
                      }

                      ?>
                    </form>
                  </div>

                <?php elseif ($app['status'] == 1): ?>



                  <h4>CONFIRMED</h4>

                <?php elseif ($app['status'] == 2): ?>

                  <h4>CANCELLED</h4>

                <?php endif ?>


              </div>

            </td>

          </tr>

        <?php endforeach; ?>

      </tbody>
    </table>
  </div>

  <?php include('../footer.php') ?>
</body>

</html>