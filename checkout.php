<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_POST['order_btn'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $method = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn, 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if (mysqli_num_rows($cart_query) > 0) {
      while ($cart_item = mysqli_fetch_assoc($cart_query)) {
         $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ', $cart_products);

   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

   if ($cart_total == 0) {
      $message[] = 'Your cart is empty.';
   } else {
      if (mysqli_num_rows($order_query) > 0) {
         $message[] = 'Order already placed!';
      } else {


         // Validate name
       if(empty($name)){
      $message[] = 'Please enter your name.';
   } elseif(!preg_match('/^[a-zA-Z\s]+$/', $name)){
      $message[] = 'Name should only contain letters and spaces.';
   }
   elseif(strlen($name) > 10){
      $message[] = 'Name should not exceed 10 letters.';
   }
        
         // Validation for number (phone number)
         if (empty($number)) {
            $message[] = 'Please enter your number.';
         } elseif (!preg_match("/^[0-9]{10}$/", $number)) {
            $message[] = 'Invalid number format. Please enter a 10-digit phone number.';
         }

         // Validate email
   if(empty($email)){
      $message[] = 'Please enter your email.';
   } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $message[] = 'Please enter a valid email address.';
   }


        // Validation for address
$address_fields = ['flat', 'street', 'city', 'country'];
foreach ($address_fields as $field) {
   if (empty($_POST[$field])) {
      $message[] = 'Please enter ' . str_replace('_', ' ', $field) . '.';
   } else if (!preg_match("/^[a-zA-Z ]+$/", $_POST[$field])) {
      $message[] = 'Invalid ' . str_replace('_', ' ', $field) . '. Only characters are allowed.';
   }
}

// Validation for pin_code
if (empty($_POST['pin_code'])) {
   $message[] = 'Please enter pin code.';
} else if (!preg_match("/^[0-9]{6}$/", $_POST['pin_code'])) {
   $message[] = 'Invalid pin code. Please enter a 6-digit number.';
}




// Validation for state
if (empty($_POST['state'])) {
   $message[] = 'Please enter state.';
} else if (!preg_match("/^[a-zA-Z ]+$/", $_POST['state'])) {
   $message[] = 'Invalid state. Only characters are allowed.';
}


// Validation for country
if (empty($_POST['country'])) {
   $message[] = 'Please enter country.';
} else if (!preg_match("/^[a-zA-Z ]+$/", $_POST['country'])) {
   $message[] = 'Invalid country. Only characters are allowed.';
}


         if (empty($message)) {
            mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
            $message[] = 'Order placed successfully!';
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         }
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
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>checkout</h3>
      <p> <a href="home.php">home</a> / checkout </p>
   </div>

   <section class="display-order">

      <?php
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if (mysqli_num_rows($select_cart) > 0) {
         while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
      ?>
            <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo '$' . $fetch_cart['price'] . '/-' . ' x ' . $fetch_cart['quantity']; ?>)</span> </p>
      <?php
         }
      } else {
         echo '<p class="empty">Your cart is empty.</p>';
      }
      ?>
      <div class="grand-total"> grand total : <span>Rs.<?php echo $grand_total; ?>/-</span> </div>

   </section>

   <section class="checkout">

      <form action="" method="post">
         <h3>place your order</h3>
         <div class="flex">
            <div class="inputBox">
               <span>your name :</span>
               <input type="text" name="name" required placeholder="Enter your name" maxlength="10">
            </div>
            <div class="inputBox">
               <span>your number :</span>
               <input type="number" name="number" required placeholder="enter your number" class="box" maxlength="10">
            </div>
            <div class="inputBox">
               <span>your email :</span>
               <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="inputBox">
               <span>payment method :</span>
               <select name="method">
                  <option value="cash on delivery">cash on delivery</option>
                  <option value="credit card">credit card</option>
                  <option value="paypal">paypal</option>
                  <option value="paytm">paytm</option>
               </select>
            </div>
            <div class="inputBox">
               <span>Flat Number :</span>
               <input type="text" name="flat" required placeholder="e.g. flat no." maxlength="4">
            </div>
            <div class="inputBox">
               <span>address :</span>
               <input type="text" name="street" required placeholder="e.g. street name" maxlength="30">
            </div>
            <div class="inputBox">
               <span>city :</span>
               <input type="text" name="city" required placeholder="e.g. city name" maxlength="10">
            </div>
            <div class="inputBox">
               <span>state :</span>
               <input type="text" name="state" required placeholder="e.g. state name" maxlength="10">
            </div>
            <div class="inputBox">
               <span>country :</span>
               <input type="text" name="country" required placeholder="e.g. country name" maxlength="10">
            </div>
            <div class="inputBox">
               <span>pin code :</span>
               <input type="number" name="pin_code" required placeholder="e.g. pin code" maxlength="6">
            </div>
         </div>
         <input type="submit" value="order now" class="btn" name="order_btn">
      </form>

   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
