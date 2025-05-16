<?php
// HTTP durum kodu ayarla
http_response_code(404);

// Yardımcı fonksiyonları dahil et
require_once 'includes/functions.php';

// Sayfa başlığı
$page_title = "Sayfa Bulunamadı | Bize Özel";

// Header'ı dahil et
include 'includes/header.php';
?>

<div class="container">
    <div class="error-page">
        <div class="error-code">404</div>
        <h1 class="error-message">Sayfa Bulunamadı</h1>
        <p class="mb-8">
            Aradığınız sayfa bulunamadı. Silinmiş veya taşınmış olabilir.
        </p>
        <a href="/index.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Ana Sayfaya Dön
        </a>
    </div>
</div>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>
