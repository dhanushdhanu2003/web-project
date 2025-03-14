<?php
// Enable error reporting to see any issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

// Handle password reset if the form is submitted
if (isset($_POST['reset'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if the email exists in the database
    $select_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_user) > 0) {
        // User exists, fetch the user data
        $user = mysqli_fetch_assoc($select_user);

        // Check if the old password matches the current password
        if (password_verify($old_password, $user['password'])) {
            // Check if the new password and confirm password match
            if ($new_password === $confirm_password) {
                // Hash the new password before saving
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the user's password in the database
                $update_query = "UPDATE users SET password = '$hashed_new_password' WHERE email = '$email'";
                if (mysqli_query($conn, $update_query)) {
                    $message[] = 'Password has been reset successfully!';
                } else {
                    $message[] = 'Failed to update password. Please try again.';
                }
            } else {
                $message[] = 'New password and confirm password do not match!';
            }
        } else {
            $message[] = 'Old password is incorrect!';
        }
    } else {
        $message[] = 'No account found with that email address!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
// Display any messages (e.g., success or error)
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . $msg . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<div class="form-container">
    <!-- Password Reset Form -->
    <form action="" method="post">
        <h3>Reset Your Password</h3>
        <input type="email" name="email" placeholder="Enter your email" required class="box">
        <input type="password" name="old_password" placeholder="Enter your old password" required class="box">
        <input type="password" name="new_password" placeholder="Enter your new password" required class="box">
        <input type="password" name="confirm_password" placeholder="Confirm your new password" required class="box">
        <input type="submit" name="reset" value="Reset Password" class="btn">
     
        <p><a href="login.php">Back to Login</a></p> <!-- Forgot password link -->
    </form>
   

    <!-- Back to Login link styled as a button -->
    <div class="back-to-login">
        
    </div>
</div>

</body>
</html>
