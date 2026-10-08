<!DOCTYPE html>
<html>
<head>
    <title>Secure User Registration</title>
    <link rel="stylesheet" href="register.css">
</head>

<body>

<div class="register">

    <h1>User Registration</h1>

    <form action="register.php" method="POST">

        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required minlength="6">

        <button type="submit">Register</button>

    </form>

</div>

</body>
</html>
```
