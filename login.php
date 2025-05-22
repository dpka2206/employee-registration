<?php
session_start();

include 'database.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)){
        if (empty($email)){
            $errors[] = "Please enter  email";
        }
        else{
            $errors[] = "Please enter  password";
        }
    }else{
        $stmt = $conn-> prepare("SELECT * FROM users WHERE email = ?");
        $stmt-> bind_param("s", $email);
        $stmt-> execute();
        $result = $stmt -> get_result();

        if ($result -> num_rows === 1 ){
            $user = $result-> fetch_assoc();

            if (password_verify($password, $user['password'])){
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['profile_pic'] = $user['profile_pic'];

                if($user['user_type'] === 'admin'){
                    header('Location: admin.php');
                }else{
                    header('Location: user.php');
                }exit;
            }else{
                $errors[] = "Incorrect Password";
            }
        }else{
            $errors = "Email is not registered";
        }
    }
}?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #1f1f1f; color: white; padding: 40px; }
        form { background: #2b2b2b; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(255,255,255,0.1); }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #444; color: white; border: none; border-radius: 6px; }
        input[type="submit"] { background: #4CAF50; cursor: pointer; transition: 0.3s; }
        input[type="submit"]:hover { background: #45a049; }
        .error { color: #ff6666; }
        a { color: #61dafb; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">User Login</h2>
    <form method="POST" action="">
        <?php
        if (!empty($errors)) {
            echo '<div class="error"><ul>';
            foreach ($errors as $e) echo "<li>$e</li>";
            echo '</ul></div>';
        }
        ?>
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <input type="submit" value="Login">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</body>
</html>
