<?php
// Yardımcı fonksiyonları dahil et
require_once '../includes/functions.php';

// Giriş kontrolü
requireLogin();

// Sayfa başlığı
$page_title = "Chat | Bize Özel";

// Mesajları al
$messages = getMessages();

// Mesajları tarihe göre gruplandır
$messagesByDate = [];
foreach ($messages as $message) {
    $dateKey = date('Y-m-d', strtotime($message['created_at']));
    if (!isset($messagesByDate[$dateKey])) {
        $messagesByDate[$dateKey] = [];
    }
    $messagesByDate[$dateKey][] = $message;
}

// Header'ı dahil et
include '../includes/header.php';
?>

<div class="container">
    <div class="chat-container">
        <div class="chat-area" id="chatMessages">
            <?php foreach ($messagesByDate as $date => $dateMessages): ?>
                <div class="chat-date-group" data-date="<?php echo formatDate($date); ?>">
                    <div class="chat-date-header">
                        <span class="chat-date-label"><?php echo formatDate($date); ?></span>
                    </div>

                    <?php foreach ($dateMessages as $message): ?>
                        <div class="message-wrapper <?php echo $message['is_current_user'] ? 'outgoing' : 'incoming'; ?>" data-message-id="<?php echo $message['id']; ?>">
                            <div class="message-bubble">
                                <?php if (!$message['is_current_user']): ?>
                                    <div class="message-sender"><?php echo htmlspecialchars($message['sender']['username']); ?></div>
                                <?php endif; ?>
                                <div class="message-text"><?php echo htmlspecialchars($message['content']); ?></div>
                                <div class="message-time">
                                    <?php echo date('H:i', strtotime($message['created_at'])); ?>
                                    <?php if ($message['is_current_user']): ?>
                                        <span class="message-status">
                                            <?php if ($message['is_read']): ?>
                                                <i class="fa-solid fa-check-double"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-check"></i>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-input-area">
            <form class="chat-input-form" id="chatForm">
                <input type="text" class="chat-input" id="chatInput" placeholder="Mesajınızı yazın..." required>
                <button type="submit" class="chat-send-button">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include '../includes/footer.php';
?>
