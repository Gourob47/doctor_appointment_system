<?php
session_start();
if (isset($_SESSION["role"])) {
    if ($_SESSION['role'] == 0)
        header("Location: patient/patient.php");
    else
        header("Location:doctor/doctor.php");
}

require "database/database.php";
$fname = $lname = $email = $password = '';
$designation = $description = $type = $dept = $image = '';
$userExist = $registerSuccess = $emailValid = '';
if (isset($_POST['submit'])) {



    $errors = array('fname' => '', 'lname' => '', 'email' => '', 'password' => '', 'designation' => '', 'type' => '', 'description' => '', 'dept' => '', 'file' => '', 'image' => '');

    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    $type = htmlspecialchars($_POST['type']);
    $dept = htmlspecialchars($_POST['dept']);




    if (empty($_POST['fname'])) {
        $errors['fname'] = 'FirstName is required';
    }
    if (empty($_POST['lname'])) {
        $errors['lname'] = 'LastName is required';
    }
    if (empty($_POST['email'])) {
        $errors['email'] = 'An email is required';
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be a valid email address';
        }
    }

    if (empty($_POST['type'])) {
        $errors['type'] = '  type is required';
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Password is required';
    }
    if (strlen($_POST['password']) < 8) {
        $errors['password'] = "Password is too short. It must be at least 8 characters long.";
    }
    if (!preg_match("/[A-Z]/", $_POST['password'])) {
        $errors['password'] = "Password must contain at least one uppercase letter.";
    }
    if (!preg_match("/[a-z]/", $_POST['password'])) {
        $errors['password'] = "Password must contain at least one lowercase letter.";
    }
    if (!preg_match("/\d/", $_POST['password'])) {
        $errors['password'] = "Password must contain at least one digit.";
    }
    else
    {
        $errors['password'] = " ";   
    }

    // Check Email is Exist or not
    // $okMail = false;


    // require_once 'VerifyEmail.class.php';


    // $mail = new VerifyEmail();


    // $mail->setStreamTimeoutWait(20);

    // $mail->Debug = false;
    // $mail->Debugoutput = 'html';


    // $mail->setEmailFrom('debnathgourob98@gmail.com');


     $userEmail = $email;


    // if ($mail->check($userEmail)) {
    //     //echo 'Email &lt;' . $userEmail . '&gt; is exist!';
    //     $okMail = true;
    // } elseif (verifyEmail::validate($userEmail)) {
    //     $okMail = false;
    //     //echo 'Email &lt;' . $userEmail . '&gt; is valid, but not exist!';
    // } else {
    //     $okMail = false;
    //     //echo 'Email &lt;' . $userEmail . '&gt; is not valid and not exist!';
    // }

    // echo $okMail;


    if ($type === 'patient') {

        if ($fname and $lname and $userEmail and $hashPassword) {
            $sql = "SELECT * FROM patient WHERE email = '$userEmail'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);

            if ($rowCount == 0) {
                $sql = "INSERT INTO patient (fname,lname, email, password) VALUES (?, ?, ?, ? )";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "ssss", $fname, $lname, $userEmail, $hashPassword);
                    mysqli_stmt_execute($stmt);
                    $registerSuccess = "User Created Successfully";
                    $fname = $lname = $email = $password = $type = '';

                }
            } else {
                $userExist = 'User Already exists';
            }

        }
    } else if ($type === 'doctor') {

        $designation = htmlspecialchars($_POST['designation']);
        $description = htmlspecialchars($_POST['description']);
        $dept = htmlspecialchars($_POST['dept']);

        if (empty($_POST['designation'])) {
            $errors['designation'] = 'designation is required';
        }
        if (empty($_POST['description'])) {
            $errors['description'] = 'description is required';
        }
        if (empty($_POST['dept'])) {
            $errors['dept'] = 'department is required';
        }



        if ($fname and $lname and $userEmail and $hashPassword and $description and $designation and $dept) {
            $sql = "SELECT * FROM doctor WHERE email = '$userEmail'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);

            if ($rowCount == 0) {

                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["image"]["tmp_name"]);

                if (file_exists($target_file)) {
                    echo "Sorry, file already exists.";
                    $uploadOk = 0;
                }


                if ($_FILES["image"]["size"] > 500000) {
                    echo "Sorry, your file is too large.";
                    $uploadOk = 0;
                }
                $imagename = $_FILES["image"]["tmp_name"];
                $res = false;
                if ($check !== false) {
                    if (move_uploaded_file($imagename, $target_file)) {
                       
                        $res = true;
                    } else {
                        $res = false;
                        
                    }
                }

                if ($res) {
                    $sql = "INSERT INTO doctor (fname,lname, email, password, designation, description,dept,image) VALUES (?,?,?,?,?,?,?,?)";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    if ($prepareStmt) {
                        mysqli_stmt_bind_param($stmt, "ssssssss", $fname, $lname, $userEmail, $hashPassword, $designation, $description, $dept, $target_file);
                        mysqli_stmt_execute($stmt);
                        $registerSuccess = "Doctor Created Successfully";
                    }
                }


            } else {
                $userExist = 'Doctor Already exists';
            }

        } else {
            $userExist = "Error";
        }



    } else {
        $emailValid = "Your Provided Email Doesn't Exist";
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
    <div class='body-div'>

        <div class='container'>
            <div class='con1'>
                <div class='con2'>
                    <div class='main'>
                        <form action="register.php" method="POST" enctype="multipart/form-data">
                            <h2>SignUp Portal</h2>
                            <div class="form-group">


                                <input type="text" class="form-control" name="fname" value="<?php $fname ?>"
                                    placeholder="First Name *" required>

                                <?php
                                if (isset($errors['fname']) && !empty($errors['fname'])) {
                                    echo "<div class='txt-red '>" . $errors['fname'] . "</div>";
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="lname" value="<?php $lname ?>"
                                    placeholder="Last Name *">
                                <div class="red-text">
                                    <?php
                                    if (isset($errors['lname']) && !empty($errors['lname'])) {
                                        echo "<div class='txt-red'>" . $errors['lname'] . "</div>";
                                    }

                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="email" class="form-control" name="email" value="<?php $email ?>"
                                    placeholder="Email *">
                                <div class="red-text">

                                    <?php
                                    if (isset($errors['email']) && !empty($errors['email'])) {
                                        echo "<div class='txt-red'>" . $errors['email'] . "</div>";
                                    }

                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control" name="password" value="<?php $password ?>"
                                    placeholder="Password *">
                                <div class="red-text">

                                    <?php
                                    if (isset($errors['password']) && !empty($errors['password'])) {
                                        echo "<div class='txt-red'>" . $errors['password'] . "</div>";
                                    }

                                    ?>
                                </div>
                            </div>

                            <div class=''>
                                <div class="select-container">
                                    <select id="dropdown" class="custom-select" name='type' <?php $type ?>>
                                        <option value="">Types</option>
                                        <option value="patient">Patient</option>
                                        <option value="doctor">Doctor</option>
                                    </select>
                                    <div class="red-text">

                                        <?php
                                        if (isset($errors['type']) && !empty($errors['type'])) {
                                            echo "<div class='txt-red'>" . $errors['type'] . "</div>";
                                        }
                                        ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <input id='specific-area' class='hidden' value="<?php $designation ?>" type="text"
                                    class="form-control" name="designation" placeholder="Designation *">
                                <div class="red-text">
                                    <?php
                                    if (isset($errors['designation']) && !empty($errors['designation'])) {
                                        echo "<div class='txt-red'>" . $errors['designation'] . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class='hidden' id='specific-area2'>
                                <div class="select-container">
                                    <select id="dropdown" class="custom-select" name='dept' <?php $dept ?>>
                                        <option value="">Department</option>
                                        <option value="medicine">Medicine</option>
                                        <option value="Kideny">Kideny</option>
                                        <option value="Liver">Liver</option>
                                        <option value="Gyne">Gyne</option>
                                        <option value="Neuro">Neuro</option>
                                        <option value="Pediatric">Pediatric</option>
                                        <option value="Ent">Ent</option>
                                    </select>
                                    <div class="red-text">
                                        <?php
                                        if (isset($errors['dept']) && !empty($errors['dept'])) {
                                            echo "<div class='txt-red'>" . $errors['dept'] . "</div>";
                                        }

                                        ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <input id='specific-area1' class='hidden' value="<?php $description ?>" type="text"
                                    class="form-control" name="description" placeholder="Description *">
                                <div class="red-text">
                                    <?php
                                    if (isset($errors['description']) && !empty($errors['description'])) {
                                        echo "<div class='txt-red'>" . $errors['description'] . "</div>";
                                    }
                                    ?>
                                </div>

                            </div>

                            <div class='form-group'>
                                <input id='specific-area3' class='hidden' value="<?php $file ?>" type="file"
                                    class="form-control" name="image" placeholder="Picture *">
                                <div class="red-text">
                                    <?php
                                    if (isset($errors['image']) && !empty($errors['image'])) {
                                        echo "<div class='txt-red'>" . $errors['image'] . "</div>";
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class="form-btn">
                                <input type="submit" class="" value="SignUp" name="submit">
                                <?php
                                if (!empty($userExist)) {
                                    echo "<script>
                        Swal.fire({
                            title: 'Register Failed!',                          
                            icon: 'error',
                            text:'User Already Exists',
                            confirmButtonText: 'OK'
                        })
                      </script>";
                                } else if (!empty($registerSuccess)) {
                                    echo "<script>
                        Swal.fire({
                            title: 'Register Success!',                          
                            icon: 'success',
                            text:'Registration Success',
                            confirmButtonText: 'OK'
                        })
                      </script>";
                                } else if (!empty($emailValid)) {
                                    echo "<script>

                                
                        Swal.fire({
                            title: 'Registration Failed!',                          
                            icon: 'error',
                            text:'Email Does not exist!',
                            confirmButtonText: 'OK'
                        })
                       
                      </script>";
                                }

                                ?>
                            </div>

                            <div>
                                <div class="form-footer">

                                    <h4>If You already Have a Account</h4>

                                    <a href="login.php" class='aa'>Login</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>




        </div>

    </div>

    <?php include('footer.php') ?>

    <script>
        let select = document.getElementById('dropdown');
        let specificArea = document.getElementById('specific-area');
        let specificArea1 = document.getElementById('specific-area1');
        let specificArea2 = document.getElementById('specific-area2');
        let specificArea3 = document.getElementById('specific-area3');



        select.addEventListener('change', function () {

            let selectedValue = select.value;
            if (selectedValue === 'doctor') {

                specificArea.style.display = 'block';
                specificArea1.style.display = 'block';
                specificArea2.style.display = 'block';
                specificArea3.style.display = 'block';

            } else {
                specificArea.style.display = 'none';
                specificArea1.style.display = 'none';
                specificArea2.style.display = 'none';
                specificArea3.style.display = 'none';

            }
        });
    </script>




</body>

</html>