<?php
session_start();
require 'database.php';

if (!isset($_SESSION['email']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $res = $conn->query("SELECT profile_pic FROM users WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        $pic = $row['profile_pic'];
        if ($pic !== 'uploads/default.png' && file_exists($pic)) {
            unlink($pic);
        }
    }
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: admin.php");
    exit;
}


$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            background: #121212;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
        }
        h1 { text-align: center; color: #4CAF50; margin-bottom: 30px; }

        .admin-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .admin-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1e1e1e;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #333;
        }

        th {
            background-color: #222;
            color: #4CAF50;
        }

        tr:hover {
            background-color: #2a2a2a;
        }

        a.btn {
            padding: 5px 10px;
            margin: 0 3px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .view-btn { background: #2196F3; color: white; }
        .edit-btn { background: #FFC107; color: black; }
        .delete-btn { background: #f44336; color: white; }

        .logout {
            display: inline-block;
            margin-top: 30px;
            text-align: center;
        }

        .logout a {
            color: #f44336;
            text-decoration: none;
        }

        /* Popup */
        .popup {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            align-items: center;
            justify-content: center;
        }

        .popup-content {
            background: #222;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 300px;
            animation: scaleIn 0.3s ease;
            color: #fff;
        }

        .popup-content img {
            width: 80px; height: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        @keyframes scaleIn {
            from { transform: scale(0.7); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes scaleOut {
            from { transform: scale(1); opacity: 1; }
            to { transform: scale(0.7); opacity: 0; }
        }

        .close-btn {
            margin-top: 15px;
            color: #f44336;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <div class="admin-info">
        <img src="<?php echo $_SESSION['profile_pic']; ?>" alt="Admin">
        <p><?php echo $_SESSION['email']; ?> | Admin</p>
    </div>

    <table>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>User Type</th><th>Profile Pic</th><th>Actions</th>
        </tr>
        <?php while($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['user_type'] ?></td>
            <td><img src="<?= $u['profile_pic'] ?>" width="40" height="40" style="border-radius:50%;"></td>
            <td>
                <a href="#" class="btn view-btn" onclick="showPopup('<?= htmlspecialchars($u['username']) ?>', '<?= htmlspecialchars($u['email']) ?>', '<?= $u['user_type'] ?>', '<?= $u['profile_pic'] ?>')">View</a>
                <a href="update.php?id=<?= $u['id'] ?>" class="btn edit-btn">Update</a>
                <a href="admin.php?delete=<?= $u['id'] ?>" class="btn delete-btn" onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

    <!-- Popup -->
    <div class="popup" id="popup">
        <div class="popup-content" id="popupContent">
            <img id="popupImg" src="" alt="User Pic">
            <h3 id="popupName"></h3>
            <p id="popupEmail"></p>
            <p id="popupType"></p>
            <div class="close-btn" onclick="closePopup()">Close</div>
        </div>
    </div>

    <script>
        function showPopup(name, email, type, img) {
            document.getElementById('popupName').textContent = name;
            document.getElementById('popupEmail').textContent = email;
            document.getElementById('popupType').textContent = "Role: " + type;
            document.getElementById('popupImg').src = img;
            document.getElementById('popup').style.display = 'flex';
        }

        function closePopup() {
            const popup = document.getElementById('popup');
            const content = document.getElementById('popupContent');
            content.style.animation = 'scaleOut 0.3s ease';
            setTimeout(() => {
                popup.style.display = 'none';
                content.style.animation = 'scaleIn 0.3s ease';
            }, 250);
        }

        window.onclick = function(e) {
            const popup = document.getElementById('popup');
            const content = document.getElementById('popupContent');
            if (e.target == popup) {
                closePopup();
            }
        }
    </script>
</body>
</html>
