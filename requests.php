<?php

include("Includes/header.php");

?>

    <div class="main_column column" id="main_column">

        <h4> Friend Requests </h4><br>

        <?php

            $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
            if(mysqli_num_rows($query) == 0) {                              // If no friend requests
                echo "You have no friend requests at the this time";
            } else {
                while($row = mysqli_fetch_array($query)) {                  // If outstanding friend requests
                    $user_from = $row['user_from'];
                    $user_from_obj = new User($con, $user_from);

                    echo $user_from_obj->getFirstAndLastName() . " sent you a friend request."; // Name of person sending friend request

                    $user_from_friend_array = $user_from_obj->getFriendArray();

                    // These must be here are they must be unique to each friend request
                    if(isset($_POST['accept_request' . $user_from])) { 
                        $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$user_from,') WHERE username = '$userLoggedIn'");
				        $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$userLoggedIn,') WHERE username = '$user_from'");

                        $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                        
                        echo "You are now friends with " . $user_from_obj->getFirstAndLastName();
                        header("Location: requests.php");
                    }

                    if(isset($_POST['ignore_request' . $user_from])) {
                        $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                        
                        echo "Friend request ignored";
                        header("Location: requests.php");
                    }

        ?>

            <form action="requests.php" method="POST">

                <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">

            </form>

        <?php

                }   // End of WHILE $row = mysqli_fetch_array($query)

            }   // End of IF STATEMENT mysqli_num_rows($query) == 0)

        ?>

    </div>

</div>  <!-- Closed tag for wrapper div (in header) -->