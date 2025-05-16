<?php
// Yardımcı fonksiyonları dahil et
require_once '../includes/functions.php';

// Admin yetkisi kontrolü
requireAdmin();

// Sayfa başlığı
$page_title = "Ana Sayfa Kartları | Yönetim Paneli";

// Ana sayfa kartlarını al
$cards = getHomeCards();

// Header'ı dahil et
include '../includes/header.php';
?>

<div class="container">
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1 class="admin-title">Ana Sayfa Kartları</h1>
                <p class="admin-description">
                    Ana sayfada görüntülenen kartları düzenleyebilirsiniz.
                </p>
            </div>

            <div id="adminMessage" class="alert" style="display: none;"></div>

            <div class="card admin-card">
                <div class="card-header">
                    <h2 class="card-title">Kartları Düzenle</h2>
                </div>
                <div class="admin-card-content">
                    <form id="homeCardsForm">
                        <?php foreach ($cards as $index => $card): ?>
                            <div class="admin-form-section card-edit-section">
                                <h3 class="admin-form-title">Kart #<?php echo $index + 1; ?></h3>

                                <input type="hidden" name="card_id_<?php echo $index; ?>" value="<?php echo htmlspecialchars($card['id']); ?>">

                                <div class="form-group">
                                    <label for="title_<?php echo $index; ?>">Başlık</label>
                                    <input type="text" id="title_<?php echo $index; ?>" name="title_<?php echo $index; ?>" value="<?php echo htmlspecialchars($card['title']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="description_<?php echo $index; ?>">Açıklama</label>
                                    <input type="text" id="description_<?php echo $index; ?>" name="description_<?php echo $index; ?>" value="<?php echo htmlspecialchars($card['description']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="content_<?php echo $index; ?>">İçerik</label>
                                    <textarea id="content_<?php echo $index; ?>" name="content_<?php echo $index; ?>" rows="3" required><?php echo htmlspecialchars($card['content']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="icon_<?php echo $index; ?>">İkon (Font Awesome sınıfı)</label>
                                    <input type="text" id="icon_<?php echo $index; ?>" name="icon_<?php echo $index; ?>" value="<?php echo htmlspecialchars($card['icon']); ?>" required>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="admin-form-actions">
                            <button type="submit" class="button">Değişiklikleri Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include '../includes/footer.php';
?>
