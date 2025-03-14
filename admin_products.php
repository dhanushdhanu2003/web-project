<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $image = $_FILES['image'];
   $image_folder = 'uploaded_img/';

   // Validate name
   if (empty($name)) {
      $message[] = 'Please enter the product name.';
   } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
      $message[] = 'Name should only contain letters and spaces.';
   }

   // Validate price
   if (empty($price) || !is_numeric($price) || $price < 0) {
      $message[] = 'Please enter a valid price.';
   }

   // Validate image
   if (empty($image['name'])) {
      $message[] = 'Please select an image.';
   } elseif (!in_array($image['type'], ['image/jpeg', 'image/png'])) {
      $message[] = 'Only JPG and PNG images are allowed.';
   } elseif ($image['size'] > 2000000) {
      $message[] = 'Image size is too large.';
   }

   // If no errors, proceed with adding the product
   if (empty($message)) {
      $image_name = uniqid() . '_' . $image['name'];
      $image_path = $image_folder . $image_name;

      if (move_uploaded_file($image['tmp_name'], $image_path)) {
         $add_product_query = mysqli_prepare($conn, "INSERT INTO `products` (name, price, image) VALUES (?, ?, ?)");
         mysqli_stmt_bind_param($add_product_query, "sds", $name, $price, $image_name);
         mysqli_stmt_execute($add_product_query);
         mysqli_stmt_close($add_product_query);

         $message[] = 'Product added successfully!';
      } else {
         $message[] = 'Failed to move the uploaded image.';
      }
   }
}

// Delete product
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_prepare($conn, "SELECT image FROM `products` WHERE id = ?");
   mysqli_stmt_bind_param($delete_image_query, "i", $delete_id);
   mysqli_stmt_execute($delete_image_query);
   mysqli_stmt_bind_result($delete_image_query, $image_name);
   mysqli_stmt_fetch($delete_image_query);
   mysqli_stmt_close($delete_image_query);

   if ($image_name) {
      unlink($image_folder . $image_name);
   }

   $delete_product_query = mysqli_prepare($conn, "DELETE FROM `products` WHERE id = ?");
   mysqli_stmt_bind_param($delete_product_query, "i", $delete_id);
   mysqli_stmt_execute($delete_product_query);
   mysqli_stmt_close($delete_product_query);

   header('location:admin_products.php');
   exit();
}

// Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_price = $_POST['update_price'];

   mysqli_query($conn, "UPDATE `products` SET name = '$update_name', price = '$update_price' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image'];
   $update_folder = 'uploaded_img/';
   $update_old_image = $_POST['update_old_image'];

   if (!empty($update_image['name'])) {
      if (!in_array($update_image['type'], ['image/jpeg', 'image/png'])) {
         $message[] = 'Only JPG and PNG images are allowed.';
      } elseif ($update_image['size'] > 2000000) {
         $message[] = 'Image size is too large.';
      } else {
         $update_image_name = uniqid() . '_' . $update_image['name'];
         $update_image_path = $update_folder . $update_image_name;

         if (move_uploaded_file($update_image['tmp_name'], $update_image_path)) {
            mysqli_query($conn, "UPDATE `products` SET image = '$update_image_name' WHERE id = '$update_p_id'") or die('query failed');
            unlink($update_folder . $update_old_image);
         } else {
            $message[] = 'Failed to move the uploaded image.';
         }
      }
   }

   header('location:admin_products.php');
   exit();
}

// Fetch all products
$select_products_query = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');

?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Products</title>

      <!-- font awesome cdn link  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

      <!-- custom admin css file link  -->
      <link rel="stylesheet" href="css/admin_style.css">
   </head>
   <body>
      <?php include 'admin_header.php'; ?>

      <!-- product CRUD section starts  -->
      <section class="add-products">
         <h1 class="title">Shop Products</h1>

         <form action="" method="post" enctype="multipart/form-data">
            <h3>Add Product</h3>
            <input type="text" name="name" class="box" placeholder="Enter product name" required maxlength="20">
            <input type="number" min="0" name="price" class="box" placeholder="Enter product price" required>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
            <input type="submit" value="Add Product" name="add_product" class="btn">
         </form>
      </section>
      <!-- product CRUD section ends -->

      <!-- show products  -->
      <section class="show-products">
         <div class="box-container">
            <?php if (mysqli_num_rows($select_products_query) > 0) {
               while ($fetch_products = mysqli_fetch_assoc($select_products_query)) { ?>
                  <div class="box">
                     <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                     <div class="name"><?php echo $fetch_products['name']; ?></div>
                     <div class="price">Rs.<?php echo $fetch_products['price']; ?>/-</div>
                     <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Update</a>
                     <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
                  </div>
            <?php }
            } else {
               echo '<p class="empty">No products added yet!</p>';
            } ?>
         </div>
      </section>

      <section class="edit-product-form">
         <?php if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');

            if (mysqli_num_rows($update_query) > 0) {
               while ($fetch_update = mysqli_fetch_assoc($update_query)) { ?>
                  <form action="" method="post" enctype="multipart/form-data">
                     <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                     <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                     <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                     <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter product name">
                     <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Enter product price">
                     <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                     <input type="submit" value="Update" name="update_product" class="btn">
                     <input type="reset" value="Cancel" id="close-update" class="option-btn" onclick="validateName()">
                  </form>
            <?php }
            }
         } else {
            echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
         } ?>
      </section>

      <!-- custom admin js file link  -->
      <script src="js/admin_script.js"></script>
   </body>
</html>
