<?php
session_start();
if (!isset($_SESSION["role"])) {
    header("Location: ../login.php");
} else if ($_SESSION['role'] == 1) {
    header("Location: ../login.php");
}

$user = array('fname' => '', 'lname' => '');


include('../database/database.php');

$fname = $_SESSION['fname'];
$lname = $_SESSION['lname'];



$id = $_SESSION["id"];


$sql = "SELECT appointment.*, doctor.fname,doctor.lname,doctor.designation
        FROM appointment
        LEFT JOIN doctor ON appointment.doctorId = doctor.id WHERE appointment.patientId = '$id'";




$resultAppointment = mysqli_query($conn, $sql);
$appoint = mysqli_fetch_all($resultAppointment, MYSQLI_ASSOC);





$value = '';
if (isset($_GET['id'])) {

    $idToDelete = $_GET['id']; 
    $sql = "DELETE FROM appointment WHERE id ='$idToDelete'";


    if (mysqli_query($conn, $sql)) {
        $value = 'success';

    } else {
        $value = 'fail';
       
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
                <div class=''>
                    <div class="select-container">
                        <div class='menu-container'>
                            <div class="form-btn menu1">
                                <a href="patient.php" type="submit" class="ok" value="" name="submit">Doctors</a>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div>
                <p>My Appointment</p>
            </div>
            <div class="menu-container">
                <div class="menu">
                    <button class="dropbtn">
                        <?php echo $fname . " " . $lname ?>
                    </button>
                    <div class="dropdown-content">

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
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Deignation</th>
                    <th>Time</th>
                    <th>Slot</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appoint as $app): ?>
                    <tr>

                        <td>

                            <?php echo $app['fname'] . " " . $app['lname'] ?>
                        </td>
                        <td>
                            <?php echo $app['fname'] . " " . $app['lname'] ?>
                        </td>
                        <td>
                            <?php echo $app['designation'] ?>
                        </td>
                        <td>
                            <?php echo $app['date'] ?>
                        </td>
                        <td>
                            <?php echo $app['slot'] ?>
                            <?php echo $app['status']?>
                        </td>
                        <td>

                        <?php if($app['status']==0): ?>
                            <form action="myAppointment.php?id=<?php echo $app['id'] ?>" method="POST">
                                <input type="submit" name="submit" value="Cancel" class="styled-button">
                            </form>
                        <?php elseif($app['status']==1): ?>
                            
                            <h4>Appointment Confirmed</h4>

                        <?php  ?>

                        <?php elseif($app['status']==2): ?>
                            
                            <h4>Appointment Cancelled</h4>

                        <?php endif; ?>

                            <?php

                            if ($value == 'success') {
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
                                    title: 'Appointment Cancellation Success'
                                  }).then(() => {
                                    window.location.href = 'myAppointment.php';
                                });
                              </script>";
                            }

                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>



    <?php include('../footer.php') ?>
</body>

</html>