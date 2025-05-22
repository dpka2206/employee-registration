<?php
session_start();
require 'database.php';

if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin.php");
    exit;
}

$errors = [];
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $user_type = $_POST['user_type'];
    $profile_pic = $user['profile_pic'];

    if (!$username || !$email || !$user_type) {
        $errors[] = "All fields are required.";
    }

    // Handle profile picture
    if ($_FILES['profile_pic']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($_FILES['profile_pic']['type'], $allowed) && $_FILES['profile_pic']['size'] <= 5 * 1024 * 1024) {
            $newPic = 'uploads/' . basename($_FILES['profile_pic']['name']);
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $newPic);
            if ($user['profile_pic'] !== 'uploads/default.png' && file_exists($user['profile_pic'])) {
                unlink($user['profile_pic']);
            }
            $profile_pic = $newPic;
        } else {
            $errors[] = "Invalid image type or size (only JPG/PNG < 5MB).";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, user_type=?, profile_pic=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $user_type, $profile_pic, $id);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User</title>
    <style>
        body {
            background: #121212;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
        }
        form {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            margin: auto;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: #2a2a2a;
            color: white;
            border: 1px solid #444;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        .error {
            color: red;
            background: #2a2a2a;
            padding: 10px;
            border-radius: 5px;
        }
        .submit-btn {
            background: #4CAF50;
            border: none;
            color: white;
            padding: 10px;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center;">Update User</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>User Type</label>
        <select name="user_type" required>
            <option value="admin" <?= $user['user_type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="user" <?= $user['user_type'] == 'user' ? 'selected' : '' ?>>User</option>
        </select>

        <label>Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*">
        <p>Current: <img src="<?= $user['profile_pic'] ?>" width="40" style="border-radius:50%; vertical-align:middle;"></p>

        <input type="submit" value="Update User" class="submit-btn">
    </form>

    <a href="admin.php" class="back-link">← Back to Dashboard</a>
</body>
</html>
