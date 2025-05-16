<?php
// Yardımcı fonksiyonları dahil et
require_once '../includes/functions.php';

// Sayfa başlığı
$page_title = "Notlar | Bize Özel";

// Notları al
$notes = getNotes();

// Header'ı dahil et
include '../includes/header.php';
?>

<div class="container">
    <h1 class="hero-title text-center mb-8">Özel Notlar</h1>

    <?php foreach ($notes as $note): ?>
        <div class="message-card <?php echo htmlspecialchars($note['gradient']); ?>">
            <i class="fa-solid <?php echo htmlspecialchars($note['icon']); ?> message-icon"></i>
            <div class="message-content"><?php echo htmlspecialchars($note['content']); ?></div>
            <div class="message-date"><?php echo formatDate($note['created_at'], true); ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php
// Footer'ı dahil et
include '../includes/footer.php';
?>
