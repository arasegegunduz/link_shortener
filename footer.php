<?php
/**
 * ArasPHP Link Shortener v1.0
 * Final Footer - Multi-Lang & Responsive
 */
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';

$footer_t = [
    'tr' => [
        'copy' => 'Tüm hakları saklıdır.',
        'about' => 'ArasPHP, modern ve güvenli link kısaltma deneyimi sunar. Hız, güvenlik ve istatistik hepsi bir arada.',
        'quick_links' => 'Hızlı Linkler',
        'home' => 'Ana Sayfa',
        'login' => 'Giriş Yap',
        'register' => 'Kayıt Ol',
        'dashboard' => 'Panel',
        'contact' => 'İletişim & Destek'
    ],
    'en' => [
        'copy' => 'All rights reserved.',
        'about' => 'ArasPHP offers a modern and secure link shortening experience. Speed, security and stats all in one.',
        'quick_links' => 'Quick Links',
        'home' => 'Home',
        'login' => 'Login',
        'register' => 'Register',
        'dashboard' => 'Dashboard',
        'contact' => 'Contact & Support'
    ]
];
$f_txt = $footer_t[$lang];
?>

<style>
    .main-footer {
        background: #0f172a;
        padding: 50px 0 20px 0;
        margin-top: 50px;
        border-top: 1px solid rgba(6, 182, 212, 0.1);
        color: var(--text-muted);
    }
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 40px;
        padding: 0 20px;
    }
    @media (max-width: 768px) {
        .footer-container { grid-template-columns: 1fr; text-align: center; }
    }
    .footer-logo { font-size: 24px; font-weight: 700; color: #f8fafc; margin-bottom: 15px; }
    .footer-logo span { color: var(--accent); }
    .footer-title { color: #f8fafc; font-size: 16px; font-weight: 600; margin-bottom: 20px; text-transform: uppercase; }
    .footer-links { list-style: none; }
    .footer-links li { margin-bottom: 10px; }
    .footer-links a { color: var(--text-muted); text-decoration: none; transition: 0.3s; font-size: 14px; }
    .footer-links a:hover { color: var(--accent); padding-left: 5px; }
    .social-icons { display: flex; gap: 15px; margin-top: 20px; }
    @media (max-width: 768px) { .social-icons { justify-content: center; } }
    .social-icons a { color: var(--text-muted); font-size: 20px; transition: 0.3s; }
    .social-icons a:hover { color: var(--accent); transform: translateY(-3px); }
    .copyright {
        text-align: center;
        padding-top: 30px;
        margin-top: 40px;
        border-top: 1px solid rgba(255,255,255,0.05);
        font-size: 13px;
    }
</style>

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-info">
            <div class="footer-logo">&lt;Aras<span>PHP</span>/&gt;</div>
            <p style="line-height: 1.6; font-size: 14px;"><?php echo $f_txt['about']; ?></p>
            <div class="social-icons">
                <a href="#"><i class="fa-brands fa-github"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin"></i></a>
            </div>
        </div>

        <div>
            <h4 class="footer-title"><?php echo $f_txt['quick_links']; ?></h4>
            <ul class="footer-links">
                <li><a href="index"><?php echo $f_txt['home']; ?></a></li>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <li><a href="login"><?php echo $f_txt['login']; ?></a></li>
                    <li><a href="register"><?php echo $f_txt['register']; ?></a></li>
                <?php else: ?>
                    <li><a href="dashboard"><?php echo $f_txt['dashboard']; ?></a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div>
            <h4 class="footer-title"><?php echo $f_txt['contact']; ?></h4>
            <ul class="footer-links">
                <li><a href="#"><i class="fa-solid fa-envelope"></i> support@arasphp.com</a></li>
                <li><a href="#"><i class="fa-solid fa-circle-question"></i> FAQ / SSS</a></li>
                <li><a href="#"><i class="fa-solid fa-file-contract"></i> Privacy Policy</a></li>
            </ul>
        </div>
    </div>

    <div class="copyright">
        &copy; <?php echo date("Y"); ?> <b>ArasPHP</b>. <?php echo $f_txt['copy']; ?>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>