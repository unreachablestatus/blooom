<?php
// Title değişkeni tanımlı değilse varsayılan başlık kullan
$page_title = isset($page_title) ? $page_title : "Bize Özel ❤";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (isset($extra_css)): echo $extra_css; endif; ?>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark-mode' : ''; ?>">

    <?php if (!isset($hide_header) || !$hide_header): ?>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="/index.php">Bize Özel ❤</a>
                </div>

                <!-- Mobil Menü Butonu -->
                <div class="mobile-menu-toggle">
                    <button id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                </div>

                <!-- Masaüstü Navigasyon -->
                <nav class="desktop-nav">
                    <ul>
                        <li class="<?php echo isActivePage('index.php') ? 'active' : ''; ?>">
                            <a href="/index.php">Ana Sayfa</a>
                        </li>
                        <li class="<?php echo isActivePage('chat.php') ? 'active' : ''; ?>">
                            <a href="/pages/chat.php">Chat</a>
                        </li>
                        <li class="<?php echo isActivePage('notlar.php') ? 'active' : ''; ?>">
                            <a href="/pages/notlar.php">Notlar</a>
                        </li>
                        <li class="<?php echo isActivePage('siirler.php') ? 'active' : ''; ?>">
                            <a href="/pages/siirler.php">Şiirler</a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active' : ''; ?>">
                            <a href="/admin/index.php">Yönetim Paneli</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <!-- Kullanıcı Bölümü -->
                <div class="user-section">
                    <?php if (isLoggedIn()): ?>
                        <div class="user-info">
                            <span><i class="fa-solid fa-user"></i> <?php echo $_SESSION['username']; ?></span>
                            <a href="/logout.php" class="logout-button" title="Çıkış Yap">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="/login.php" class="login-button">
                            <i class="fa-solid fa-right-to-bracket"></i> Giriş Yap
                        </a>
                    <?php endif; ?>

                    <!-- Tema Değiştirme Butonu -->
                    <button id="themeToggle" class="theme-toggle">
                        <i class="fa-solid <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'fa-sun' : 'fa-moon'; ?>"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobil Menü -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <h2>Bize Özel ❤</h2>
            <button id="closeMenu"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="mobile-user-info">
            <?php if (isLoggedIn()): ?>
                <div class="user-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="user-details">
                    <p class="username"><?php echo $_SESSION['username']; ?></p>
                    <p class="user-role"><?php echo isAdmin() ? 'Yönetici' : 'Kullanıcı'; ?></p>
                </div>
            <?php else: ?>
                <p>Giriş yapmadınız</p>
                <a href="/login.php" class="login-btn">
                    <i class="fa-solid fa-right-to-bracket"></i> Giriş Yap
                </a>
            <?php endif; ?>
        </div>

        <nav class="mobile-nav">
            <ul>
                <li class="<?php echo isActivePage('index.php') ? 'active' : ''; ?>">
                    <a href="/index.php"><i class="fa-solid fa-home"></i> Ana Sayfa</a>
                </li>
                <li class="<?php echo isActivePage('chat.php') ? 'active' : ''; ?>">
                    <a href="/pages/chat.php"><i class="fa-solid fa-comments"></i> Chat</a>
                </li>
                <li class="<?php echo isActivePage('notlar.php') ? 'active' : ''; ?>">
                    <a href="/pages/notlar.php"><i class="fa-solid fa-heart"></i> Notlar</a>
                </li>
                <li class="<?php echo isActivePage('siirler.php') ? 'active' : ''; ?>">
                    <a href="/pages/siirler.php"><i class="fa-solid fa-book"></i> Şiirler</a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active' : ''; ?>">
                    <a href="/admin/index.php"><i class="fa-solid fa-gear"></i> Yönetim Paneli</a>
                </li>
                <?php endif; ?>

                <!-- Tema Değiştir Butonu -->
                <li class="theme-menu-item">
                    <div>
                        <i class="fa-solid fa-sun"></i> Tema
                    </div>
                    <div class="theme-toggle-wrapper">
                        <button id="mobileThemeToggle" class="theme-toggle">
                            <i class="fa-solid <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'fa-sun' : 'fa-moon'; ?>"></i>
                        </button>
                    </div>
                </li>
            </ul>
        </nav>

        <?php if (isLoggedIn()): ?>
        <div class="mobile-menu-footer">
            <a href="/logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Çıkış Yap
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <main class="main-content">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_msg) && !empty($success_msg)): ?>
            <div class="alert alert-success">
                <p><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>
