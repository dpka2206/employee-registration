<?php

include 'database.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD']=='POST'){
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email']) ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    $profile_pic = '';



if (empty($username) || empty($email) || empty($password) || empty($confirm_password)){
    if (empty($username)){
        $errors[] = "Please enter user name ";
    }
    if (empty($email)){
        $errors[] = "Please enter email ";
    }
    if (empty($password)){
        $errors[] = "Please enter password ";
    }
    if (empty($confirm_password)){
        $errors[] = "Please enter confirm password ";
    }
    else{
        $errors[] = "Please enter the values ";
    }
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $errors[] = "invalid email format";
}

if ($password !== $confirm_password){
    $errors[] = "Passwords do not match";
}



$check_stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($check_stmt, "s", $email);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);
if (mysqli_num_rows($result) > 0) {
    $errors[] = "Email address already exists";
}



if ($_FILES['profile_pic']['error']== 0){
    $allowed = ['image/jpeg', 'image/jpg' , 'image/png'];
    if (($_FILES['profile_pic']['size'] < 5 * 1024 * 1024 )&& in_array($_FILES['profile_pic']['type'], $allowed)){
        $profile_pic = 'uploads/' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }else{
        $errors[] = "Invalid profile picture (only jpg,jpeg or png & <5mb";
    }
}else{
    $errors[] = "Please upload the profile pic";
}

//no errors

if (empty($errors)){
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, user_type, profile_pic) values (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt , "sssss" , $username , $email, $hashed_password, $user_type , $profile_pic);
    mysqli_stmt_execute($stmt);
    header("Location: login.php");
    exit();
}
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #e0e0e0;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #00ff88;
            margin-bottom: 30px;
        }

        form {
            background: #1e1e1e;
            padding: 25px;
            border-radius: 12px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.2);
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 6px;
            background: #2a2a2a;
            color: #e0e0e0;
            font-size: 15px;
        }

        input:focus, select:focus {
            outline: none;
            background: #333;
            box-shadow: 0 0 0 2px #00ff88;
        }

        input[type="submit"] {
            background-color: #00ff88;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #00cc70;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
            color: #ccc;
        }

        .error {
            background: #2a0000;
            border-left: 5px solid #ff4444;
            padding: 10px;
            margin-bottom: 15px;
            color: #ffaaaa;
            border-radius: 6px;
        }

        a {
            color: #00ff88;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>
    <h2 style="text-align:center;">Employee Registration</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <?php
        if (!empty($errors)) {
            echo '<div class="error"><ul>';
            foreach ($errors as $e) echo "<li>$e</li>";
            echo '</ul></div>';
        }
        ?>
        <input type="text" name="username" placeholder="Employee Username" required>
        <input type="email" name="email" placeholder="Company Email" required>
        <select name="user_type" required>
            <option value="">Select Employee Type</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <label>Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*" required>
        <input type="submit" value="Register">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</body>
</html>