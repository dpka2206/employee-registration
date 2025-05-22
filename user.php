<?php
session_start();
require 'database.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];
$user = $conn->query("SELECT * FROM users WHERE email = '$email'")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard {
            background-color: #1f1f1f;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
            max-width: 400px;
        }
        img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #4caf50;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 10px;
        }
        p {
            margin: 5px 0;
        }
        .logout {
            margin-top: 20px;
            background: #e53935;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .logout:hover {
            background: #c62828;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="dashboard">
        <img src="<?= $user['profile_pic'] ?>" alt="Profile Picture">
        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>User Type:</strong> <?= htmlspecialchars($user['user_type']) ?></p>

        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

</body>
</html>
