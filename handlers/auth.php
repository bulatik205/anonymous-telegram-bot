<?php
function userData($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function check($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $SQLdata = $stmt->fetch();

        if (empty($SQLdata)) {
            $bytes = random_bytes(4);
            $href = "";

            while (true) {
                $href = bin2hex($bytes);
                $stmt = $pdo->prepare("SELECT * FROM users WHERE href = ?");
                $stmt->execute([$href]);
                $SQLdata = $stmt->fetch();

                if (empty($SQLdata)) {
                    break;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO users (telegram_id, href) VALUES (?, ?)");
            $stmt->execute([$userId, $href]);
        }

        return userData($pdo, $userId);
    } catch (PDOException $e) {
        $data['text'] = "Ошибка БД";
        exit;
    }
}
