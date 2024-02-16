<?php
session_start();
$pokemonCards = json_decode(file_get_contents('cards_data.json'), true);

if (isset($_SESSION['user'])) {
    $authPage = 'logout.php';
    $userDetailsPage = 'user.php';
} else {
    $authPage = 'auth.php';
    $userDetailsPage = 'auth.php';
}


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
        <title>Pokémon Card Trading</title>
        <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Welcome to Pokémon Card Trading</h1>
    </header>
    <nav>
        <a class="active" href="index.php">Browse</a>
        <a href="<?php echo $authPage; ?>">Login | Register</a>
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
                            <h3><?php echo $card['price'] . '' . getTypeEmoji(strtolower('price')) ; ?></h3>
                       
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
 
</body>

</html>