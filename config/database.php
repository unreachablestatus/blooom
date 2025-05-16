<?php
/**
 * Veritabanı bağlantı ayarları
 */

// Veritabanı ayarları
$db_host = "localhost";
$db_name = "bize_ozel_db";
$db_user = "root";
$db_pass = "";

// Test modu (veritabanını kullanmadan test verilerle çalışmak için)
$test_mode = false;

// Veritabanına bağlantı yap
function connectDB() {
    global $db_host, $db_name, $db_user, $db_pass, $test_mode;

    // Test modu aktifse veritabanı bağlantısı yapmadan return
    if ($test_mode) {
        return null;
    }

    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $db_user, $db_pass, $options);
    } catch (PDOException $e) {
        // Bağlantı hatası durumunda
        error_log("Veritabanı bağlantı hatası: " . $e->getMessage());

        // Otomatik test moduna geç
        $test_mode = true;
        return null;
    }
}

// Test modu kontrolü
function isTestMode() {
    global $test_mode;
    return $test_mode;
}

// Test modunu ayarlama fonksiyonu
function setTestMode($mode) {
    global $test_mode;
    $test_mode = (bool)$mode;
}
