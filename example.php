<?php

require 'vendor/autoload.php';

use RamiroEstrella\ChatwootPhpSdk\ChatwootClient;

$chatwoot = new ChatwootClient(
    'https://chat.hococo.io',
    'WuJe2zUNFfEBs7q9PUghY4C7',
    1
);

// ✅ Use Platform App #3's token
// $account = $chatwoot->platform('iwUbvVz4S7BnJDwQW8QawTsA')->accounts()->show(1);

// print_r($account->custom_attributes);

$conversations = $chatwoot->application()->conversations()->list();

foreach ($conversations->items as $conversation) {
    print_r($conversation->id);
}