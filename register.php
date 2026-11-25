<?php
require_once 'config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username == '' || strlen($password) < 6) {
        $message = 'Enter a username and a password of at least 6 characters.';
    } else {
        $user = new User();
        if ($user->register($username, $password)) {
            $message = 'Registered! You can now <a href="login.php">login</a>.';
        } else {
            $message = 'Username already taken.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

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

        .register-container {
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

    <div class="register-container">

        <h2>Register</h2>

        <p><?= $message ?></p>

        <form method="post">

            <input type="text" name="username"
                   placeholder="Username" required>
            <br><br>

            <input type="password" name="password"
                   placeholder="Password" required>
            <br><br>

            <button type="submit">Register</button>

        </form>

        <p>
            <a href="login.php">Already have an account? Login</a>
        </p>

    </div>

</body>
</html>
