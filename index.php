<?php
// Yardımcı fonksiyonları dahil et
require_once 'includes/functions.php';

// Sayfa başlığı
$page_title = "Bize Özel ❤";

// Ana sayfa kartlarını al
$cards = getHomeCards();

// Header'ı dahil et
include 'includes/header.php';
?>

<div class="container">
    <!-- Hero Bölümü -->
    <section class="hero-section">
        <h1 class="hero-title">İyi ki varsın</h1>
        <p class="hero-description">
            Birlikte yaşadığımız özel anları, paylaştığımız duyguları ve hayallerimizi içeren özel yerimiz.
        </p>
        <div class="hero-buttons">
            <a href="/pages/chat.php" class="button">
                <i class="fa-solid fa-comments me-2"></i> Chat'e Başla
            </a>
            <a href="/pages/notlar.php" class="button outline">
                <i class="fa-solid fa-heart me-2"></i> Notları Oku
            </a>
        </div>
    </section>

    <!-- Kartlar Bölümü -->
    <section class="cards-grid">
        <?php foreach ($cards as $index => $card): ?>
            <div class="card" data-card-id="<?php echo htmlspecialchars($card['id']); ?>">
                <div class="card-header">
                    <div class="flex justify-between items-center">
                        <h2 class="card-title"><?php echo htmlspecialchars($card['title']); ?></h2>
                        <i class="fa-solid <?php echo htmlspecialchars($card['icon']); ?>" style="color: var(--primary-light);"></i>
                    </div>
                    <p class="card-description"><?php echo htmlspecialchars($card['description']); ?></p>
                </div>
                <div class="card-content">
                    <p><?php echo htmlspecialchars($card['content']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Alt Bölüm -->
    <section class="footer-section">
        <h2 class="footer-title">Sevgimizin İzinde</h2>
        <p class="footer-text">
            Bu site, sevgimizin bir yansıması olarak tasarlandı. Birlikte yaşadığımız anılarımızı,
            paylaştığımız duyguları ve birbirimize olan sevgimizi kutlamak için.
        </p>
    </section>
</div>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>
