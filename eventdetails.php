<?php
//Initializing connection to MySQL database. Includes credentials and creates database connection in db_connection.php, all outside of root document. Also contains the session_start().
require_once("includes/db_connection.php");

//Uses value in URL to load correct event information from database.
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	if(!empty($_GET['event_id'])) {
		//Query to get event information.

		$query = "SELECT events.*, categories.name AS category, towns.name AS town FROM events "
        . "LEFT JOIN categories ON categories.id = events.category_id "
        . "LEFT JOIN towns ON towns.id = events.town_id "
        . "WHERE event_id = " . $_GET['event_id'];
		$result = db_select($query);
	}
} else {
	//Kills page if no correct code for any product was provided in URL. (i.e. from direct access to page)
	die("Something went wrong. Please try again later.");
}
?>

<html>
    <head>
        <title>Surrey Events</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <?php
        //Adds the header.
        require('includes/header.php');
        ?>

		<?php

		//Kills page if data could not be attained.
		if (!$result) {
			die("No results were attained. Please try again later.");
		} else {
			//Using associative array from result, populate table columns with corresponding information.
			$array = mysqli_fetch_assoc($result);
			echo "<h1>" . $array["event_title"] . "</h1>"; //Heading of page.

			echo "<div class='eventImg'>";
			
			if ($array["img_url"] != "") echo "<img src=" . $array["img_url"] . "></img>";
			else echo "<img src='assets/placeholder.png'></img>";

			echo "</div>";
			echo "<table class='details-table'>";
			echo "<tr>";
				echo "<th>Category</th>";
				echo "<th>Town</th></tr>";
				echo "<tr><td>".$array["category"]."</td>";
				echo "<td>".$array["town"]."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<th>Start Date</th>";
				echo "<th>End Date</th></tr>";
				$startdateformat = date("l jS \of F Y", strtotime($array['start_date']));
				echo "<tr><td>".$startdateformat."</td>";
				$enddateformat = date("l jS \of F Y", strtotime($array['end_date']));
				echo "<td>".$enddateformat."</td>";
			echo "</tr>";
			echo "</table>";

			echo "<table class='details-table'>";
			echo "<tr><td>";
			echo "<h3>Address</h3>";

			if ($array["address"] != "") echo "<p>".$array["address"]."</p>";
			else echo "<p>No address available.</p>";

			echo "<h3>Description</h3>";
			echo "<p>".$array["description"]."</p>";
			echo "</td></tr>";
			echo "<tr><td style='padding-top: 15px;'>";

			//Website button.
			if ($array["website_url"] != "") {
				echo "<a class='button website-button' href=" . $array["website_url"] . " target='_blank'>Website</a>";
			} else {
				echo "<a class='button dead-button'>Website link unavailable.</a>";
			}
			//Add to bookmarks button.
			echo '<a href="addtobookmarks.php" class="button bookmark-button">Bookmark</a>';

			echo "</td></tr>";
			echo "</table>";

			//Saves this product code to session so addtowatchlist.php can make a new watchlist item with it, even after having to login or register.
			//Won't cause a mistaken entry because button to add to watchlist only ever appears after this statement.
			$_SESSION['event_viewed'] = $array["event_id"];

			//Frees results and closes the connection to the database.
			$result->free_result();
		}
		?>

		<?php
        //Adds the footer.
        require('includes/footer.php');
        ?>
    </body>
</html>
