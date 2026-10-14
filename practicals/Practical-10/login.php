<?php

require_once "auth.php";

$message = "";

if (isset($_SESSION['login_error'])) {

    $message = $_SESSION['login_error'];

    unset($_SESSION['login_error']);
}

if (isset($_GET['timeout'])) {

    $message = "Session expired. Please login again.";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username == "" || $password == "") {

        $message = "Please enter username and password.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE username = ?"
        );

        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            /* Prevent session fixation */
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();


            /* Update last login */
            $update = $pdo->prepare(
                "UPDATE users SET last_login = NOW() WHERE id = ?"
            );

            $update->execute([$user['id']]);


            /* Remember Me */
            if (isset($_POST['remember'])) {

                $token = bin2hex(random_bytes(32));

                $token_hash = hash('sha256', $token);

                $expires = date(
                    "Y-m-d H:i:s",
                    time() + (30 * 24 * 60 * 60)
                );

                $stmt = $pdo->prepare("
                    INSERT INTO remember_tokens
                    (user_id, token_hash, expires_at)
                    VALUES (?, ?, ?)
                ");

                $stmt->execute([
                    $user['id'],
                    $token_hash,
                    $expires
                ]);


                setcookie(
                    "remember_token",
                    $token,
                    [
                        "expires" => time() + (30 * 24 * 60 * 60),
                        "path" => "/",
                        "httponly" => true,
                        "samesite" => "Lax"
                    ]
                );
            }


            header("Location: dashboard.php");
            exit;

        } else {

            $_SESSION['login_error'] = "Invalid username or password.";
            header("Location: login.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Secure Login</title>

   <style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #eaf4ff, #f5f9ff);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}


/* Login Box */
.login-box {
    width: 450px;
    padding: 45px 40px;
    background: #ffffff;

    border-radius: 16px;

    box-shadow:
        0 10px 30px rgba(0, 70, 140, 0.12),
        0 2px 8px rgba(0, 0, 0, 0.06);

    border: 1px solid #e5eef8;
}


/* Heading */
.login-box h2 {
    margin: 0 0 32px;

    text-align: center;

    font-size: 38px;
    font-weight: 700;

    color: #064b8d;
}


/* Labels */
.login-box label {
    display: block;
    margin-bottom: 9px;
    font-size: 19px;
    font-weight: 400;
    color: #111111;
}


/* Input Fields */
.login-box input[type="text"],
.login-box input[type="email"],
.login-box input[type="password"] {
    width: 100%;
    height: 45px;
    padding: 0 16px;
    margin-bottom: 24px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    font-size: 17px;
    outline: none;
    transition: all 0.25s ease;
}

.login-box input[type="text"]:focus,
.login-box input[type="email"]:focus,
.login-box input[type="password"]:focus {

    border-color: #0866b6;
    box-shadow: 0 0 0 3px rgba(8, 102, 182, 0.12);
    background: #fbfdff;
}

.login-box button {
    width: 100%;
    height: 50px;
    margin-top: 2px;
    border: none;
    border-radius: 8px;
    background: #075294;
    color: white;
    font-size: 20px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.25s ease;
}

.login-box button:hover {
    background: #063f73;
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(7, 82, 148, 0.25);
}

.login-box button:active {
    transform: translateY(0);
}

.login-box p {
    margin-top: 25px;
    text-align: center;
    font-size: 18px;
    color: #222222;
}


.login-box a {
    color: #075294;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}


.login-box a:hover {
    color: #063f73;
    text-decoration: underline;
}

.message {
    padding: 10px;
    margin-bottom: 18px;
    border-radius: 6px;
    background: #fff1f1;
    color: #c62828;
    text-align: center;
}

@media (max-width: 600px) {

    .login-box {
        width: 90%;
        padding: 35px 25px;
    }
    .login-box h2 {
        font-size: 32px;
    }

}
</style>
</head>

<body>

<div class="login-box">

    <h2>Secure Login</h2>

    <?php if ($message != ""): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <label>Username</label>

        <input
            type="text"
            name="username"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >


        <label>

            <input
                type="checkbox"
                name="remember"
                style="width:auto;"
            >

            Remember Me

        </label>


        <button type="submit">
            Login
        </button>

    </form>

</div>

</body>
</html>