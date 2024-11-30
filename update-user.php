<?php
session_start();

// Ensure the user is logged in
if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";

    // Fetch user data
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    // Redirect if user is not logged in
    header("Location: index.php");
    exit;
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["submit"])) {
        $fullname = $_POST["fullname"];
        $email = $_POST["email"];
        $password = !empty($_POST["password"]) ? password_hash($_POST["password"], PASSWORD_DEFAULT) : null;

        // Update query
        if ($password) {
            $sql = "UPDATE users SET fullname = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssi", $fullname, $email, $password, $_SESSION["user_id"]);
        } else {
            $sql = "UPDATE users SET fullname = ?, email = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssi", $fullname, $email, $_SESSION["user_id"]);
        }

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            die("Error: " . $mysqli->error);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <?php if (isset($user)): ?>
    <h1>Update User</h1>
        <form method="post">
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
            
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            
            <label for="password">New Password (optional)</label>
            <input type="password" name="password" id="password">

            <button type="submit" name="submit">Update User</button>
        </form>
    <?php else: ?>
        <?php header("Location: index.php"); ?>
    <?php endif; ?>
</body>
</html>
