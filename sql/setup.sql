-- Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS bize_ozel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Veritabanını kullan
USE bize_ozel_db;

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Mesajlar tablosu
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Notlar tablosu
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    gradient VARCHAR(50) NOT NULL DEFAULT 'gradient-violet-pink',
    icon VARCHAR(50) NOT NULL DEFAULT 'fa-heart',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Şiirler tablosu
CREATE TABLE IF NOT EXISTS poems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    gradient VARCHAR(50) NOT NULL DEFAULT 'gradient-violet-pink',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ana sayfa kartları tablosu
CREATE TABLE IF NOT EXISTS home_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    icon VARCHAR(50) NOT NULL,
    card_order INT NOT NULL DEFAULT 0
);

-- Tema ayarları tablosu
CREATE TABLE IF NOT EXISTS theme_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Örnek kullanıcılar
INSERT INTO users (username, display_name, password, role) VALUES
('admin', 'Admin Kullanıcı', '$2y$10$uDbdoTKCjHAJVJjl1X0r0uu6i32KTbk4GQ1oT9YTOBBCRz5vQJL7K', 'admin'), -- şifre: password
('sevgilim', 'Sevgilim', '$2y$10$uDbdoTKCjHAJVJjl1X0r0uu6i32KTbk4GQ1oT9YTOBBCRz5vQJL7K', 'user'); -- şifre: password

-- Örnek mesajlar
INSERT INTO messages (user_id, content, is_read, created_at) VALUES
(1, 'Merhaba, nasılsın?', 1, '2023-06-15 10:30:00'),
(2, 'İyiyim, teşekkür ederim. Sen nasılsın?', 1, '2023-06-15 10:31:00'),
(1, 'Ben de iyiyim. Bugün ne yapmak istersin?', 1, '2023-06-15 10:32:00'),
(2, 'Seninle birlikte olmak güzel olurdu. Belki biraz yürüyüş yapabiliriz?', 1, '2023-06-15 10:33:00'),
(1, 'Harika fikir! Hangi parkta buluşalım?', 0, '2023-06-15 10:34:00');

-- Örnek notlar
INSERT INTO notes (content, gradient, icon, created_at) VALUES
('Seni ilk gördüğüm gün, hayatımın en güzel günüydü. O anı hiç unutmayacağım.', 'gradient-violet-pink', 'fa-heart', '2023-05-15 14:30:00'),
('Seninle geçirdiğim her an, hayatıma kattığın her renk için teşekkür ederim.', 'gradient-blue-purple', 'fa-star', '2023-06-22 09:15:00'),
('Gülüşün, benim için güneşin doğuşu gibi. Her seferinde içimi aydınlatıyor.', 'gradient-pink-orange', 'fa-sun', '2023-07-10 18:45:00'),
('Hayallerimizi birlikte gerçekleştirmek için sabırsızlanıyorum. Seninle her şey daha güzel.', 'gradient-green-blue', 'fa-map', '2023-08-05 11:20:00');

-- Örnek şiirler
INSERT INTO poems (title, content, gradient, created_at) VALUES
('Sonsuz Sevgi', 'Gözlerine baktığımda,\nTüm dünyayı görüyorum.\nEllerini tuttuğumda,\nEvreni avuçlarımda hissediyorum.\n\nSenin gülüşün,\nGüneşin ilk ışıkları gibi.\nKalbimi ısıtıyor,\nRuhumu aydınlatıyor.', 'gradient-violet-pink', '2023-04-10 15:30:00'),
('Hayallerimiz', 'Seninle hayal kurduğumuzda,\nGeleceğimiz aydınlanıyor.\nBirlikte yürüdüğümüzde,\nKalplerimiz bir oluyor.\n\nSenin yanında olmak,\nEn büyük mutluluğum.\nSeninle yaşlanmak,\nEn büyük hayalim.', 'gradient-blue-purple', '2023-05-22 09:15:00'),
('Kalp Atışları', 'Her kalp atışımda sen varsın,\nHer nefesimde seni hissediyorum.\nGözlerim kapandığında bile,\nSenin hayalin karşımda duruyor.\n\nSen benim ilk düşüncem sabahları,\nSon düşüncem geceleri.\nSen benim en güzel şiirim,\nKalbime yazdığım en güzel sözler.', 'gradient-pink-orange', '2023-06-15 18:45:00');

-- Örnek ana sayfa kartları
INSERT INTO home_cards (title, description, content, icon, card_order) VALUES
('Sohbet Et', 'Özel mesajlaşma alanımız', 'Tüm güzel konuşmalarımız burada saklanıyor. İstediğin zaman eski mesajları okuyabilirsin.', 'fa-comments', 1),
('Anılarımız', 'Birlikte geçirdiğimiz zamanlar', 'Beraber yaşadığımız özel anların küçük notları. Her biri çok değerli.', 'fa-heart', 2),
('Şiirler', 'Sana yazılan özel şiirler', 'Hislerimi ve duygularımı aktardığım şiirler. Sadece senin için yazıldı.', 'fa-book', 3);
