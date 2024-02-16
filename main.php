<?php
session_start();
$errorMsg = "";
$pokemonCards = json_decode(file_get_contents('cards_data.json'), true);

if (isset($_SESSION['user'])) {
    $username = ($_SESSION['user']);
} else if (isset($_SESSION['admin'])) {
    $username = ($_SESSION['admin']);
} else {
    $username = null;
}


if (isset($_POST['buy'])) {
    $cardName = $_POST['cardName'];
    $username = $_SESSION['user'];

    $cards = json_decode(file_get_contents('cards_data.json'), true);
    $users = json_decode(file_get_contents('users.json'), true);

    $purchaseSuccessful = false;

    foreach ($cards as $key => $card) {
        if ($card['name'] == $cardName && $card['owner'] == 'admin') {
            foreach ($users as $userKey => $user) {
                if ($user['username'] == $username && $user['money'] >= $card['price'] && count($user['cards']) < 5) {
                    $users[$userKey]['money'] -= $card['price'];
                    $users[$userKey]['cards'][] = $card['name'];
                    $cards[$key]['owner'] = $username;
                    $purchaseSuccessful = true;
                    break;
                }
            }
            break;
        }
    }

    if (!$purchaseSuccessful) {
        $errorMsg = "Insufficient funds or you already have 5 cards.";
    } else {
        file_put_contents('cards_data.json', json_encode($cards));
        file_put_contents('users.json', json_encode($users));
        header('Location: main.php');
        exit();
    }
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
$userDetails = $username ? getUserDetails($username) : null;

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

$ownedCards = getUserOwnedCards($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>List Cards</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

</head>

<body>
    <header>

        <a href="user.php"><?php echo htmlspecialchars($userDetails['username']); ?></a>
        <i class="fa-solid fa-coins"><?php echo htmlspecialchars($userDetails['money']); ?></i>
        <nav>
            <a class="active" href="main.php">Market</a>
            <a href="user.php">User Details</a>
            <a href="logout.php">Logout</a>

        </nav>

    </header>

    <article id="content">

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
                            <?php if (isset($_SESSION['user'])) : ?>
                                <form method="post">
                                    <input type="hidden" name="cardName" value="<?php echo htmlspecialchars($card['name']); ?>">
                                    <button type="submit" name="buy">Buy</button>
                                </form>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php if (!empty($ownedCards)) : ?>
            <section id="inventory">

                <h2>Your Inventory</h2>

                <div id="owned-cards" class="cards-container">
                    <?php
                    $ownedCards = getUserOwnedCards($username);
                    foreach ($ownedCards as $card) :
                    ?>
                        <a href="card-details.php?name=<?php echo urlencode($card['name']); ?>" class="card card-small">
                            <img src="<?php echo $card['image']; ?>" alt="<?php echo htmlspecialchars($card['name']); ?>">
                            <h3><?php echo htmlspecialchars($card['name']); ?></h3>
                            <p><?php echo htmlspecialchars($card['type']) . ' ' . getTypeEmoji(strtolower($card['type'])); ?></p>
                            <p>Price: <?php echo htmlspecialchars($card['price']); ?></p>

                            <?php if ($username !== 'admin') : ?>
                                <form method="post" action="sellCard.php">
                                    <input type="hidden" name="cardName" value="<?php echo htmlspecialchars($card['name']); ?>">
                                    <button type="submit" name="sell">Sell</button>
                                </form>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </article>




</body>

</html>