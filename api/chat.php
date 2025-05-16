<?php
// Yardımcı fonksiyonları dahil et
require_once '../includes/functions.php';

// JSON başlığı ayarla
header('Content-Type: application/json');

// İstek metodu kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Son mesaj ID'sini al
    $lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

    // İstek tipini kontrol et
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'get_messages') {
        // Yeni mesajları getir
        $messages = getMessages($lastId);

        // Başarılı yanıt
        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // İstek tipini kontrol et
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'send_message') {
        // Giriş kontrolü
        if (!isLoggedIn()) {
            echo json_encode([
                'success' => false,
                'error' => 'Mesaj göndermek için giriş yapmanız gerekiyor.'
            ]);
            exit;
        }

        // Mesaj içeriğini al
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        if (empty($content)) {
            echo json_encode([
                'success' => false,
                'error' => 'Mesaj içeriği boş olamaz.'
            ]);
            exit;
        }

        // Mesajı ekle
        $message = addMessage($content);

        if ($message) {
            // Başarılı yanıt
            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Mesaj eklenemedi.'
            ]);
            exit;
        }
    }
}

// Geçersiz istek
echo json_encode([
    'success' => false,
    'error' => 'Geçersiz istek.'
]);
?>
