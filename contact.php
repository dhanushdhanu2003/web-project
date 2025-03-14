<?php

include 'config.php';  // Ensure your database and other configuration settings are properly included
session_start();  // Start the session

// Check if user_id exists in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Fetch user_id from session if available
} else {
    $user_id = null;  // Set $user_id to null if not logged in
}

$message = array();  // Initialize an array to hold messages

if (isset($_POST['send'])) {
    // Ensure data is available before assigning
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $number = isset($_POST['number']) ? $_POST['number'] : '';
    $msg = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : '';

    // Check if the user is logged in
    if ($user_id === null) {
        $message[] = 'Please login first to send a message.';
    }

    // Validate name
    if (empty($name)) {
        $message[] = 'Please enter your name.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $message[] = 'Name should only contain letters and spaces.';
    } elseif (strlen($name) > 10) {
        $message[] = 'Name should not exceed 10 letters.';
    }

    // Validate message
    if (empty($msg)) {
        $message[] = 'Please enter your message.';
    } elseif (strlen($msg) > 30) {
        $message[] = 'Message should not exceed 30 characters.';
    }

    // Validate number (phone number)
    if (empty($number)) {
        $message[] = 'Please enter your number.';
    } elseif (!preg_match("/^[0-9]{10}$/", $number)) {
        $message[] = 'Invalid number format. Please enter a 10-digit phone number.';
    }

    // Validate email
    if (empty($email)) {
        $message[] = 'Please enter your email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Please enter a valid email address.';
    }

    // If no validation errors, proceed with message insertion
    if (empty($message)) {
        $insert_query = "INSERT INTO `message`(name, email, number, message) VALUES('$name', '$email', '$number', '$msg')";
        $insert_result = mysqli_query($conn, $insert_query);
        
        if ($insert_result) {
            $message[] = 'Message sent successfully!';
        } else {
            $message[] = 'Failed to send message. Please try again.';
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
    <title>Contact</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    
    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Contact Us</h3>
        <p><a href="home.php">Home</a> / Contact</p>
    </div>

    <section class="contact">
        <!-- Display messages if there are any -->
        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<p class="message">' . $msg . '</p>';
            }
        }
        ?>

        <form action="" method="post">
            <h3>Say Something!</h3>
            <input type="text" name="name" required placeholder="Enter your name" class="box" maxlength="10">
            <input type="email" name="email" required placeholder="Enter your email" class="box">
            <input type="number" name="number" required placeholder="Enter your number" class="box" maxlength="10">
            <textarea name="message" class="box" placeholder="Enter your message" required cols="30" rows="10" maxlength="30"></textarea>
            <input type="submit" value="Send Message" name="send" class="btn">
        </form>

    </section>

    <?php include 'footer.php'; ?>

    <!-- Custom JS file link -->
    <script src="js/script.js"></script>

</body>

</html>
