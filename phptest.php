<?php
	include 'lib/database.php';

	/*
	 * PHP Security Test
	 * Find as many security holes within this script as possible and explain how you would fix them
	 * This script is a very simple message board, at this point the user is already logged in
	 */

/* MB PHP TEST
* cookies shouldn't be used for storing user names or passwords. a random token should be used
*/ 
	/* Check taht the user is logged in */
	$userQuery = $db->prepare("SELECT * FROM `users` WHERE `users`.`id` = :id");
	$userQuery->bindParam(":id", $_COOKIE["user_id"]);
	$userQuery->execute();

	$user = $userQuery->fetch(PDO::FETCH_ASSOC);

	if (!isset($user["id"])) {
		echo "You are not logged in!";
		exit;
	}

/* MB PHP TEST
* raw POST vars should not be used as it's an SQL injection risk. Prepared statements should be used
*/ 
	/* Now we know the user is logged in, insert the message to the database */
	$subject = $_POST["subject"];
	$message = $_POST["message"];
	$userID = $user["id"];

	$insertSQL = "INSERT INTO `messages` (`id`, `subject`, `message`, `user`, `date`) VALUES (NULL, $subject, $message, $userID, NOW())";

	$db->query($insertSQL);

	/* Display the new updated list */
	$messageQuery = $db->prepare("SELECT * FROM `messages` INNER JOIN `users` ON (`messages`.`user` = `users`.`id`) ORDER BY `date` DESC LIMIT 10");
	$messageQuery->execute();

	$messages = $messageQuery->fetchAll(PDO::FETCH_ASSOC);

/* MB PHP TEST
* this is an XSS risk. htmlspecialchars should be used to avoid script injection exploites
*/
	foreach ($messages as $message) {

		echo '<h2>' . $message["subject"] . ' <small>' . $message["user_name"] . '</small></h2>';
		echo '<p>' . $message["message"] . '</p>';
		echo '<hr />';

	}

?>