<!-- Creating the like button - records and displays user likes -->

<?php  

require 'Config/config.php';
include("Includes/Classes/User.php");
include("Includes/Classes/Post.php");
include("Includes/Classes/Notification.php");

if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else {
	header("Location: register.php");
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <!-- Title and Favicon -->
	<title></title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="Assets/CSS/style.css">

</head>

<body>
    
    <style>

            * {
                font-family: Arial, Helvetica, Sans-serif;
                background-color: #ffffff;
            }

            form {
                position: absolute;
                top: 6px;
            }
            
    </style>

    <?php

        //Get id of post
        if(isset($_GET['post_id'])) {

            $post_id = $_GET['post_id'];

        }

        $get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id = '$post_id'");
        $row = mysqli_fetch_array($get_likes);

        // Recording user likes and who was liked
        $total_likes = $row['likes']; 
        $user_liked = $row['added_by'];

        // Retriving the account details of the person the user likes to add a like
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$user_liked'");
        $row = mysqli_fetch_array($user_details_query);
        $total_user_likes = $row['num_likes'];

        // Like button
        if(isset($_POST['like_button'])) {
            $total_likes++;                                                             // Post likes
            $query = mysqli_query($con, "UPDATE posts SET likes = '$total_likes' WHERE id = '$post_id'");

            $total_user_likes++;                                                        // User total likes
            $user_likes = mysqli_query($con, "UPDATE user SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");

            $insert_user = mysqli_query($con, "INSERT INTO likes VALUES ('', '$userLoggedIn', '$post_id')");

            // Insert notification
            if($user_liked != $userLoggedIn) {

                $notification = new Notification($con, $userLoggedIn);
                $notification->insertNotification($post_id, $user_liked, "like");

            }
        }

        // Unlike button - Pressing the like button again undoes a like
        if(isset($_POST['unlike_button'])) {
            $total_likes--;                                                             // Post likes
            $query = mysqli_query($con, "UPDATE posts SET likes = '$total_likes' WHERE id = '$post_id'");

            $total_user_likes--;                                                        // User total likes
            $user_likes = mysqli_query($con, "UPDATE user SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");

            $insert_user = mysqli_query($con, "DELETE FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");

        }

        // Check for previous likes
        $check_query = mysqli_query($con, "SELECT * FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
        $num_rows = mysqli_num_rows($check_query);

        if($num_rows > 0) {
            echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                    <input type="submit" class="comment_like" name="unlike_button" value="Unlike">
                    <div class="like_value">
                        '. $total_likes .' Likes
                    </div>
                </form>
            ';
        } else {
            echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                    <input type="submit" class="comment_like" name="like_button" value="Like">
                    <div class="like_value">
                        '. $total_likes .' Likes
                    </div>
                </form>
            ';
        }

    ?>

</body>

</html>