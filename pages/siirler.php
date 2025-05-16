<?php
// Yardımcı fonksiyonları dahil et
require_once '../includes/functions.php';

// Sayfa başlığı
$page_title = "Şiirler | Bize Özel";

// Şiirleri al
$poems = getPoems();

// Header'ı dahil et
include '../includes/header.php';
?>

<div class="container">
    <h1 class="hero-title text-center mb-8">Sana Yazılan Şiirler</h1>

    <?php foreach ($poems as $poem): ?>
        <div class="poem-card <?php echo htmlspecialchars($poem['gradient']); ?>">
            <h2 class="poem-title"><?php echo htmlspecialchars($poem['title']); ?></h2>
            <pre class="poem-content"><?php echo htmlspecialchars($poem['content']); ?></pre>
            <div class="poem-date"><?php echo formatDate($poem['created_at'], true); ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php
// Footer'ı dahil et
include '../includes/footer.php';
?>
