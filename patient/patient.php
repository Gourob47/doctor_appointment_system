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

$deptSelect = 'All';


$sqlDoc = "SELECT * FROM doctor";
$resultDoc = mysqli_query($conn, $sqlDoc);
$doc = mysqli_fetch_array($resultPat);

if (isset($_POST['dept'])) {

  $deptSelect = $_POST['dept'];
  if ($deptSelect != 'All') {
    $sqlDoc = "SELECT * FROM doctor WHERE dept='$deptSelect'";
    $resultDoc = mysqli_query($conn, $sqlDoc);
    $doc = mysqli_fetch_array($resultPat);

  } else {
    $resultDoc = mysqli_query($conn, $sqlDoc);

  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="../styling/patient.css">
  <link rel="stylesheet" href="../styling/common.css">
  <link rel="stylesheet" href="../styling/header.css">

</head>

<body>
  <?php
  include('../header.php');
  ?>
  <div class='container'>
    <div class='main'>
      <div class='dept'>
        <div class="select-container">
          <form method='post'>
            <select id="mySelect" class="custom-select" name='dept' onchange="this.form.submit()">
              <option value="<?php echo $deptSelect ?>">
                <?php echo $deptSelect ?>
              </option>

              <?php if($deptSelect!="Medicine"):?>
               <option value="Medicine">Medicine</option>
              <?php endif; ?>

              <?php if($deptSelect!="Kideny"):?>
               <option value="Kideny">kideny</option>
              <?php endif; ?>

              
              <?php if($deptSelect!="Liver"):?>
               <option value="Liver">Liver</option>
              <?php endif; ?>
             
                           
              <?php if($deptSelect!="Gyne"):?>
               <option value="Gyne">Gyne</option>
              <?php endif; ?>
           
     
                    
              <?php if($deptSelect!="Neuro"):?>
               <option value="Neuro">Neuro</option>
              <?php endif; ?>
              <?php if($deptSelect!="Pediatric"):?>
               <option value="Pediatric">Pediatric</option>
              <?php endif; ?>

              <?php if($deptSelect!="Ent"):?>
               <option value="Ent">Ent</option>
              <?php endif; ?>


              <?php if ($deptSelect != 'All'): ?>
                <option value="All">All</option>
              <?php endif ?>
            </select>
          </form>
          <h1>
            <?php $dept ?>
          </h1>

        </div>
      </div>
      <div class='txt'>
        <p>Doctors</p>
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

    <div class='grid'>
        <?php foreach ($resultDoc as $res): ?>
          <div class='grid-area'>
            <div class='card'>
              <img height="250" width="250" src="../<?php echo $res['image'] ?>" alt="">
              <h2>
                <?php echo 'Dr' . " " . ($res['fname']) . " " . ($res['lname']) ?>
              </h2>
              <p>
                <?php echo ($res['designation']) ?>
              </p>
              <a href='doctorDetails.php?id=<?php echo ($res['id']) ?>' class='btn'>Book Appointment</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>


   
  </div>
  <?php include('../footer.php') ?>
</body>

</html>