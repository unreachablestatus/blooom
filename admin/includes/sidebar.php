<div class="admin-sidebar">
    <div class="admin-sidebar-title">Yönetim Paneli</div>
    <nav class="admin-nav">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <a href="/admin/index.php">
                    <i class="fa-solid fa-home"></i> Genel Bakış
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'anasayfa-kartlari.php' ? 'active' : ''; ?>">
                <a href="/admin/anasayfa-kartlari.php">
                    <i class="fa-solid fa-th"></i> Ana Sayfa Kartları
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'notlar.php' ? 'active' : ''; ?>">
                <a href="/admin/notlar.php">
                    <i class="fa-solid fa-sticky-note"></i> Notlar
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'siirler.php' ? 'active' : ''; ?>">
                <a href="/admin/siirler.php">
                    <i class="fa-solid fa-book"></i> Şiirler
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'ayarlar.php' ? 'active' : ''; ?>">
                <a href="/admin/ayarlar.php">
                    <i class="fa-solid fa-gear"></i> Ayarlar
                </a>
            </li>
        </ul>
    </nav>
</div>
