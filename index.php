<?php
require 'config/config.php';
require 'handlers/auth.php';
require 'source/text.php';
require 'source/inline.php';

$update = json_decode(file_get_contents('php://input'), true);
$json_response = json_encode($update, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

function sendMessage($arrayData, $url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrayData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
}

if (isset($update['message'])) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $userId = $update['message']['from']['id'];
    $firstName = $update['message']['from']['first_name'];
    $text = $update['message']['text'];
    $data = [
        'chat_id' => $userId,
        'parse_mode' => 'HTML'
    ];

    $databaseData = check($main_pdo, $userId);

    $href = $databaseData['href'];

    $messageParts = explode(" ", $text);

    if ($databaseData['status'] != "output") {
        if ($messageParts[0] == "/start" && isset($messageParts[1])) {
            try {
                $stmt = $main_pdo->prepare("SELECT * FROM users WHERE href = ?");
                $stmt->execute([$messageParts[1]]);
                $SQLdata = $stmt->fetch(PDO::FETCH_ASSOC);

                if (empty($SQLdata)) {
                    $data['text'] = commandStartText($firstName, $databaseData['href']);
                    # --==--== ATTENTION START ==--==-- #
                    $data['reply_markup'] = json_encode(getStartButtons("https://t.me/anon_bulatik_bot?start=$href")); # update link for you bot
                    # --==--== ATTENTION END ==--==-- #
                    sendMessage($data, $url);
                } else {
                    $stmt = $main_pdo->prepare("UPDATE users SET status = ? WHERE telegram_id = ?");
                    $stmt->execute([$messageParts[1], $userId]);

                    $data['text'] = commandUserText();
                    sendMessage($data, $url);
                }
            } catch (Exception $e) {
                $data['text'] = "Ошибка БД";
                exit;
            }
        } else {
            if ($messageParts[0] != "/start") {
                $stmt = $main_pdo->prepare("SELECT status FROM users WHERE telegram_id = ?");
                $stmt->execute([$userId]);
                $SQLdata = $stmt->fetch(PDO::FETCH_ASSOC);
                $href = $SQLdata['status'];

                $stmt = $main_pdo->prepare("SELECT * FROM users WHERE href = ?");
                $stmt->execute([$href]);
                $SQLdata = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!empty($SQLdata)) {
                    $data = [
                        'chat_id' => $SQLdata['telegram_id'],
                        'parse_mode' => 'HTML',
                        'text' => "<b>❄️ Новое анонимное сообщение: \n\n<blockquote>" . $text . "</blockquote></b>"
                    ];
                    sendMessage($data, $url);
                    $data = [
                        'chat_id' => $userId,
                        'parse_mode' => 'HTML'
                    ];

                    $messageHash = password_hash($text, PASSWORD_DEFAULT);
                    $stmt = $main_pdo->prepare("INSERT INTO messages(message_by, message_from, message) VALUES(?, ?, ?)");
                    $stmt->execute([$SQLdata['telegram_id'], $userId, $messageHash]);

                    $stmt = $main_pdo->prepare("UPDATE users SET status = ? WHERE telegram_id = ?");
                    $stmt->execute(["output", $userId]);

                    $data['text'] = commandSendText();
                    # --==--== ATTENTION START ==--==-- #
                    $data['reply_markup'] = json_encode(getMoreButtons("https://t.me/anon_bulatik_bot?start=$href")); # update link for you bot
                    # --==--== ATTENTION END ==--==-- #
                    sendMessage($data, $url);

                    $data['text'] = commandStartText($firstName, $databaseData['href']);
                    # --==--== ATTENTION START ==--==-- #
                    $data['reply_markup'] = json_encode(getStartButtons("https://t.me/anon_bulatik_bot?start=$href")); # update link for you bot
                    # --==--== ATTENTION END ==--==-- #
                    sendMessage($data, $url);
                    exit;
                } else {
                    $stmt = $main_pdo->prepare("UPDATE users SET status = ? WHERE telegram_id = ?");
                    $stmt->execute(["output", $userId]);

                    $data['text'] = commandErrorText();
                    sendMessage($data, $url);
                    exit;
                }
            }

            $stmt = $main_pdo->prepare("UPDATE users SET status = ? WHERE telegram_id = ?");
            $stmt->execute(["output", $userId]);

            $data['text'] = commandStartText($firstName, $databaseData['href']);
            # --==--== ATTENTION START ==--==-- #
            $data['reply_markup'] = json_encode(getStartButtons("https://t.me/anon_bulatik_bot?start=$href"));
            # --==--== ATTENTION END ==--==-- #
            sendMessage($data, $url);
        }

        exit;
    }

    if ($messageParts[0] == "/start" && isset($messageParts[1])) {
        try {
            $stmt = $main_pdo->prepare("SELECT * FROM users WHERE href = ?");
            $stmt->execute([$messageParts[1]]);
            $SQLdata = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($SQLdata)) {
                $data['text'] = commandStartText($firstName, $databaseData['href']);
                # --==--== ATTENTION START ==--==-- #
                $data['reply_markup'] = json_encode(getStartButtons("https://t.me/anon_bulatik_bot?start=$href"));
                # --==--== ATTENTION END ==--==-- #
                sendMessage($data, $url);
            } else {
                $stmt = $main_pdo->prepare("UPDATE users SET status = ? WHERE telegram_id = ?");
                $stmt->execute([$messageParts[1], $userId]);

                $data['text'] = commandUserText();
                sendMessage($data, $url);
            }
        } catch (Exception $e) {
            $data['text'] = "Ошибка БД";
            exit;
        }

        exit;
    }

    if ($messageParts[0] == "/start") {
        $data['text'] = commandStartText($firstName, $databaseData['href']);
        # --==--== ATTENTION START ==--==-- #
        $data['reply_markup'] = json_encode(getStartButtons("https://t.me/anon_bulatik_bot?start=$href"));
        # --==--== ATTENTION END ==--==-- #
        sendMessage($data, $url);
    }
}
