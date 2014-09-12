<?php
// configuration
require("../includes/config.php");
// if form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// validate submission
	if(empty($_POST["username"]))
    {
        apologize("You must provide your username.");
    }
    elseif(empty($_POST["email"])){
        apologize("You must provide your email address.");
    }
    elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
    {
        apologize("Email is not valid");
    }
    elseif(empty($_POST["password"]))
    {
        apologize("You must provide your password.");
    }
    elseif(empty($_POST["confirmation"]))
	{
        apologize("You must provide your password.");
    }
	
	elseif($_POST["password"] != $_POST["confirmation"])
	{
		apologize("Passwords do not match!");
	}else{
        // query database for user
        $rowsuser = query("SELECT * FROM users WHERE username = ?", $_POST["username"]);
        $rowsemail = query("SELECT * FROM users WHERE email = ?", $_POST["email"]);

        // if we found name or email
        if (count($rowsuser) == 1)
        {
            apologize("Username already exists.");
        }elseif(count($rowsemail) == 1)
        {
            apologize("Email already exists.");
        }
    }
	
	$result = query("INSERT INTO users (username, hash, email) VALUES(?, ?, ?)", $_POST["username"], crypt($_POST["password"]), $_POST["email"]);
	
	if ($result === false)
	{
		apologize("An unexpected error occurred!");
	}
	else
	{
		$rows = query("SELECT LAST_INSERT_ID() AS id");
		$id = $rows[0]["id"];

		// remember that user's now logged in by storing user's ID in session
    	$_SESSION["id"] = $id;

    	// redirect to portfolio
    	redirect("/");
    }
}
else
{
// else render form
render("register_form.php", array("title" => "Register"));
}
?>