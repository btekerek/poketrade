<?php
session_start();

function saveUserData($newUser)
{
    $users = json_decode(file_get_contents('users.json'), true) ?: [];
    $users[] = $newUser;
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}

function getUserData($username)
{
    $users = json_decode(file_get_contents('users.json'), true);
    if ($users === null) {
        return null;
    }
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $errors = [];

    if (empty($username)) $errors[] = "Username is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match";

    $existingUsers = json_decode(file_get_contents('users.json'), true) ?: [];
    foreach ($existingUsers as $user) {
        if ($user['username'] === $username || $user['email'] === $email) {
            $errors[] = "Username or email already exists";
            break;
        }
    }

    if (count($errors) == 0) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $startingMoney = 600; 
        $newUser = [
            'username' => $username, 
            'password' => $hashedPassword, 
            'email' => $email, 
            'money' => $startingMoney, 
            'cards' => []
        ];
        saveUserData($newUser);

        $_SESSION['user'] = $username;
        if($username === 'admin') {
            header('Location: admin.php');
            exit();
        } else {
            header('Location: user.php');
            exit();
        }
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userData = getUserData($username);

    if ($userData) {
        if (password_verify($password, $userData['password'])) {
            if ($username === 'admin') {
                $_SESSION['admin'] = true;
                header('Location: admin.php');
            } else {
                $_SESSION['user'] = $username;
                header('Location: main.php');
            }
            exit();
        } else {
            $loginError = "Invalid username or password";
        }
    } else {
        $loginError = "User not registered. Please register first.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Auth - Pokémon Card Trading</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Login or Register to See All Pokémon Cards</h1>
        
    </header>
    
    <nav>
        <a href="index.php">Browse</a>
        <a class="active" href="<?php echo $authPage; ?>">Login | Register</a>
    </nav>
    <section id="auth-forms">

        <div class="form-container login-container">
            <h2>Login</h2>
            <?php if (isset($loginError)) : ?>
                <p class="error"><?php echo $loginError; ?></p>
                <?php endif; ?>
                <form method="post">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
        <div class="form-container register-container">
        <h2>Register</h2>
        <?php if (!empty($errors)) : ?>
            <div class="error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $_POST['username'] ?? ''; ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <br>
            <button type="submit" name="submit">Register</button>
        </form>
    </div>
                
                
            </section>
        </body>

</html>