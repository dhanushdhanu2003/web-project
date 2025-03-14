<?php
include 'config.php';  // Ensure your database and other configuration settings are properly included
session_start();  // Start the session

// Check if user_id exists in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Fetch user_id from session if available
} else {
    $user_id = null;  // Set $user_id to null if not logged in
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>about us</h3>
   <p> <a href="home.php">home</a> / about </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>We offer an extensive collection of books, carefully curated to cater to diverse interests and preferences, ensuring there's something for every reader.
Our commitment to exceptional customer service, affordable prices, and prompt delivery sets us apart, making us the ideal destination for book lovers seeking quality reads and a seamless shopping experience.
         </p>
         <p>Apart of this numerous advantage of choosing us, <br>
            
             100% original books<br>
             Books available at affordable prices<br>
            cash on delivery facility available
              </p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>

</section>

<section class="reviews">

   <h1 class="title">client's reviews</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/pic-1.png" alt="">
         <p>"Impressed by the wide selection of books and the ease of navigating the website. The ordering process was seamless, and my books arrived in excellent condition. Highly recommend!"</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Dhanush</h3>
      </div>

      <div class="box">
         <img src="images/pic-2.png" alt="">
         <p>on time delivery books are good</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Alexiya</h3>
      </div>

      <div class="box">
         <img src="images/pic-3.png" alt="">
         <p>"Outstanding service! The book recommendations were spot-on, and the delivery was prompt. Will definitely be ordering from them again."
</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Ram</h3>
      </div>

      <div class="box">
         <img src="images/pic-4.png" alt="">
         <p>"A great bookstore with a fantastic selection! The customer service was friendly and helpful. Highly recommend for book lovers!"</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Hemavathi</h3>
      </div>

      

   </div>

</section>

<section class="authors">

   <h1 class="title">greate authors</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/Rudyard_Kipling.jpg" alt="">
        
         <h3>Rudyard Kipling</h3>
      </div>

      <div class="box">
         <img src="images/sashitharoor.jpg" alt="">
         
         <h3>sashi tharoor</h3>
      </div>

      <div class="box">
         <img src="images/author-3.jpg" alt="">
         <div class="share">
         </div>
         <h3>john deo</h3>
      </div>

      

   </div>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>