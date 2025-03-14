<?php
include 'config.php';

$message = array(); // Initialize an empty array to store validation messages

if(isset($_POST['submit'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $user_type = $_POST['user_type'];

   // Validate name
   if(empty($name)){
      $message[] = 'Please enter your name.';
   } elseif(!preg_match('/^[a-zA-Z\s]+$/', $name)){
      $message[] = 'Name should only contain letters and spaces.';
   }
   elseif(strlen($name) > 10){
      $message[] = 'Name should not exceed 10 letters.';
   }

   // Validate email
   if(empty($email)){
      $message[] = 'Please enter your email.';
   } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $message[] = 'Please enter a valid email address.';
   }

   // Validate password
   if(empty($_POST['password'])){
      $message[] = 'Please enter a password.';
   }elseif(strlen($_POST['password']) !== 10){
      $message[] = 'Password should be exactly 10 characters long.';
   }

   // Validate confirm password
   if(empty($_POST['cpassword'])){
      $message[] = 'Please confirm your password.';
   } elseif($_POST['password'] !== $_POST['cpassword']){
      $message[] = 'Confirm password does not match.';
   }

   // Check if any validation errors occurred
   if(empty($message)){
      $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('Query failed');

      if(mysqli_num_rows($select_users) > 0){
         $message[] = 'User already exists!';
      } else {
         mysqli_query($conn, "INSERT INTO `users` (name, email, password, user_type) VALUES ('$name', '$email', '$cpass', '$user_type')") or die('Query failed');
         $message[] = 'Registered successfully!';
         header('location: login.php');
         exit(); // Add exit() to prevent further execution of the code
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<div class="form-container">
   <form action="" method="post" id="form">
      <h3>register now</h3>
      <input type="text" name="name" placeholder="Enter your name"  class="box" id="name">
      <input type="email" name="email" placeholder="Enter your email"  class="box" id="email">
      <input type="password" name="password" placeholder="Enter your password"  class="box" id="password">
      <input type="password" name="cpassword" placeholder="Confirm your password"  class="box" id="cpassword">
      <select name="user_type"class="box">
         <option value="user">user</option> 
         <option value="admin">admin</option>
      </select>
      <input type="submit" name="submit" value="register now" class="btn" id="submit">
      <p>already have an account? <a href="login.php">login now</a></p>
      <div>
         
      </div>
   </form>
</div>
<script src="js/script.js"></script>

</body>
</html>