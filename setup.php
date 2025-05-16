<?php
/**
 * Veritabanı Kurulum Betiği
 * Bu betiği çalıştırarak veritabanı ve tabloları oluşturabilirsiniz.
 */

// Hataları göster
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantı ayarlarını içe aktar
require_once 'config/database.php';

// Sayfa başlığı
$page_title = "Kurulum | Bize Özel";

// Hata ve başarı mesajları
$errors = [];
$success_msg = '';

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Veritabanı bağlantı bilgileri
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'bize_ozel_db';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';

    // Test bağlantısı
    try {
        $dsn = "mysql:host=$db_host";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);

        // SQL dosyasını oku
        $sql = file_get_contents('sql/setup.sql');

        // SQL komutlarını çalıştır
        $pdo->exec($sql);

        // database.php dosyasını güncelle
        $config_content = <<<EOT
<?php
/**
 * Veritabanı bağlantı ayarları
 */

// Veritabanı ayarları
\$db_host = "$db_host";
\$db_name = "$db_name";
\$db_user = "$db_user";
\$db_pass = "$db_pass";

// Test modu (veritabanını kullanmadan test verilerle çalışmak için)
\$test_mode = false;

// Veritabanına bağlantı yap
function connectDB() {
    global \$db_host, \$db_name, \$db_user, \$db_pass, \$test_mode;

    // Test modu aktifse veritabanı bağlantısı yapmadan return
    if (\$test_mode) {
        return null;
    }

    try {
        \$dsn = "mysql:host=\$db_host;dbname=\$db_name;charset=utf8mb4";
        \$options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO(\$dsn, \$db_user, \$db_pass, \$options);
    } catch (PDOException \$e) {
        // Bağlantı hatası durumunda
        error_log("Veritabanı bağlantı hatası: " . \$e->getMessage());

        // Otomatik test moduna geç
        \$test_mode = true;
        return null;
    }
}

// Test modu kontrolü
function isTestMode() {
    global \$test_mode;
    return \$test_mode;
}

// Test modunu ayarlama fonksiyonu
function setTestMode(\$mode) {
    global \$test_mode;
    \$test_mode = (bool)\$mode;
}
EOT;

        // Dosyaya yaz
        if (file_put_contents('config/database.php', $config_content)) {
            $success_msg = "Veritabanı ve tablolar başarıyla oluşturuldu. Kurulum tamamlandı!";
        } else {
            $errors[] = "Yapılandırma dosyası yazılırken bir hata oluştu.";
        }
    } catch (PDOException $e) {
        $errors[] = "Veritabanı hatası: " . $e->getMessage();
    }
}

// Test modu için form gönderildi mi
if (isset($_POST['enable_test_mode'])) {
    // database.php dosyasını test modu için güncelle
    $config_content = <<<EOT
<?php
/**
 * Veritabanı bağlantı ayarları
 */

// Veritabanı ayarları
\$db_host = "localhost";
\$db_name = "bize_ozel_db";
\$db_user = "root";
\$db_pass = "";

// Test modu (veritabanını kullanmadan test verilerle çalışmak için)
\$test_mode = true;

// Veritabanına bağlantı yap
function connectDB() {
    global \$db_host, \$db_name, \$db_user, \$db_pass, \$test_mode;

    // Test modu aktifse veritabanı bağlantısı yapmadan return
    if (\$test_mode) {
        return null;
    }

    try {
        \$dsn = "mysql:host=\$db_host;dbname=\$db_name;charset=utf8mb4";
        \$options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO(\$dsn, \$db_user, \$db_pass, \$options);
    } catch (PDOException \$e) {
        // Bağlantı hatası durumunda
        error_log("Veritabanı bağlantı hatası: " . \$e->getMessage());

        // Otomatik test moduna geç
        \$test_mode = true;
        return null;
    }
}

// Test modu kontrolü
function isTestMode() {
    global \$test_mode;
    return \$test_mode;
}

// Test modunu ayarlama fonksiyonu
function setTestMode(\$mode) {
    global \$test_mode;
    \$test_mode = (bool)\$mode;
}
EOT;

    // Dosyaya yaz
    if (file_put_contents('config/database.php', $config_content)) {
        $success_msg = "Test modu etkinleştirildi. Sistem veritabanı olmadan test verileriyle çalışacak.";
    } else {
        $errors[] = "Yapılandırma dosyası yazılırken bir hata oluştu.";
    }
}

// Header'ı dahil et
include 'includes/header.php';
?>

<div class="container">
    <div class="login-container" style="max-width: 600px;">
        <div class="login-card card">
            <div class="login-header">
                <h1>Sistem Kurulumu</h1>
                <p>Bize Özel sistemini kurmak için aşağıdaki formu kullanın.</p>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">
                    <p><?php echo $success_msg; ?></p>
                    <p class="mt-4">
                        <a href="index.php" class="button">Ana Sayfaya Git</a>
                    </p>
                </div>
            <?php else: ?>
                <div class="mb-8">
                    <h2 class="text-xl mb-4">Veritabanı Kurulumu</h2>
                    <p class="mb-4">Veritabanı bağlantı bilgilerini girerek sistemi kurabilirsiniz.</p>

                    <form method="post" action="setup.php">
                        <div class="form-group">
                            <label for="db_host">Veritabanı Sunucusu</label>
                            <input type="text" id="db_host" name="db_host" value="localhost" required>
                        </div>

                        <div class="form-group">
                            <label for="db_name">Veritabanı Adı</label>
                            <input type="text" id="db_name" name="db_name" value="bize_ozel_db" required>
                        </div>

                        <div class="form-group">
                            <label for="db_user">Veritabanı Kullanıcısı</label>
                            <input type="text" id="db_user" name="db_user" value="root" required>
                        </div>

                        <div class="form-group">
                            <label for="db_pass">Veritabanı Şifresi</label>
                            <input type="password" id="db_pass" name="db_pass">
                        </div>

                        <button type="submit" class="submit-button">Kurulumu Başlat</button>
                    </form>
                </div>

                <div>
                    <h2 class="text-xl mb-4">Test Modu Etkinleştir</h2>
                    <p class="mb-4">Veritabanı olmadan test verilerle sistemi kullanmak isterseniz test modunu etkinleştirebilirsiniz.</p>

                    <form method="post" action="setup.php">
                        <input type="hidden" name="enable_test_mode" value="1">
                        <button type="submit" class="submit-button">Test Modunu Etkinleştir</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>
