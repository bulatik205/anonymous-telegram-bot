<?php 
function commandStartText($firstName, $href) {
    return "<b>👋 Привет, $firstName\n\n<blockquote>💬 Отправь анонимное сообщение, перейдя по ссылке, которую тебе дали</blockquote>\n<blockquote>🔗 Твоя ссылка: https://t.me/anon_bulatik_bot?start=$href</blockquote></b>";
}

function commandUserText() {
    return "<b>💬 Напиши сообщение. Это увидят анонимно:</b>";
}

function commandSendText() {
    return "<b>💬 Сообщение отправлено!</b>";
}

function commandErrorText() {
    return "<b>❌ Произошла ошибка!</b>";
}