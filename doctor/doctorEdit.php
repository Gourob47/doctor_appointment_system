<?php
session_start();
if (!isset($_SESSION["role"])) {
  header("Location: ../login.php");
}
else if($_SESSION['role']==0)
{
  header("Location: ../login.php");
}

$user = array('fname' => '', 'lname' => '');


include('../database/database.php');

$id = $_SESSION["id"];
$sql = "SELECT * FROM doctor WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$doctor = mysqli_fetch_array($result);


$updateSuccess = $updateFailed = '';

if (isset($_POST['submit'])) {

  $fname = htmlspecialchars($_POST['fname']);
  $lname = htmlspecialchars($_POST['lname']);
  $designation = htmlspecialchars($_POST['designation']);
  $description = htmlspecialchars($_POST['description']);



  if ($fname and $lname and $designation and $description) {

    if ($_FILES["image"]["name"] == '') {
      $sqlUpdate = "UPDATE doctor SET fname='$fname', lname='$lname', designation='$designation', description='$description' WHERE id='$id'";

      if (mysqli_query($conn, $sqlUpdate)) {
        $updateSuccess = "Profile Updated Successfully";

      } else {
        $updateFailed = "Profile Updated Failed";
      }

    } else {


      $target_dir = "../uploads/";
      $target_file = $target_dir . basename($_FILES["image"]["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
      $check = getimagesize($_FILES["image"]["tmp_name"]);

      $final_dir = "uploads/" . basename($_FILES["image"]["name"]);

      if (file_exists($target_file)) {
        unlink($target_file);
        $uploadOk = 1;
      }
      if ($uploadOk == 1) {
        if ($_FILES["image"]["size"] > 500000) {
          echo "Sorry, your file is too large.";
          $uploadOk = 0;
        } else {
          $uploadOk = 1;
        }

      }

      if ($uploadOk == 1 and $check !== false) {
        $imagename = $_FILES["image"]["tmp_name"];
        $res = false;
        if ($check !== false) {
          if (move_uploaded_file($imagename, $target_file)) {
            //echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
            $res = true;
          } else {
            // echo "Sorry, there was an error uploading your file.";
            $res = false;
          }
        }

        if ($res) {
          $sqlUpdate = "UPDATE doctor SET fname='$fname', lname='$lname', designation='$designation', description='$description', image='$final_dir' WHERE id='$id'";
          if (mysqli_query($conn, $sqlUpdate)) {

            $updateSuccess = "Profile Updated Successfully";

          }
        }
      } else {
        $updateFailed = "Profile Updated Failed";
      }
    }

  } else {
    $updateFailed = "Profile Updated Failed";
  }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>

  <link rel="stylesheet" href="../styling/common.css">
  <link rel="stylesheet" href="../styling/header.css">
  <link rel="stylesheet" href="../styling/doctorEdit.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <body>
    <?php
    include('../header.php');
    ?>

    <div class='main'>
      <div class='txt'>

      </div>
      <div class='txt'>
        <p>My Profile</p>
      </div>
      <div class="menu-container">
        <div class="menu">
          <button class="dropbtn">
            <?php echo $doctor['fname'] . " " . $doctor['lname'] ?>
          </button>
          <div class="dropdown-content">
            <a href="doctor.php">Appointment</a>
            <a href="../logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>


    <div class="container1">
      <h2>Edit Your Profile</h2>
      <form action="doctorEdit.php?id=<?php echo $doctor['id'] ?>" method="POST" enctype="multipart/form-data">


        <div class="form-group">
          <label for="fname">Firstname:</label>
          <input type="text" id="fname" name="fname" value="<?php echo $doctor['fname'] ?>">
        </div>
        <div class="form-group">
          <label for="lname">Lastname:</label>
          <input type="text" id="lname" name="lname" value="<?php echo $doctor['lname'] ?>">
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" value="<?php echo $doctor['email'] ?>">
        </div>
        <div class="form-group">
          <label for="designation">Designation:</label>
          <input type="text" id="designation" name="designation" value="<?php echo $doctor['designation'] ?>">
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea type="text" id="description" name="description"
            rows="10"><?php echo $doctor['description'] ?></textarea>
        </div>
        <div class="form-group">
          <img src="../<?php echo $doctor['image'] ?>" height="200" width="200" alt="">
        </div>
        <div class="form-group">
          <input type="file" class="form-control" name="image" placeholder="Picture">
        </div>

        <div class="form-group">
          <input type="submit" name="submit" value="Update Profile">

          <?php



          if (!empty($updateSuccess)) {
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
              title: 'Profile Update successfully'
            }).then(() => {
              window.location.href = 'doctorEdit.php?id=$id';
          });
          </script>";


          } else if (!empty($updateFailed)) {
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
              title: 'Profile Update Failed'
            }).then(() => {
              window.location.href = 'doctorEdit.php?id=$id';
          });
          </script>";


          }


          ?>


        </div>
      </form>

    </div>



    <?php echo $value ?>

    <?php include('../footer.php') ?>


  </body>

</html>