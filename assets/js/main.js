document.addEventListener('DOMContentLoaded', function() {
    // Mobil menü kontrolü
    setupMobileMenu();

    // Tema değiştirme kontrolü
    setupThemeToggle();

    // Chat sayfası davranışları
    setupChatPage();

    // Admin sayfası davranışları
    setupAdminPage();
});

/**
 * Mobil menü işlevselliği
 */
function setupMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const closeMenu = document.getElementById('closeMenu');
    const mobileMenu = document.getElementById('mobileMenu');

    if (!menuToggle || !mobileMenu) return;

    // Menü açma
    menuToggle.addEventListener('click', function() {
        mobileMenu.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    // Menü kapatma
    if (closeMenu) {
        closeMenu.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    }

    // Sayfa dışına tıklama ile menüyü kapatma
    document.addEventListener('click', function(e) {
        if (mobileMenu.classList.contains('active') &&
            !mobileMenu.contains(e.target) &&
            e.target !== menuToggle) {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
}

/**
 * Tema değiştirme işlevselliği
 */
function setupThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const mobileThemeToggle = document.getElementById('mobileThemeToggle');

    function toggleTheme() {
        const isDarkMode = document.body.classList.toggle('dark-mode');

        // Cookie'ye tema ayarını kaydet (7 gün süreyle)
        const date = new Date();
        date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
        document.cookie = `theme=${isDarkMode ? 'dark' : 'light'}; expires=${date.toUTCString()}; path=/`;

        // Tema simgesini güncelle
        updateThemeIcons(isDarkMode);
    }

    function updateThemeIcons(isDarkMode) {
        // Ana tema düğmesi
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = isDarkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        // Mobil menü tema düğmesi
        if (mobileThemeToggle) {
            const icon = mobileThemeToggle.querySelector('i');
            if (icon) {
                icon.className = isDarkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }
    }

    // Ana tema düğmesi
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    // Mobil menü tema düğmesi
    if (mobileThemeToggle) {
        mobileThemeToggle.addEventListener('click', toggleTheme);
    }
}

/**
 * Chat sayfası özellikleri
 */
function setupChatPage() {
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    if (!chatForm || !chatMessages) return;

    // Mesaj gönderme
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        // AJAX ile mesaj gönderme işlemi
        sendMessage(message);

        // Input alanını temizle
        chatInput.value = '';
    });

    // Mesajları getir ve scroll'u en alta ayarla
    scrollMessagesToBottom();

    // Belirli aralıklarla yeni mesajları kontrol et
    if (chatMessages) {
        setInterval(function() {
            fetchNewMessages();
        }, 10000); // 10 saniyede bir
    }
}

/**
 * Mesaj gönderme AJAX işlemi
 */
function sendMessage(message) {
    const formData = new FormData();
    formData.append('content', message);
    formData.append('action', 'send_message');

    fetch('/api/chat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Başarıyla gönderildiyse sayfaya yeni mesajı ekle
            addMessageToChat(data.message);
            scrollMessagesToBottom();
        } else {
            alert('Mesaj gönderilemedi: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Mesaj gönderme hatası:', error);
    });
}

/**
 * Yeni mesajları alma AJAX işlemi
 */
function fetchNewMessages() {
    // Son mesaj ID'sini al
    const messages = document.querySelectorAll('.message-wrapper');
    let lastMessageId = 0;

    if (messages.length > 0) {
        lastMessageId = messages[messages.length - 1].dataset.messageId;
    }

    fetch(`/api/chat.php?action=get_messages&last_id=${lastMessageId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages.length > 0) {
            // Yeni mesajları ekle
            data.messages.forEach(message => {
                addMessageToChat(message);
            });
            scrollMessagesToBottom();
        }
    })
    .catch(error => {
        console.error('Mesaj alma hatası:', error);
    });
}

/**
 * Mesajı sohbet alanına ekle
 */
function addMessageToChat(message) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;

    // Mesaj grubu (tarih) var mı kontrol et
    const messageDate = formatMessageDate(message.created_at);
    let dateGroup = document.querySelector(`.chat-date-group[data-date="${messageDate}"]`);

    // Yoksa yeni tarih grubu oluştur
    if (!dateGroup) {
        dateGroup = document.createElement('div');
        dateGroup.className = 'chat-date-group';
        dateGroup.dataset.date = messageDate;

        const dateHeader = document.createElement('div');
        dateHeader.className = 'chat-date-header';
        dateHeader.innerHTML = `<span class="chat-date-label">${messageDate}</span>`;

        dateGroup.appendChild(dateHeader);
        chatMessages.appendChild(dateGroup);
    }

    // Yeni mesaj elementi oluştur
    const messageType = message.is_current_user ? 'outgoing' : 'incoming';

    const messageWrapper = document.createElement('div');
    messageWrapper.className = `message-wrapper ${messageType}`;
    messageWrapper.dataset.messageId = message.id;

    let messageContent = `
        <div class="message-bubble">
            ${!message.is_current_user ? `<div class="message-sender">${message.sender.username}</div>` : ''}
            <div class="message-text">${message.content}</div>
            <div class="message-time">
                ${formatMessageTime(message.created_at)}
                ${message.is_current_user ? `
                    <span class="message-status">
                        ${message.is_read ? '<i class="fa-solid fa-check-double"></i>' : '<i class="fa-solid fa-check"></i>'}
                    </span>
                ` : ''}
            </div>
        </div>
    `;

    messageWrapper.innerHTML = messageContent;
    dateGroup.appendChild(messageWrapper);
}

/**
 * Sohbet scroll'unu en alta ayarla
 */
function scrollMessagesToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

/**
 * Mesaj tarihini formatlama
 */
function formatMessageDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    // Aynı gün için
    if (date.toDateString() === today.toDateString()) {
        return 'Bugün';
    }

    // Dün için
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Dün';
    }

    // Diğer günler için
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('tr-TR', options);
}

/**
 * Mesaj saatini formatlama
 */
function formatMessageTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
}

/**
 * Admin sayfası özellikleri
 */
function setupAdminPage() {
    // Ana sayfa kartları düzenleme
    setupHomeCardsAdmin();

    // Tema özelleştirme işlemleri
    setupCustomTheme();

    // Admin mesaj editörü
    setupMessageEditor();

    // Admin şiir editörü
    setupPoemEditor();
}

/**
 * Ana sayfa kartları düzenleme
 */
function setupHomeCardsAdmin() {
    const homeCardsForm = document.getElementById('homeCardsForm');
    if (!homeCardsForm) return;

    homeCardsForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Form verilerini topla
        const formData = new FormData(homeCardsForm);

        // Kart verilerini düzenle
        const cards = [];
        const cardCount = document.querySelectorAll('.card-edit-section').length;

        for (let i = 0; i < cardCount; i++) {
            cards.push({
                id: formData.get(`card_id_${i}`),
                title: formData.get(`title_${i}`),
                description: formData.get(`description_${i}`),
                content: formData.get(`content_${i}`),
                icon: formData.get(`icon_${i}`)
            });
        }

        // Sunucuya kaydetme AJAX işlemi
        const serverFormData = new FormData();
        serverFormData.append('action', 'save_home_cards');
        serverFormData.append('cards', JSON.stringify(cards));

        fetch('/admin/api/cards.php', {
            method: 'POST',
            body: serverFormData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAdminMessage('Kartlar başarıyla kaydedildi!', 'success');
            } else {
                showAdminMessage('Kart kaydetme hatası: ' + data.error, 'error');
                console.error('Kart kaydetme hatası:', data.error);
            }
        })
        .catch(error => {
            showAdminMessage('Kart kaydetme hatası', 'error');
            console.error('Kart kaydetme hatası:', error);
        });
    });
}

/**
 * Tema özelleştirme
 */
function setupCustomTheme() {
    // İlgili özelleştirme formları
    const themeForm = document.getElementById('themeForm');
    if (!themeForm) return;

    themeForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Form verilerini topla
        const formData = new FormData(themeForm);

        // Sunucuya kaydetme AJAX işlemi
        const serverFormData = new FormData();
        serverFormData.append('action', 'save_theme_settings');

        // Form verilerini döngü ile ekle
        for (let [key, value] of formData.entries()) {
            serverFormData.append(key, value);
        }

        fetch('/admin/api/theme.php', {
            method: 'POST',
            body: serverFormData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAdminMessage('Tema ayarları başarıyla kaydedildi!', 'success');
            } else {
                showAdminMessage('Tema ayarları kaydetme hatası: ' + data.error, 'error');
                console.error('Tema ayarları kaydetme hatası:', data.error);
            }
        })
        .catch(error => {
            showAdminMessage('Tema ayarları kaydetme hatası', 'error');
            console.error('Tema ayarları kaydetme hatası:', error);
        });
    });
}

/**
 * Admin mesaj editörü
 */
function setupMessageEditor() {
    const messageForm = document.getElementById('messageForm');
    if (!messageForm) return;

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Form verilerini topla
        const formData = new FormData(messageForm);

        // Sunucuya kaydetme AJAX işlemi
        const serverFormData = new FormData();
        serverFormData.append('action', formData.get('message_id') ? 'update_message' : 'add_message');

        // Form verilerini döngü ile ekle
        for (let [key, value] of formData.entries()) {
            serverFormData.append(key, value);
        }

        fetch('/admin/api/messages.php', {
            method: 'POST',
            body: serverFormData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAdminMessage('Mesaj başarıyla kaydedildi!', 'success');
                // Başarılıysa listeye yönlendir
                setTimeout(() => {
                    window.location.href = '/admin/notlar.php';
                }, 1500);
            } else {
                showAdminMessage('Mesaj kaydetme hatası: ' + data.error, 'error');
                console.error('Mesaj kaydetme hatası:', data.error);
            }
        })
        .catch(error => {
            showAdminMessage('Mesaj kaydetme hatası', 'error');
            console.error('Mesaj kaydetme hatası:', error);
        });
    });
}

/**
 * Admin şiir editörü
 */
function setupPoemEditor() {
    const poemForm = document.getElementById('poemForm');
    if (!poemForm) return;

    poemForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Form verilerini topla
        const formData = new FormData(poemForm);

        // Sunucuya kaydetme AJAX işlemi
        const serverFormData = new FormData();
        serverFormData.append('action', formData.get('poem_id') ? 'update_poem' : 'add_poem');

        // Form verilerini döngü ile ekle
        for (let [key, value] of formData.entries()) {
            serverFormData.append(key, value);
        }

        fetch('/admin/api/poems.php', {
            method: 'POST',
            body: serverFormData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAdminMessage('Şiir başarıyla kaydedildi!', 'success');
                // Başarılıysa listeye yönlendir
                setTimeout(() => {
                    window.location.href = '/admin/siirler.php';
                }, 1500);
            } else {
                showAdminMessage('Şiir kaydetme hatası: ' + data.error, 'error');
                console.error('Şiir kaydetme hatası:', data.error);
            }
        })
        .catch(error => {
            showAdminMessage('Şiir kaydetme hatası', 'error');
            console.error('Şiir kaydetme hatası:', error);
        });
    });
}

/**
 * Admin panelinde başarı/hata mesajı göster
 */
function showAdminMessage(message, type = 'success') {
    const messageElement = document.getElementById('adminMessage');
    if (!messageElement) return;

    messageElement.textContent = message;
    messageElement.className = `alert alert-${type}`;
    messageElement.style.display = 'block';

    // 3 saniye sonra mesajı gizle
    setTimeout(() => {
        messageElement.style.display = 'none';
    }, 3000);
}
