<!DOCTYPE html>
<html lang="en">  
<head>
<link href="<?php echo base_url(); ?>assets/css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>
    <p><a href="<?php echo base_url(); ?>users/logout">Logout</a></p>
<div class="container">
    <h2>User Account</h2>
    <h3>Welcome <?php echo $user['user_name']; ?>!</h3>
    <div class="account-info">
        <p><b>Name: </b><?php echo $user['user_name']; ?></p>
        <p><b>Email: </b><?php echo $user['user_email']; ?></p>
      <!--  <p><b>Phone: </b><?php echo $user['user_phone']; ?></p>
        <p><b>Gender: </b><?php echo $user['user_gender']; ?></p>-->
    </div>
<p>Show My Videos:<a href="<?php echo base_url(); ?>users/showUserVideos">Videos</a></p>    
</div>
</body>
</html>