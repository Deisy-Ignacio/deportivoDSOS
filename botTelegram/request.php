<?php

$botToken = "895700067:AAGiJrXI2zdex26R76UWKMpkD6QuKwq1UQg";
$website = "https://api.telegram.org/bot".$botToken;
$file_url = "http://images2.fanpop.com/images/photos/6300000/cat-animal-stars-6334317-1600-1598.jpg";

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatId = $update["message"]["chat"]["id"];
$chatType = $update["message"]["char"]["type"];

$message = $update["message"]["text"];

switch ($message) {
    case '/ayuda':
        $response = "https://dsosequipo1.000webhostapp.com/recipe.pdf";
        sendMessage($chatId, $response);
        break;
    case '/hora':
    return Request::sendDocument([
        'caption'  => 'caption file',
        'chat_id'  => $chatId,
        'document' => fopen($file_url,'rb'),
    ]);
        break;
}

function sendMessage($chatId, $response){
    $url = $GLOBALS[website].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}
?>