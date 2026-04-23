<?php 
include 'header.php'; 

$t = [
    'tr' => [
        'title' => 'Gelişmiş Link Kısaltıcı',
        'desc' => 'Hızlı, şifreli ve QR destekli. Uzun bağlantını aşağıya bırak.',
        'placeholder' => 'Uzun URL\'yi buraya yapıştır...',
        'btn_shorten' => 'LİNKİ KISALT 🚀',
        'lbl_alias' => 'Özel İsim (Opsiyonel)',
        'ph_alias' => 'Örn: arasphp',
        'lbl_pass' => 'Şifre Koruması (Opsiyonel)',
        'ph_pass' => 'Gizli Şifre Belirle',
        'limit_title' => 'Kalan Ücretsiz Hakkınız:',
        'limit_promo' => 'Sınırsız kısaltma, detaylı istatistikler ve özel paneller için',
        'register_now' => 'Hemen Kayıt Ol',
        'why_title' => 'Neden ArasPHP?',
        'f1_t' => 'Işık Hızında', 'f1_d' => 'Saliseler içinde hedefe ulaşır.',
        'f2_t' => 'Şifreli Erişim', 'f2_d' => 'Linklerinize sadece şifreyi bilenler erişebilir.',
        'f3_t' => 'Özel İsimler', 'f3_d' => 'Akılda kalıcı linkler (örn: kampanya26).',
        'qr_title' => 'QR Kod Ne İşe Yarar?',
        'q1_t' => 'Kameraya Okutun', 'q1_d' => 'Müşterileriniz kamerasını açarak sitenize gitsin.',
        'q2_t' => 'Mobil Uyum', 'q2_d' => 'Kısaltılan her linke özel karekod otomatik üretilir.',
        'result_ready' => 'Bağlantın Hazır!',
        'btn_copy' => 'Kopyala',
        'qr_down' => 'QR Kodu İndirmek İçin Sağ Tıklayın'
    ],
    'en' => [
        'title' => 'Advanced Link Shortener',
        'desc' => 'Fast, secure and QR supported. Drop your long URL below.',
        'placeholder' => 'Paste your long URL here...',
        'btn_shorten' => 'SHORTEN LINK 🚀',
        'lbl_alias' => 'Custom Alias (Optional)',
        'ph_alias' => 'Ex: arasphp',
        'lbl_pass' => 'Password Protect (Optional)',
        'ph_pass' => 'Set Secret Password',
        'limit_title' => 'Remaining Free Uses:',
        'limit_promo' => 'For unlimited shortening, detailed stats and custom panels',
        'register_now' => 'Register Now',
        'why_title' => 'Why ArasPHP?',
        'f1_t' => 'Lightning Fast', 'f1_d' => 'Reaches the target in milliseconds.',
        'f2_t' => 'Password Protection', 'f2_d' => 'Restrict access to your links with a password.',
        'f3_t' => 'Custom Aliases', 'f3_d' => 'Memorable links (e.g., promo26).',
        'qr_title' => 'Why use QR Codes?',
        'q1_t' => 'Scan with Camera', 'q1_d' => 'Customers can visit your site by scanning.',
        'q2_t' => 'Mobile Ready', 'q2_d' => 'A unique QR code is generated instantly.',
        'result_ready' => 'Your Link is Ready!',
        'btn_copy' => 'Copy',
        'qr_down' => 'Right click to save QR Code'
    ]
];
$txt = $t[$lang];

$is_logged_in = isset($_SESSION['user_id']);
$guest_count = isset($_SESSION['guest_count']) ? $_SESSION['guest_count'] : 0;
$remaining = 5 - $guest_count;
if($remaining < 0) $remaining = 0;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .hero-container { max-width: 1300px; margin: 0 auto; min-height: calc(100vh - 70px); display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 30px; padding: 40px 20px; align-items: start; position: relative; }
    @media (max-width: 992px) { .hero-container { grid-template-columns: 1fr; } }
    .glass-box { background: var(--card-bg); border-radius: 12px; padding: 30px; position: relative; border: 1px solid rgba(6, 182, 212, 0.2); box-shadow: 0 0 20px rgba(0,0,0,0.5), inset 0 0 10px rgba(6,182,212,0.05); z-index: 1; }
    .shortener-box { padding: 40px; }
    .shortener-box::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--accent); box-shadow: 0 0 15px var(--accent); border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .info-panel { background: rgba(30, 41, 59, 0.7); }
    .info-title { color: var(--accent); font-size: 18px; font-weight: 700; margin-bottom: 15px; text-transform: uppercase; border-bottom: 1px solid rgba(6,182,212,0.2); padding-bottom: 10px; }
    .info-list { list-style: none; color: var(--text-muted); font-size: 14px; line-height: 1.6; }
    .info-list li { margin-bottom: 15px; display: flex; gap: 10px; }
    .info-list i { color: var(--accent); font-size: 16px; margin-top: 3px; }
    .input-group { position: relative; margin-bottom: 20px; }
    .input-group i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--accent); font-size: 20px; }
    .url-input { width: 100%; padding: 20px 20px 20px 55px; background: rgba(15, 23, 42, 0.6); border: 1px solid #334155; color: white; font-size: 18px; border-radius: 8px; outline: none; transition: 0.3s; }
    .url-input:focus { border-color: var(--accent); box-shadow: 0 0 15px rgba(6,182,212,0.3); }
    
    /* Gelişmiş Araçlar Grid */
    .adv-tools { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
    @media (max-width: 600px) { .adv-tools { grid-template-columns: 1fr; } }
    .adv-input { width: 100%; padding: 12px; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 6px; outline: none; transition: 0.3s;}
    .adv-input:focus { border-color: var(--accent); }
    
    .btn-neon { background: rgba(6,182,212,0.1); color: var(--accent); border: 2px solid var(--accent); padding: 16px; font-size: 20px; font-weight: 700; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; letter-spacing: 2px; }
    .btn-neon:hover { background: var(--accent); color: #0f172a; box-shadow: 0 0 25px var(--accent); }
    .result-box { display: none; margin-top: 30px; padding: 25px; background: rgba(6,182,212,0.05); border: 1px solid rgba(6,182,212,0.3); border-radius: 8px; text-align: center; }
    .short-link-display { font-size: 24px; color: var(--accent); margin: 15px 0; font-weight: bold; word-break: break-all;}
    .qr-placeholder img { border-radius: 8px; border: 2px solid white; padding: 5px; background: white; margin-top: 15px;}
    .cyber-lines { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(rgba(15,23,42,0.9) 1px, transparent 1px), linear-gradient(90deg, rgba(15,23,42,0.9) 1px, transparent 1px); background-size: 30px 30px; z-index: 0; opacity: 0.1; pointer-events: none; }
    .limit-box { margin-top: 25px; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 8px; border: 1px dashed #334155; text-align: center; }
    .limit-bar { height: 8px; background: #0f172a; border-radius: 4px; margin: 10px 0; overflow: hidden; position: relative;}
    .limit-fill { height: 100%; background: var(--accent); width: <?php echo ($remaining*20); ?>%; transition: 0.5s; }
</style>

<div style="position: relative; background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);">
    <div class="cyber-lines"></div>

    <main class="hero-container">
        
        <aside class="glass-box info-panel">
            <h3 class="info-title"><i class="fa-solid fa-shield-halved"></i> <?php echo $txt['why_title']; ?></h3>
            <ul class="info-list">
                <li><i class="fa-solid fa-bolt"></i> <div><b><?php echo $txt['f1_t']; ?></b><br><?php echo $txt['f1_d']; ?></div></li>
                <li><i class="fa-solid fa-lock"></i> <div><b><?php echo $txt['f2_t']; ?></b><br><?php echo $txt['f2_d']; ?></div></li>
                <li><i class="fa-solid fa-link"></i> <div><b><?php echo $txt['f3_t']; ?></b><br><?php echo $txt['f3_d']; ?></div></li>
            </ul>
        </aside>

        <section class="glass-box shortener-box">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 30px; color: white;"><?php echo $txt['title']; ?></h1>
                <p style="color: var(--text-muted);"><?php echo $txt['desc']; ?></p>
            </div>
            
            <form id="shortenerForm">
                <input type="text" id="website_url_trap" name="website_url_trap" style="display:none;" tabindex="-1" autocomplete="off">

                <div class="input-group">
                    <i class="fa-solid fa-link"></i>
                    <input type="url" id="long_url" class="url-input" placeholder="<?php echo $txt['placeholder']; ?>" required>
                </div>

                <div class="adv-tools">
                    <div>
                        <label style="font-size: 12px; color: var(--accent);"><i class="fa-solid fa-pen-nib"></i> <?php echo $txt['lbl_alias']; ?></label>
                        <input type="text" id="custom_alias" class="adv-input" placeholder="<?php echo $txt['ph_alias']; ?>">
                    </div>
                    <div>
                        <label style="font-size: 12px; color: var(--accent);"><i class="fa-solid fa-key"></i> <?php echo $txt['lbl_pass']; ?></label>
                        <input type="password" id="link_password" class="adv-input" placeholder="<?php echo $txt['ph_pass']; ?>">
                    </div>
                </div>

                <button type="submit" class="btn-neon" id="submitBtn">
                    <i class="fa-solid fa-bolt"></i> <?php echo $txt['btn_shorten']; ?>
                </button>
            </form>

            <?php if(!$is_logged_in): ?>
            <div class="limit-box">
                <span style="font-size: 14px; color: var(--text-muted);"><?php echo $txt['limit_title']; ?> <b><span id="remCount"><?php echo $remaining; ?></span>/5</b></span>
                <div class="limit-bar"><div class="limit-fill" id="remFill"></div></div>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 10px;"><?php echo $txt['limit_promo']; ?></p>
                <a href="register" style="color: var(--accent); text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid var(--accent); padding: 5px 15px; border-radius: 4px;"><?php echo $txt['register_now']; ?></a>
            </div>
            <?php endif; ?>

            <div class="result-box" id="resultBox">
                <p style="color: #10b981;"><i class="fa-solid fa-circle-check"></i> <?php echo $txt['result_ready']; ?></p>
                <div class="short-link-display" id="generatedLink">...</div>
                
                <div>
                    <button onclick="copyToClipboard()" style="padding: 10px 20px; background: #334155; color: white; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s;"><i class="fa-regular fa-copy"></i> <?php echo $txt['btn_copy']; ?></button>
                </div>

                <div class="qr-placeholder">
                    <img id="qrImage" src="" alt="QR Code" width="150" height="150">
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 10px;"><?php echo $txt['qr_down']; ?></p>
            </div>
        </section>

        <aside class="glass-box info-panel">
            <h3 class="info-title"><i class="fa-solid fa-qrcode"></i> <?php echo $txt['qr_title']; ?></h3>
            <ul class="info-list">
                <li><i class="fa-solid fa-camera"></i> <div><b><?php echo $txt['q1_t']; ?></b><br><?php echo $txt['q1_d']; ?></div></li>
                <li><i class="fa-solid fa-mobile-screen"></i> <div><b><?php echo $txt['q2_t']; ?></b><br><?php echo $txt['q2_d']; ?></div></li>
            </ul>
        </aside>

    </main>
</div>

<script>
    const lang = {
        processing: "<?php echo $lang=='tr'?'Bağlantı Şifreleniyor...':'Encrypting Link...'; ?>",
        success: "<?php echo $lang=='tr'?'İşlem Başarılı!':'Success!'; ?>",
        error: "<?php echo $lang=='tr'?'Hata!':'Error!'; ?>",
        err_invalid: "<?php echo $lang=='tr'?'Geçerli bir URL girin.':'Please enter a valid URL.'; ?>",
        err_taken: "<?php echo $lang=='tr'?'Bu özel isim zaten alınmış!':'This alias is already taken!'; ?>",
        err_limit: "<?php echo $lang=='tr'?'5 adet ücretsiz limitinizi doldurdunuz. Sınırsız kullanım için kayıt olun!':'You reached your 5 free limits. Please register for unlimited use!'; ?>",
        err_sys: "<?php echo $lang=='tr'?'Sistemsel bir hata oluştu.':'A system error occurred.'; ?>",
        err_spam: "<?php echo $lang=='tr'?'Çok hızlı işlem yapıyorsunuz. Lütfen 5 saniye bekleyin!':'You are acting too fast. Please wait 5 seconds!'; ?>",
        err_alias_len: "<?php echo $lang=='tr'?'Özel isim 3 ile 20 karakter arasında olmalıdır.':'Alias must be between 3 and 20 characters.'; ?>",
        err_bot: "<?php echo $lang=='tr'?'Bot aktivitesi tespit edildi.':'Bot activity detected.'; ?>",
        copied: "<?php echo $lang=='tr'?'Kopyalandı!':'Copied!'; ?>"
    };

    document.getElementById('shortenerForm').addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        let longUrl = document.getElementById('long_url').value;
        let customAlias = document.getElementById('custom_alias').value;
        let linkPassword = document.getElementById('link_password').value; // Şifreyi alıyoruz
        let botTrap = document.getElementById('website_url_trap').value;

        Swal.fire({
            title: lang.processing,
            background: '#1e293b', color: '#06b6d4', allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let formData = new FormData();
        formData.append('long_url', longUrl);
        formData.append('custom_alias', customAlias);
        formData.append('link_password', linkPassword); // Şifreyi PHP'ye gönderiyoruz
        formData.append('website_url_trap', botTrap);

        fetch('ajax.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({
                    icon: 'success', title: lang.success,
                    background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#06b6d4'
                }).then(() => {
                    document.getElementById('resultBox').style.display = 'block';
                    document.getElementById('generatedLink').innerText = data.short_link;
                    document.getElementById('qrImage').src = data.qr_url;
                    
                    let remCount = document.getElementById('remCount');
                    if(remCount) {
                        remCount.innerText = data.remaining;
                        document.getElementById('remFill').style.width = (data.remaining * 20) + '%';
                    }
                    
                    document.getElementById('shortenerForm').reset();
                });
            } else if(data.status === 'limit_reached') {
                Swal.fire({ icon: 'warning', title: lang.error, text: lang.err_limit, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#f59e0b' });
            } else if(data.message === 'spam_cooldown') {
                Swal.fire({ icon: 'warning', title: lang.error, text: lang.err_spam, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#f59e0b' });
            } else if(data.message === 'alias_length') {
                Swal.fire({ icon: 'error', title: lang.error, text: lang.err_alias_len, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#ef4444' });
            } else if(data.message === 'bot_detected') {
                Swal.fire({ icon: 'error', title: 'STOP', text: lang.err_bot, background: '#500', color: '#fff', confirmButtonColor: '#000' });
            } else if(data.message === 'invalid_url') {
                Swal.fire({ icon: 'error', title: lang.error, text: lang.err_invalid, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#ef4444' });
            } else if(data.message === 'alias_taken') {
                Swal.fire({ icon: 'error', title: lang.error, text: lang.err_taken, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#ef4444' });
            } else {
                Swal.fire({ icon: 'error', title: lang.error, text: lang.err_sys, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#ef4444' });
            }
        }).catch(error => { Swal.fire('Error!', 'Connection failed.', 'error'); });
    });

    function copyToClipboard() {
        const link = document.getElementById('generatedLink').innerText;
        navigator.clipboard.writeText(link).then(() => {
            Swal.fire({ icon: 'info', title: lang.copied, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#0f172a', color: '#06b6d4' });
        });
    }
</script>

</body>
</html>

<?php include 'footer.php'; ?>