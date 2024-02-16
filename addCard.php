<?php
session_start();
$errorMsg = "";
$pokemonCards = json_decode(file_get_contents('cards_data.json'), true);

function getUserOwnedCards($username)
{
    $allCards = json_decode(file_get_contents('cards_data.json'), true);
    $userOwnedCards = [];

    foreach ($allCards as $card) {
        if ($card['owner'] === $username) {
            $userOwnedCards[] = $card;
        }
    }

    return $userOwnedCards;
}

function getUserDetails($username)
{
    $users = json_decode(file_get_contents('users.json'), true);
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}
 
if (isset($_POST['addCard'])) {
    $name = $_POST['name'];
    $image = $_POST['image'];
    $type = $_POST['type'];
    $hp = $_POST['hp'];
    $attack = $_POST['attack'];
    $defense = $_POST['defense'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $pokemonCards = json_decode(file_get_contents('cards_data.json'), true);

    $newCard = [
        'name' => $name,
        'image' => $image,
        'type' => $type,
        'hp' => $hp,
        'attack' => $attack,
        'defense' => $defense,
        'price' => $price,
        'description' => $description,
        'owner' => 'admin',
    ];

    $pokemonCards[] = $newCard;


    file_put_contents('cards_data.json', json_encode($pokemonCards));
    header('Location: admin.php');
    exit;
}

$ownedCards = getUserOwnedCards('admin');
$userDetails = getUserDetails('admin');
function getTypeEmoji($type)
{
    $emojis = [
        'fire' => '🔥',
        'bug' => '🐛',
        'water' => '💧',
        'electric' => '⚡',
        'grass' => '🌿',
        'normal' => '🙂',
        'poison' => '☠️',
        'psychic' => '🔮',
        'metal' => '🛠️',
        'hp' => '❤️',
        'attack' => '⚔️',
        'defense' => '🛡️' ,
        'price' => '💰',
    ];

    return isset($emojis[$type]) ? $emojis[$type] : '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Pokémon Card Trading</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

</head>

<body>
    <header>
        <h1> Admin</h1>
    </header>
    <nav>
        <a  href="admin.php">Market</a>
        <a class="active"  href="addCard.php">Add New Card</a>
        <a href="logout.php">Logout</a>
    </nav>
 
        <form method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <label for="image">Image URL:</label>
            <input type="text" id="image" name="image" required>
            <br>
            <label for="type">Type:</label>
            <input type="text" id="type" name="type" required>
            <br>
            <label for="hp">HP:</label>
            <input type="number" id="hp" name="hp" required>
            <br>
            <label for="attack">Attack:</label>
            <input type="number" id="attack" name="attack" required>
            <br>
            <label for="defense">Defense:</label>
            <input type="number" id="defense" name="defense" required>
            <br>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <br>
            <button type="submit" name="addCard">Add Card</button>
        </form>
    </section>


</body>

</html>