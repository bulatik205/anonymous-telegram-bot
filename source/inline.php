<?php
function getMoreButtons($hrefUrl)
{
    return [
        'inline_keyboard' => [
            [
                ['text' => '⛄️ Отправить ещё', 'url' => $hrefUrl]
            ]
        ]
    ];
}

function getStartButtons($hrefUrl)
{
    return [
        'inline_keyboard' => [
            [
                ['text' => '⛄️ Отправить друзьям', 'url' => "https://t.me/share/url?url=" . $hrefUrl]
            ]
        ]
    ];
}
