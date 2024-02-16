<?php
session_start();
$errorMsg = "";
$pokemonCards = json_decode(file_get_contents('cards_data.json'), true);
$username = null;

if (isset($_SESSION['admin'])) {
    $username = $_SESSION['admin'];
} elseif (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
}
$userDetails = getUserDetails($username);

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
        'defense' => '🛡️',
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
        <a class="active" href="admin.php">Market</a>
        <a href="addCard.php">Add New Card</a>
        <a href="logout.php">Logout</a>
    </nav>

    <section id="card-list">
        <h2>Available Pokémon Cards</h2>
        <?php if (!empty($errorMsg)) : ?>
            <p class="error"><?php echo htmlspecialchars($errorMsg); ?></p>
        <?php endif; ?>
        <div class="cards-container">

            <?php foreach ($pokemonCards as $card) : ?>
                <?php if ($card['owner'] == 'admin') : ?>
                    <a href="card-details.php?name=<?php echo urlencode($card['name']); ?>" class="card <?php echo 'type-' . strtolower($card['type']); ?>">

                        <img src="<?php echo $card['image']; ?>" alt="<?php echo $card['name']; ?>">
                        <h2><?php echo $card['name']; ?></h2>
                        <h3><?php echo htmlspecialchars($card['type']) . ' ' . getTypeEmoji(strtolower($card['type'])); ?></h3>
                        <h3><?php echo $card['price'] . '' . getTypeEmoji(strtolower('price')); ?></h3>

                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>




</body>

</html>