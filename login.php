<?php

session_start();
if (isset($_SESSION["role"])) {
  if ($_SESSION['role'] == 0)
    header("Location: patient/patient.php");
  else
    header("Location: doctor/doctor.php");
}

$email = $password = $loginFailed = $loginSuccess = $role = '';
if (isset($_POST['submit'])) {
  $email = $_POST["email"];
  $password = $_POST["password"];


  require_once "database/database.php";

  $sqlDoc = "SELECT * FROM doctor WHERE email = '$email'";
  $resultDoc = mysqli_query($conn, $sqlDoc);
  $doc = mysqli_fetch_array($resultDoc, MYSQLI_ASSOC);



  if ($doc) {
    if ($doc and password_verify($password, $doc["password"])) {     
      $role = $doc["role"];
      
      $_SESSION['id']=$doc['id'];
      $_SESSION['role']=$doc['role'];
      $loginSuccess = 'success';
      
    } else {
      $loginFailed = 'Login Failed';
    }

  }
  else if(!$doc)
  { 
    $sqlPat = "SELECT * FROM patient WHERE email = '$email'";
    $resultPat = mysqli_query($conn, $sqlPat);
    $pat= mysqli_fetch_array($resultPat, MYSQLI_ASSOC);
    if ($pat and password_verify($password, $pat["password"])) {     
      $role = $pat["role"];
      session_start();
      $_SESSION['id']=$pat['id'];
      $_SESSION['role']=$pat['role'];
      $_SESSION['fname']=$pat['fname'];
      $_SESSION['lname']=$pat['lname'];

      $loginSuccess = 'success'; 
    } else {
      $loginFailed = 'Login Failed';
     
    }
  }
  else
  {
    $loginFailed = 'Login Failed';      
  }

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Page</title>
  <link rel="stylesheet" href="styling/loginLogout.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<?php   
  include('header.php');
?>
  <div class='container1'>
   <div class='con1'>
       <div class='con2'>
       <div class='main'>
      <form action="login.php" method="POST">
        <h2>Login Portal</h2>
        <div class="form-group">
          <input type="email" class="form-control" name="email" placeholder="Email *">
        </div>
        <div class="form-group">
          <input type="password" class="form-control" name="password" placeholder="Password *">
        </div>

        <div class="form-btn">
          <input type="submit" onclick="showAlert()" class="" value="Login" name="submit">

         <?php
          if (!empty($loginSuccess)) {
            
            if ($_SESSION['role'] == 0) {
              header("Location: patient/patient.php");
      
     
            } else {
              header("Location: doctor/doctor.php");
     
            }

          } else if (!empty($loginFailed)) {
            echo "<script>
            Swal.fire({
                title: 'Login Failed!',
                text: 'Please Enter valid Data',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = 'login.php';
          });
          </script>";
        

          }
          ?> 

        </div>
        <div class="form-footer">
          <h4>If You Don't Have Any Account</h4>
          <a href="register.php" class='aa'>SignUp</a>
        </div>
      </form>
    </div>
       </div>
   </div>
   
  </div>


  <?php include('footer.php') ?>

</body>

</html>