<?php
require_once 'config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    $found = $user->login(trim($_POST['username']), $_POST['password']);

    if ($found) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $found['id'];
        $_SESSION['username'] = $found['username'];
        $_SESSION['role'] = $found['role'];

        // admin is not tracked
        if ($found['role'] == 'admin') {
            header('Location: admin.php');
            exit;
        }

        // normal users are tracked
        $visit = new Visit();
        $_SESSION['visit_id'] = $visit->start($found['id']);
        header('Location: dashboard.php');
        exit;
    }
    $message = 'Wrong username or password.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            background: white;
            width: 350px;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            font-size: 14px;
        }

        input:focus {
            border-color: #2563eb;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            color: red;
            text-align: center;
        }

        p {
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <h2>Login</h2>

        <p class="message"><?= $message ?></p>

        <form method="post">

            <input type="text" name="username"
                   placeholder="Username" required>
            <br><br>

            <input type="password" name="password"
                   placeholder="Password" required>
            <br><br>

            <button type="submit">Login</button>

        </form>

        <p>
            <a href="register.php">Create an account</a>
        </p>

    </div>

</body>
</html>