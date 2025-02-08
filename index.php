<?php
    $username = 'a8289c9b7b36f10a519e08907a4a18c2';
    $key = '46237111ca2cd3d2bf11e0fc8496f9cb';
    $webhookurl = "https://discord.com/api/webhooks/1251699015624429648/_tkGGgC7wmkusbr0jVVypPvzGVmGLC4f9VgWaVyTkCKp-cpl_5zN4KWaYkyCpiL2SMTW"; // Kendi webhook'unu buraya koy!

    if (!(isset($_POST['res']) && isset($_POST['hash']))) {
        echo "missing parameter";
        die();
    }

    $hash = hash_hmac('sha256', $_POST['res'] . $username, $key, false);
    if (strcmp($hash, $_POST['hash']) != 0) {
        die();
    }

    $json_result = base64_decode($_POST['res']);
    $array_result = json_decode($json_result, true);

    $email = $array_result['email'];
    $orderid = $array_result['orderid'];
    $currency = $array_result['currency'];
    $price = $array_result['price'];
    $buyername = $array_result['buyername'];
    $buyersurname = $array_result['buyersurname'];
    $productcount = $array_result['productcount'];
    $customernote = $array_result['customernote'];
    $istest = $array_result['istest'];

    $birim = ($currency == "0") ? '₺' : (($currency == "1") ? '$' : '€');
    $musterinotu = ($customernote === true) ? $customernote : "Bulunmuyor.";

    echo "success";
    $timestamp = date("c");

    $json_data = json_encode([
        "content" => "Bir ödeme alındı <@&rol_id>",
        "username" => "$buyername $buyersurname",
        "avatar_url" => "https://cdn.discordapp.com/avatars/553548136728231976/d674ebe45820805934cba909452082ac.png",
        "tts" => false,
        "embeds" => [
            [
                "type" => "rich",
                "description" => "1 Aylık $productcount adet sipariş alındı!",
                "timestamp" => $timestamp,
                "color" => hexdec("f5f5f5"),
                "fields" => [
                    ["name" => "Müşteri Adı Soyadı", "value" => "$buyername $buyersurname", "inline" => true],
                    ["name" => "Müşteri E-posta", "value" => "$email", "inline" => true],
                    ["name" => "Sipariş ID", "value" => "#$orderid", "inline" => true],
                    ["name" => "Ürün adı", "value" => "1 Aylık", "inline" => true],
                    ["name" => "Ürün adeti", "value" => "$productcount adet", "inline" => true],
                    ["name" => "Ürün fiyatı", "value" => "$price $birim", "inline" => true],
                    ["name" => "Müşteri açıklaması", "value" => "$musterinotu", "inline" => true]
                ]
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_exec($ch);
    curl_close($ch);
?>
