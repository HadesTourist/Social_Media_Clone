<?php

// Search bar ajax call - Called from Demo.js - populates the search bar based upon user input

include("../../Config/config.php");
include("../../Includes/Classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

// Search check - if person searching uses "_" they are likely looking for a username
if(strpos($query, '-') !== false) {

    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 8");

// Search check - if person searching uses two words, assume they are first and last names
}   else if(count($names) == 2) {

    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%' ) AND user_closed = 'no' LIMIT 8");

// Search check - if person searchs for one word only search first names and last names
}   else  {

    $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%' ) AND user_closed = 'no' LIMIT 8");

}


if($query != "") {

    while($row = mysqli_fetch_array($usersReturnedQuery)) {

        $user = new User($con, $userLoggedIn);

        if($row['username'] != $userLoggedIn) {

            $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";

        }   else    {

            $mutual_friends = "";

        }

        echo "<div class='resultDisplay'>
				<a href='" . $row['username'] . "' style='color: #1485BD'>
					<div class='liveSearchProfilePic'>
						<img src='" . $row['profile_pic'] ."'>
					</div>

					<div class='liveSearchText'>
						" . $row['first_name'] . " " . $row['last_name'] . "
						<p>" . $row['username'] ."</p>
						<p id='grey'>" . $mutual_friends ."</p>
					</div>
				</a>
				</div>";

    }

}


?>