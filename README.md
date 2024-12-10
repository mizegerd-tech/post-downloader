
# 🌐 Social Media Downloader Bot

A versatile and powerful **PHP** bot for downloading various content from popular social media platforms. This bot simplifies the process of downloading posts, stories, and more, while maintaining a permanent connection to web services for uninterrupted service.

---

## 🍏 Key Features

- **Multi-Social Platform Support**: Easily download media such as posts and stories from multiple social media networks.
- **Professional Coding**: The bot is built with highly efficient and professional-grade code to ensure smooth performance.
- **Permanent Web Service**: The bot is connected to a web service that guarantees constant availability for downloading content.
- **Telegram Integration**: Users can interact with the bot through Telegram to send links and receive media.
- **Group Usability**: The bot can be added to groups, allowing multiple users to request downloads concurrently.

---

## 🛠️ Installation and Setup

### Prerequisites

1. **PHP 7+** installed on your web server or local machine.
2. A **Telegram Bot** created via [BotFather](https://core.telegram.org/bots#botfather) with the token ready.
3. A **web service** for downloading media from social networks (such as an external API).

### Steps to Set Up

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-repo/social-media-downloader-bot.git
   cd social-media-downloader-bot
   ```

2. **Edit Configuration**:
   - Open the PHP file and replace `"token_here"` with your **Telegram bot token**.

   Example:
   ```php
   $botToken = "your_bot_token_here";
   ```

3. **Set up Web Server**:
   Ensure that your server is configured to run PHP scripts. For local environments, you can use **XAMPP**, **WAMP**, or **MAMP**.

4. **Deploy the Bot**:
   Upload the PHP files to your server or run the script locally.

5. **Telegram Interaction**:
   Users can interact with the bot through Telegram by sending a URL of the media they want to download.

---

## 📥 Usage

1. **Starting the Bot**:
   - Open Telegram and send `/start` to the bot.
   - The bot will prompt you to send a link of the media you want to download.

2. **Download Process**:
   - Once you send a valid media link, the bot fetches the media using a web service and sends the file directly back to you in Telegram.

3. **Supported Platforms**:
   - Instagram
   - Twitter
   - Facebook
   - And more…

---

## 🧑‍💻 Example

- **User**: `/start`
  - **Bot Response**: `"Hello! Please send your media link."`

- **User**: (Send a valid media link)
  - **Bot Response**: `"Downloading the media..."`  
    (Media will be processed and sent back to the user)

---

## 💻 Code Explanation

### Core PHP Logic

1. **Bot Token and URLs**:  
   The bot uses the Telegram API to send messages and files. Replace `token_here` with your actual Telegram bot token.

2. **Handling User Input**:
   - The script checks for valid URLs using `FILTER_VALIDATE_URL`. 
   - Once a valid link is detected, it sends a request to the external service (e.g., Cobalt API) to retrieve the media.

3. **Downloading Media**:
   - The bot downloads the media using a `curl` request to the external service, then uploads it to Telegram.

4. **Sending Files to Telegram**:
   - After the media is successfully downloaded, the bot uploads it to Telegram using `sendDocument`, along with a caption containing the original link.

5. **Error Handling**:
   - If a download or upload fails, the bot informs the user of the failure.

---

## 🤖 Adding to Groups

This bot is group-friendly and can be added to Telegram groups. Group members can send links to the bot, and it will download the requested media for everyone in the group.

---

## 👥 Support

For any issues or suggestions, feel free to [reach out on Telegram](https://t.me/mizegerd_dev) or contribute directly via [GitHub](https://github.com/mizegerd-tech).
