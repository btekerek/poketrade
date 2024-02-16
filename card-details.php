<?php
session_start();

$pokemonCards = json_decode(file_get_contents('cards_data.json'), true);
$cardName = $_GET['name'] ?? '';
$username = $_SESSION['user'] ?? null; 
$cardDetails = null;

if (is_array($pokemonCards)) { 
    foreach ($pokemonCards as $card) {
        if ($card['name'] === $cardName) {
            $cardDetails = $card;
            break;
        }
    }
}

function getUserDetails($username)
{
    $users = json_decode(file_get_contents('users.json'), true);
    if (is_array($users)) { 
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
    }
    return null;
}

$userDetails = $username ? getUserDetails($username) : null; 

if (!$cardDetails) {
    echo "Card not found";
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pokémon Card</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Welcome to Pokémon Card Trading</h1>
    </header>
    <nav>
        <a class="active" href="index.php">Browse</a>
        <a href="auth.php">Login | Register</a>
    </nav>

    <section id="card-details" class="<?php echo htmlspecialchars($typeClass); ?>">
        <a href="index.php">Back to list</a>
        <a href="card-details.php?name=<?php echo urlencode($card['name']); ?>" class="card <?php echo 'type-' . strtolower($card['type']); ?>">

            <img src="<?php echo $cardDetails['image']; ?>" alt="<?php echo $cardDetails['name']; ?>">
            <p>Type: <?php echo $cardDetails['type']; ?></p>
            <p>HP: <?php echo $cardDetails['hp']; ?></p>
            <p>Attack: <?php echo $cardDetails['attack']; ?></p>
            <p>Defense: <?php echo $cardDetails['defense']; ?></p>
            <p>Price: <?php echo $cardDetails['price']; ?></p>
            <p>Description: <?php echo $cardDetails['description']; ?></p>
        </a>
    </section>


</body>

</html>