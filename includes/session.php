<?php
/**
 * Oturum Yönetimi
 */

// Oturumu başlat
session_start();

// Test modu kullanıcı bilgileri
$test_users = [
    'demo' => [
        'id' => 1,
        'password' => '$2y$10$uDbdoTKCjHAJVJjl1X0r0uu6i32KTbk4GQ1oT9YTOBBCRz5vQJL7K', // 'password'
        'username' => 'Demo Kullanıcı',
        'role' => 'admin'
    ],
    'sevgilim' => [
        'id' => 2,
        'password' => '$2y$10$uDbdoTKCjHAJVJjl1X0r0uu6i32KTbk4GQ1oT9YTOBBCRz5vQJL7K', // 'password'
        'username' => 'Sevgilim',
        'role' => 'user'
    ]
];

/**
 * Kullanıcı giriş yapmış mı kontrolü
 *
 * @return bool Kullanıcı giriş yapmışsa true, değilse false
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Kullanıcı girişi
 *
 * @param string $username Kullanıcı adı
 * @param string $password Şifre
 * @return bool Giriş başarılıysa true, değilse false
 */
function login($username, $password) {
    global $test_users;

    // Kullanıcı adını küçük harfe çevir
    $username = strtolower($username);

    // Test modu kontrolü
    if (isTestMode()) {
        if (isset($test_users[$username])) {
            $user = $test_users[$username];

            // Şifre kontrolü
            if (password_verify($password, $user['password'])) {
                // Oturum bilgilerini kaydet
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
        return false;
    }

    // Veritabanı bağlantısı
    require_once __DIR__ . '/../config/database.php';
    $db = connectDB();

    if (!$db) {
        return false;
    }

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Oturum bilgilerini kaydet
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['display_name'];
            $_SESSION['role'] = $user['role'];
            return true;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Kullanıcı girişi hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kullanıcı çıkışı
 */
function logout() {
    // Oturum değişkenlerini temizle
    $_SESSION = [];

    // Oturum çerezini sil
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Oturumu sonlandır
    session_destroy();
}

/**
 * Giriş gerektirir
 *
 * @param string $redirect Yönlendirilecek URL
 */
function requireLogin($redirect = '/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect");
        exit;
    }
}

/**
 * Admin yetkisi gerektirir
 *
 * @param string $redirect Yönlendirilecek URL
 */
function requireAdmin($redirect = '/index.php') {
    requireLogin($redirect);

    if (!isAdmin()) {
        header("Location: $redirect");
        exit;
    }
}
