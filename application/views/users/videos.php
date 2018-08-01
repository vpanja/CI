<!DOCTYPE html>
<html lang="en">  
<head>
<link href="<?php echo base_url(); ?>assets/css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>
    <p><a href="<?php echo base_url(); ?>users/logout">Logout</a></p>
<div class="container">
    <?php
    if(isset($userVideos)){
        echo '<p class="statusMsg">Your Videos</p><br>';
        foreach($userVideos as $videoID){
            echo "<iframe id='player' type='text/html' width='320' height='200'
    src='http://www.youtube.com/embed/".$videoID['video_id']."?enablejsapi=1&origin=http://example.com' frameborder='0'></iframe><br><br>";
        }
    }else{
        echo "<p>".$video_message."</p>";
    }
    echo "<a href='".base_url()."users/fetchVideos'>Fetch Videos</a> From Youtube.";
    ?>    
</div>
</body>
</html>