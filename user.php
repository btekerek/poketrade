<?php
session_start();
$username = $_SESSION['user'] ?? null;

if (!$username) {
    header('Location: auth.php');
    exit();
} else {
    $username = $_SESSION['user'];
}

$pokemonCards = json_decode(file_get_contents('cards_data.json'), true);

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

$ownedCards = getUserOwnedCards($_SESSION['user']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($userDetails['username']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>




    <header>

        <a href="user.php"><?php echo htmlspecialchars($userDetails['username']); ?></a>
        <i class="fa-solid fa-coins"><?php echo htmlspecialchars($userDetails['money']); ?></i>

        <nav>
            <a href="main.php">Market</a>
            <?php if ($username === 'admin') : ?>
                <a href="addCard.php">Add New Card</a>
            <?php endif;     ?>
            <a class="active" href="user.php">User Details</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section id="user-cards">

        <div id="user-details">
            <?php if ($userDetails) : ?>
                <h2>Name: <?php echo htmlspecialchars($userDetails['username']); ?></h2>
                <i class="fa-solid fa-coins">: <?php echo htmlspecialchars($userDetails['money']); ?></i>
                <h3>Email: <?php echo htmlspecialchars($userDetails['email']); ?> </h3>
            <?php endif;     ?>
        </div>



        <div class="cards-container">
            <?php if (empty($ownedCards)) : ?>
                <p><a href="main.php">Click to see available cards</a></p>
            <?php else : ?>
                <h2>My Pokémon Cards</h2>
                <?php foreach ($ownedCards as $card) : ?>
                    <a href="card-details.php?name=<?php echo urlencode($card['name']); ?>" class="card">
                        <img src="<?php echo $card['image']; ?>" alt="<?php echo htmlspecialchars($card['name']); ?>">
                        <h3><?php echo htmlspecialchars($card['name']); ?></h3>
                        <p>Type: <?php echo htmlspecialchars($card['type']); ?></p>
                        <p>Price: <?php echo htmlspecialchars($card['price']); ?></p>
                        <form method="post" action="sellCard.php">
                            <input type="hidden" name="cardName" value="<?php echo htmlspecialchars($card['name']); ?>">
                            <button type="submit" name="sell">Sell</button>
                        </form>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>


    </section>

</body>

</html>