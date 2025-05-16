<?php
// Yardımcı fonksiyonları dahil et
require_once '../includes/functions.php';

// Admin yetkisi kontrolü
requireAdmin();

// Sayfa başlığı
$page_title = "Yönetim Paneli | Bize Özel";

// İstatistikleri hazırla
$stats = [
    'notes_count' => count(getNotes()),
    'poems_count' => count(getPoems()),
    'messages_count' => count(getMessages())
];

// Header'ı dahil et
include '../includes/header.php';
?>

<div class="container">
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Kontrol Paneli</h1>
                <p class="admin-description">
                    Site içeriğini ve ayarlarını buradan yönetebilirsiniz.
                </p>
            </div>

            <div id="adminMessage" class="alert" style="display: none;"></div>

            <div class="cards-grid">
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title">Özel Notlar</h2>
                            <i class="fa-solid fa-sticky-note"></i>
                        </div>
                        <p class="card-description">Toplam Not Sayısı</p>
                    </div>
                    <div class="card-content">
                        <div class="flex justify-between items-center">
                            <p class="text-2xl font-bold"><?php echo $stats['notes_count']; ?></p>
                            <a href="/admin/notlar.php" class="button secondary">
                                Yönet
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title">Şiirler</h2>
                            <i class="fa-solid fa-book"></i>
                        </div>
                        <p class="card-description">Toplam Şiir Sayısı</p>
                    </div>
                    <div class="card-content">
                        <div class="flex justify-between items-center">
                            <p class="text-2xl font-bold"><?php echo $stats['poems_count']; ?></p>
                            <a href="/admin/siirler.php" class="button secondary">
                                Yönet
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title">Mesajlar</h2>
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <p class="card-description">Toplam Mesaj Sayısı</p>
                    </div>
                    <div class="card-content">
                        <div class="flex justify-between items-center">
                            <p class="text-2xl font-bold"><?php echo $stats['messages_count']; ?></p>
                            <a href="/admin/mesajlar.php" class="button secondary">
                                Yönet
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Hızlı İşlemler</h2>
                </div>
                <div class="card-content">
                    <div class="flex flex-col gap-4 md:flex-row">
                        <a href="/admin/not-ekle.php" class="button">
                            <i class="fa-solid fa-plus me-2"></i> Yeni Not Ekle
                        </a>
                        <a href="/admin/siir-ekle.php" class="button secondary">
                            <i class="fa-solid fa-plus me-2"></i> Yeni Şiir Ekle
                        </a>
                        <a href="/admin/anasayfa-kartlari.php" class="button outline">
                            <i class="fa-solid fa-edit me-2"></i> Ana Sayfa Kartlarını Düzenle
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include '../includes/footer.php';
?>
