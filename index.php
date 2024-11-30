<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Query all users
    $sql = "SELECT * FROM users";
    $result = $mysqli->query($sql);
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["delete"])) {
        $userIdToDelete = $_POST["user_id"];

        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userIdToDelete);
        if ($stmt->execute()) {
            // Redirect to the same page
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        } else {
            echo "Error: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Home</h1>
    <?php if (isset($user)): ?>
        <p>Hello <b><?= htmlspecialchars($user["fullname"]) ?></b></p>
        <?php if ($user["fullname"] == "admin"): ?>
            <a href= "add-user.php">Add User</a>
            <h2>Users</h2>
            <table>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user["id"]) ?></td>
                        <td><?= htmlspecialchars($user["fullname"]) ?></td>
                        <td><?= htmlspecialchars($user["email"]) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                        <td>
                            <a href="update-user.php?user_id=<?= $user["id"] ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php endif; ?>
        <p><a href="logout.php">Log out</a></p>
    <?php else: ?>
        <p><a href="login.php">Log in</a> or <a href="signup.html">sign up</a></p>
    <?php endif; ?>
</body>
</html>
