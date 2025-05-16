<?php
// Yardımcı fonksiyonları dahil et
require_once 'includes/functions.php';

// Zaten giriş yapmışsa ana sayfaya yönlendir
if (isLoggedIn()) {
    redirect('index.php');
}

// Hata mesajı değişkeni
$errors = [];

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token kontrolü
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Güvenlik doğrulaması başarısız oldu. Lütfen sayfayı yenileyip tekrar deneyin.";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Boş alan kontrolü
        if (empty($username) || empty($password)) {
            $errors[] = "Kullanıcı adı ve şifre gereklidir.";
        } else {
            // Giriş işlemi
            if (login($username, $password)) {
                // Başarılı giriş, ana sayfaya yönlendir
                redirect('index.php');
            } else {
                $errors[] = "Geçersiz kullanıcı adı veya şifre.";
            }
        }
    }
}

// Sayfa başlığı
$page_title = "Giriş Yap | Bize Özel";

// Header'ı dahil et
include 'includes/header.php';
?>

<div class="container">
    <div class="login-container">
        <div class="login-card card">
            <div class="login-header">
                <h1>Hesabınıza Giriş Yapın</h1>
                <p>Sevgi dolu anlarınıza erişin</p>
            </div>

            <form method="post" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="submit-button">Giriş Yap</button>
            </form>

            <div class="text-center mt-4 text-sm">
                <p>Test hesabı: kullanıcı adı "demo", şifre "password"</p>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>
