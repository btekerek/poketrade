<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit();
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
        header('Location: user.php');
        exit();
    }

}

