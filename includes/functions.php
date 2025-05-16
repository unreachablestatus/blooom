<?php
/**
 * Yardımcı fonksiyonlar
 */

// Veritabanı bağlantısını içe aktar
require_once __DIR__ . '/../config/database.php';

// Session kontrolünü içe aktar
require_once __DIR__ . '/session.php';

/**
 * Aktif sayfanın kontrolü
 *
 * @param string $page Kontrol edilecek sayfa adı
 * @return bool Aktif sayfa ise true, değilse false
 */
function isActivePage($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $page;
}

/**
 * Kullanıcı admin mi kontrolü
 *
 * @return bool Kullanıcı admin ise true, değilse false
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Güvenli HTML çıktısı
 *
 * @param string $string Güvenli hale getirilecek metin
 * @return string Güvenli hale getirilmiş metin
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Ana sayfa kartlarını getir
 *
 * @return array Ana sayfa kartları
 */
function getHomeCards() {
    if (isTestMode()) {
        // Test modu aktifse hazır kartları döndür
        return [
            [
                'id' => '1',
                'title' => 'Sohbet Et',
                'description' => 'Özel mesajlaşma alanımız',
                'content' => 'Tüm güzel konuşmalarımız burada saklanıyor. İstediğin zaman eski mesajları okuyabilirsin.',
                'icon' => 'fa-comments'
            ],
            [
                'id' => '2',
                'title' => 'Anılarımız',
                'description' => 'Birlikte geçirdiğimiz zamanlar',
                'content' => 'Beraber yaşadığımız özel anların küçük notları. Her biri çok değerli.',
                'icon' => 'fa-heart'
            ],
            [
                'id' => '3',
                'title' => 'Şiirler',
                'description' => 'Sana yazılan özel şiirler',
                'content' => 'Hislerimi ve duygularımı aktardığım şiirler. Sadece senin için yazıldı.',
                'icon' => 'fa-book'
            ]
        ];
    }

    // Veritabanı bağlantısı
    $db = connectDB();
    if (!$db) {
        return [];
    }

    try {
        $stmt = $db->query("SELECT * FROM home_cards ORDER BY card_order ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Ana sayfa kartları getirme hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Chat mesajlarını getir
 *
 * @param int $lastId Son mesaj ID'si (bu ID'den büyük mesajlar getirilir)
 * @return array Mesajlar
 */
function getMessages($lastId = 0) {
    if (isTestMode()) {
        // Test modu aktifse hazır mesajları döndür
        $mockMessages = [
            [
                'id' => 1,
                'user_id' => 1,
                'content' => 'Merhaba, nasılsın?',
                'created_at' => '2023-06-15 10:30:00',
                'is_read' => 1,
                'sender' => [
                    'id' => 1,
                    'username' => 'Kullanıcı'
                ],
                'is_current_user' => true
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'content' => 'İyiyim, teşekkür ederim. Sen nasılsın?',
                'created_at' => '2023-06-15 10:31:00',
                'is_read' => 1,
                'sender' => [
                    'id' => 2,
                    'username' => 'Sevgilim'
                ],
                'is_current_user' => false
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'content' => 'Ben de iyiyim. Bugün ne yapmak istersin?',
                'created_at' => '2023-06-15 10:32:00',
                'is_read' => 1,
                'sender' => [
                    'id' => 1,
                    'username' => 'Kullanıcı'
                ],
                'is_current_user' => true
            ],
            [
                'id' => 4,
                'user_id' => 2,
                'content' => 'Seninle birlikte olmak güzel olurdu. Belki biraz yürüyüş yapabiliriz?',
                'created_at' => '2023-06-15 10:33:00',
                'is_read' => 1,
                'sender' => [
                    'id' => 2,
                    'username' => 'Sevgilim'
                ],
                'is_current_user' => false
            ],
            [
                'id' => 5,
                'user_id' => 1,
                'content' => 'Harika fikir! Hangi parkta buluşalım?',
                'created_at' => '2023-06-15 10:34:00',
                'is_read' => 0,
                'sender' => [
                    'id' => 1,
                    'username' => 'Kullanıcı'
                ],
                'is_current_user' => true
            ],
        ];

        // Son mesaj ID'sinden büyük mesajları filtrele
        if ($lastId > 0) {
            return array_filter($mockMessages, function($msg) use ($lastId) {
                return $msg['id'] > $lastId;
            });
        }

        return $mockMessages;
    }

    // Veritabanı bağlantısı
    $db = connectDB();
    if (!$db) {
        return [];
    }

    try {
        $currentUserId = $_SESSION['user_id'] ?? 0;

        $query = "
            SELECT m.*, u.username
            FROM messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.id > :lastId
            ORDER BY m.created_at ASC
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':lastId', $lastId, PDO::PARAM_INT);
        $stmt->execute();

        $messages = $stmt->fetchAll();

        // Mesaj bilgilerini düzenle
        foreach ($messages as &$message) {
            $message['sender'] = [
                'id' => $message['user_id'],
                'username' => $message['username']
            ];
            $message['is_current_user'] = ($message['user_id'] == $currentUserId);

            // Gereksiz alanları kaldır
            unset($message['username']);
        }

        return $messages;
    } catch (PDOException $e) {
        error_log("Mesajları getirme hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Yeni mesaj ekle
 *
 * @param string $content Mesaj içeriği
 * @return array|bool Başarılıysa mesaj bilgilerini içeren array, değilse false
 */
function addMessage($content) {
    if (!isLoggedIn()) {
        return false;
    }

    if (isTestMode()) {
        // Test modu aktifse mesaj sayısını al ve yeni mesaj oluştur
        $mockMessages = getMessages();
        $lastId = count($mockMessages) + 1;

        $newMessage = [
            'id' => $lastId,
            'user_id' => $_SESSION['user_id'],
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'is_read' => 0,
            'sender' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username']
            ],
            'is_current_user' => true
        ];

        return $newMessage;
    }

    // Veritabanı bağlantısı
    $db = connectDB();
    if (!$db) {
        return false;
    }

    try {
        $userId = $_SESSION['user_id'];
        $now = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO messages (user_id, content, created_at)
            VALUES (:user_id, :content, :created_at)
        ";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':created_at', $now);

        if ($stmt->execute()) {
            $messageId = $db->lastInsertId();

            // Yeni oluşturulan mesajı getir
            $getStmt = $db->prepare("
                SELECT m.*, u.username
                FROM messages m
                JOIN users u ON m.user_id = u.id
                WHERE m.id = :id
            ");
            $getStmt->bindParam(':id', $messageId, PDO::PARAM_INT);
            $getStmt->execute();

            $message = $getStmt->fetch();
            if ($message) {
                $message['sender'] = [
                    'id' => $message['user_id'],
                    'username' => $message['username']
                ];
                $message['is_current_user'] = true;

                // Gereksiz alanları kaldır
                unset($message['username']);

                return $message;
            }
        }

        return false;
    } catch (PDOException $e) {
        error_log("Mesaj ekleme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Not/Mesaj kartlarını getir
 *
 * @return array Not kartları
 */
function getNotes() {
    if (isTestMode()) {
        // Test modu aktifse hazır notları döndür
        return [
            [
                'id' => 1,
                'content' => 'Seni ilk gördüğüm gün, hayatımın en güzel günüydü. O anı hiç unutmayacağım.',
                'created_at' => '2023-05-15 14:30:00',
                'gradient' => 'gradient-violet-pink',
                'icon' => 'fa-heart'
            ],
            [
                'id' => 2,
                'content' => 'Seninle geçirdiğim her an, hayatıma kattığın her renk için teşekkür ederim.',
                'created_at' => '2023-06-22 09:15:00',
                'gradient' => 'gradient-blue-purple',
                'icon' => 'fa-star'
            ],
            [
                'id' => 3,
                'content' => 'Gülüşün, benim için güneşin doğuşu gibi. Her seferinde içimi aydınlatıyor.',
                'created_at' => '2023-07-10 18:45:00',
                'gradient' => 'gradient-pink-orange',
                'icon' => 'fa-sun'
            ],
            [
                'id' => 4,
                'content' => 'Hayallerimizi birlikte gerçekleştirmek için sabırsızlanıyorum. Seninle her şey daha güzel.',
                'created_at' => '2023-08-05 11:20:00',
                'gradient' => 'gradient-green-blue',
                'icon' => 'fa-map'
            ]
        ];
    }

    // Veritabanı bağlantısı
    $db = connectDB();
    if (!$db) {
        return [];
    }

    try {
        $stmt = $db->query("SELECT * FROM notes ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Notları getirme hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Şiirleri getir
 *
 * @return array Şiirler
 */
function getPoems() {
    if (isTestMode()) {
        // Test modu aktifse hazır şiirleri döndür
        return [
            [
                'id' => 1,
                'title' => 'Sonsuz Sevgi',
                'content' => "Gözlerine baktığımda,\nTüm dünyayı görüyorum.\nEllerini tuttuğumda,\nEvreni avuçlarımda hissediyorum.\n\nSenin gülüşün,\nGüneşin ilk ışıkları gibi.\nKalbimi ısıtıyor,\nRuhumu aydınlatıyor.",
                'created_at' => '2023-04-10 15:30:00',
                'gradient' => 'gradient-violet-pink'
            ],
            [
                'id' => 2,
                'title' => 'Hayallerimiz',
                'content' => "Seninle hayal kurduğumuzda,\nGeleceğimiz aydınlanıyor.\nBirlikte yürüdüğümüzde,\nKalplerimiz bir oluyor.\n\nSenin yanında olmak,\nEn büyük mutluluğum.\nSeninle yaşlanmak,\nEn büyük hayalim.",
                'created_at' => '2023-05-22 09:15:00',
                'gradient' => 'gradient-blue-purple'
            ],
            [
                'id' => 3,
                'title' => 'Kalp Atışları',
                'content' => "Her kalp atışımda sen varsın,\nHer nefesimde seni hissediyorum.\nGözlerim kapandığında bile,\nSenin hayalin karşımda duruyor.\n\nSen benim ilk düşüncem sabahları,\nSon düşüncem geceleri.\nSen benim en güzel şiirim,\nKalbime yazdığım en güzel sözler.",
                'created_at' => '2023-06-15 18:45:00',
                'gradient' => 'gradient-pink-orange'
            ]
        ];
    }

    // Veritabanı bağlantısı
    $db = connectDB();
    if (!$db) {
        return [];
    }

    try {
        $stmt = $db->query("SELECT * FROM poems ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Şiirleri getirme hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Tarih formatını düzenle
 *
 * @param string $dateString Tarih string'i
 * @param bool $withTime Saat bilgisiyle birlikte mi
 * @return string Düzenlenmiş tarih
 */
function formatDate($dateString, $withTime = false) {
    $date = new DateTime($dateString);
    $format = $withTime ? 'd F Y, H:i' : 'd F Y';

    $formatter = new IntlDateFormatter(
        'tr_TR',
        IntlDateFormatter::LONG,
        $withTime ? IntlDateFormatter::SHORT : IntlDateFormatter::NONE
    );

    return $formatter->format($date);
}

/**
 * Yönlendirme yap
 *
 * @param string $url Yönlendirilecek URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * CSRF token oluştur
 *
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
