<?php

if (empty($_POST ["name"]) ){
    die("Name is required!");
}
if ( ! filter_var ($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid Email is required");
}

if (strlen($_POST["password"]) < 8) {
    die ("Password must be at least 8 Characters");
}
if (! preg_match("/[a-z]/i", $_POST["password"])){
    die("Password must contatin atleast one letter");
}
if (! preg_match("/[0-9]/i", $_POST["password"])){
    die("Password must contatin at least one number");
}
if ($_POST["password"] != $_POST["confirm-password"]) {
    die("Password must match");
}

$mysqli = require  __DIR__ . "/database.php";

// Check if email is duplicate.
$sql = sprintf("SELECT * FROM users WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));
$result = $mysqli->query($sql);
$user = $result->fetch_assoc();

if ($user) {
    die("Email already taken");
    return;
}







$password_hash = password_hash( $_POST["password"], PASSWORD_DEFAULT);



$sql = "INSERT INTO users (fullname, email, password_hash)
        VALUES (?, ?, ?)";
        
$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash);
                  
                  
if ($stmt->execute()) {

    
    header("Location: signup-success.html");
    exit;
                    
} else {
                    
if ($mysqli->errno === 1062) {
    die("email already taken");
} else {
    die($mysqli->error . " " . $mysqli->errno);
        }
}
