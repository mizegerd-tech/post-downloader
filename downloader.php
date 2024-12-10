<?php
// Watermark: https://t.me/mizegerddev & https://github.com/mizegerd-tech

// Telegram bot token
$botToken = "token_here";

// Telegram API URLs
$apiUrl = "https://api.telegram.org/bot$botToken/sendMessage";
$apiFileUrl = "https://api.telegram.org/bot$botToken/sendDocument";
$deleteMessageUrl = "https://api.telegram.org/bot$botToken/deleteMessage";

// Get the update from Telegram
$update = file_get_contents("php://input");
$update = json_decode($update, TRUE);

// Extract chat ID and message text from the update
$chatId = $update['message']['chat']['id'];
$messageText = $update['message']['text'];

/**
 * Sends a message to a Telegram chat.
 *
 * @param int $chatId The chat ID to send the message to.
 * @param string $message The message text to send.
 * @param string $botToken The bot token for authentication.
 * @return array The response from the Telegram API.
 */
function sendMessage($chatId, $message, $botToken) {
    $apiUrl = "https://api.telegram.org/bot$botToken/sendMessage";
    $postData = array(
        'chat_id' => $chatId,
        'text' => $message
    );
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/**
 * Deletes a message from a Telegram chat.
 *
 * @param int $chatId The chat ID where the message is located.
 * @param int $messageId The ID of the message to delete.
 * @param string $botToken The bot token for authentication.
 */
function deleteMessage($chatId, $messageId, $botToken) {
    global $deleteMessageUrl;
    $postData = array(
        'chat_id' => $chatId,
        'message_id' => $messageId
    );

    $ch = curl_init($deleteMessageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_exec($ch);
    curl_close($ch);
}

// Handle the /start command
if ($messageText == "/start") {
    sendMessage($chatId, "سلام! لطفاً لینک خود را ارسال کنید.", $botToken);

// Handle valid URLs
} elseif (filter_var($messageText, FILTER_VALIDATE_URL)) {
    $userUrl = $messageText;

    // API URL for Cobalt tools
    $cobaltApiUrl = "https://api.cobalt.tools/";

    $postData = array(
        "url" => $userUrl
    );

    $postDataJson = json_encode($postData);

    // Send URL to Cobalt API
    $ch = curl_init($cobaltApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseJson = json_decode($response, true);

    // Check if the response contains a valid URL and filename
    if (isset($responseJson['url']) && isset($responseJson['filename'])) {
        $fileUrl = $responseJson['url'];  
        $fileName = $responseJson['filename'];

        // Send a progress message
        $progressMessage = sendMessage($chatId, "در حال دانلود...", $botToken);
        $progressMessageId = $progressMessage['result']['message_id'];

        // Download the file content
        $fileCh = curl_init($fileUrl);
        curl_setopt($fileCh, CURLOPT_RETURNTRANSFER, true);
        $fileContent = curl_exec($fileCh);
        curl_close($fileCh);

        if ($fileContent === false) {
            sendMessage($chatId, "خطا در دانلود فایل از URL.", $botToken);
        } else {
            // Prepare the file for sending to Telegram
            $boundary = uniqid();
            $delimiter = '-------------' . $boundary;

            $caption = "link: " . $fileUrl; 

            $postFields = "--$delimiter\r\n"
                . "Content-Disposition: form-data; name=\"chat_id\"\r\n\r\n$chatId\r\n"
                . "--$delimiter\r\n"
                . "Content-Disposition: form-data; name=\"document\"; filename=\"$fileName\"\r\n"
                . "Content-Type: application/octet-stream\r\n\r\n"
                . $fileContent . "\r\n"
                . "--$delimiter\r\n"
                . "Content-Disposition: form-data; name=\"caption\"\r\n\r\n$caption\r\n"
                . "--$delimiter--\r\n";

            $headers = array(
                "Content-Type: multipart/form-data; boundary=$delimiter",
                "Content-Length: " . strlen($postFields)
            );

            // Send the file to Telegram
            $telegramCh = curl_init($apiFileUrl);
            curl_setopt($telegramCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($telegramCh, CURLOPT_POST, true);
            curl_setopt($telegramCh, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($telegramCh, CURLOPT_POSTFIELDS, $postFields);

            $telegramResponse = curl_exec($telegramCh);
            curl_close($telegramCh);

            if ($telegramResponse === false) {
                sendMessage($chatId, "خطا در ارسال فایل به تلگرام.", $botToken);
            } else {
                // Delete the progress message
                deleteMessage($chatId, $progressMessageId, $botToken);

                // Notify the user of success
                sendMessage($chatId, "فایل با موفقیت دانلود و ارسال شد!", $botToken);
            }
        }
    } else {
        sendMessage($chatId, "هیچ URL یا filename معتبری در پاسخ وجود ندارد.", $botToken);
    }

} else {
    // Handle invalid input
    sendMessage($chatId, "لطفاً یک لینک معتبر ارسال کنید.", $botToken);
}
?>