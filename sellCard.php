<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit();
}

if (isset($_POST['sell'])) {
    $cardName = $_POST['cardName'];
    $username = $_SESSION['user'];

    $cards = json_decode(file_get_contents('cards_data.json'), true);
    $users = json_decode(file_get_contents('users.json'), true);

    foreach ($cards as $key => $card) {
        if ($card['name'] == $cardName && $card['owner'] == $username) {
            $cards[$key]['owner'] = 'admin';
            $moneyEarned = $card['price'] * 0.9;
            break;
        }
    }

    foreach ($users as $key => $user) {
        if ($user['username'] == $username) {
            $users[$key]['money'] += $moneyEarned;
            $cardIndex = array_search($cardName, $users[$key]['cards']);
            if ($cardIndex !== false) {
                array_splice($users[$key]['cards'], $cardIndex, 1);
            }

            break;
        }
    }

    file_put_contents('cards_data.json', json_encode($cards));
    file_put_contents('users.json', json_encode($users));

    header('Location: user.php');
    exit();
}

header('Location: user.php');
exit();
