<?php
require_once 'database.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(isset($_SESSION['user_id'])) { header("Location: index"); exit; }

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

$max_attempts = 5; 
$lockout_time = 900; 
$swal_script = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
        $time_diff = time() - $_SESSION['last_failed_login'];
        if ($time_diff < $lockout_time) {
            $kalan = ceil(($lockout_time - $time_diff) / 60);
            die("GÜVENLİK: Çok fazla hatalı deneme. $kalan dakika bekleyin.");
        } else { $_SESSION['login_attempts'] = 0; }
    }

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die("Security Error!"); }
    if (!empty($_POST['website_url_trap'])) { die("Bot detected."); }

    $user_input = trim($_POST['username']);
    $password = $_POST['password'];

    $query = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $query->execute([$user_input, $user_input]);
    $user = $query->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['login_attempts'] = 0;
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $swal_script = "Swal.fire({ icon: 'success', title: 'Bağlantı Kuruldu', text: 'Sisteme giriş yapıldı.', background: '#1e293b', color: '#f8fafc', showConfirmButton: false, timer: 1500 }).then(() => { window.location.href = 'index'; });";
    } else {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_failed_login'] = time();
        $kalan_hak = $max_attempts - $_SESSION['login_attempts'];
        $swal_script = "Swal.fire({ icon: 'error', title: 'Hatalı Bilgi', text: 'Kalan deneme hakkınız: $kalan_hak', background: '#1e293b', color: '#f8fafc' });";
    }
}
include 'header.php'; 
?>

<style>
    .auth-wrapper {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
    }

    .auth-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 900px;
        width: 100%;
        background: #1e293b;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(6, 182, 212, 0.2);
        box-shadow: 0 25px 60px rgba(0,0,0,0.6);
    }

    /* Sol Taraf: İkonik Panel */
    .auth-info {
        padding: 40px;
        background: rgba(15, 23, 42, 0.6);
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-right: 1px solid rgba(6, 182, 212, 0.1);
    }

    .feature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 25px;
    }

    .feature-item {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(255,255,255,0.03);
        border-radius: 15px;
        padding: 20px 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
        transition: 0.3s;
    }

    .feature-item:hover {
        border-color: #06b6d4;
        background: rgba(6, 182, 212, 0.05);
    }

    .icon-circle {
        width: 38px;
        height: 38px;
        background: rgba(6, 182, 212, 0.1);
        color: #06b6d4;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .feature-item h4 { color: #fff; font-size: 12px; margin: 0 0 5px 0; font-weight: 600; text-transform: uppercase; }
    .feature-item p { color: #94a3b8; font-size: 10px; margin: 0; opacity: 0.8; }

    /* Sağ Taraf: Form */
    .auth-form-area { padding: 50px; display: flex; flex-direction: column; justify-content: center; }

    .input-group { position: relative; margin-bottom: 15px; }
    .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #06b6d4; }
    
    .auth-input {
        width: 100%;
        padding: 14px 15px 14px 45px;
        background: #0f172a;
        border: 1px solid #334155;
        color: #fff;
        border-radius: 10px;
        outline: none;
        transition: 0.3s;
    }
    .auth-input:focus { border-color: #06b6d4; background: rgba(15, 23, 42, 1); }

    .btn-submit {
        width: 100%;
        padding: 16px;
        background: #06b6d4;
        color: #0f172a;
        border: none;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-submit:hover { box-shadow: 0 0 25px rgba(6, 182, 212, 0.5); transform: translateY(-2px); }

    @media (max-width: 850px) {
        .auth-grid { grid-template-columns: 1fr; }
        .auth-info { display: none; }
    }
</style>

<div class="auth-wrapper">
    <div class="auth-grid">
        <div class="auth-info">
            <h2 style="color: white; margin: 0; font-size: 26px;">Yönetim<span>Paneli</span></h2>
            <p style="color: #94a3b8; font-size: 13px; margin: 8px 0 0 0;">Verilerini kontrol etmeye hazır mısın?</p>
            
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon-circle"><i class="fa-solid fa-gauge-high"></i></div>
                    <h4>Hız</h4>
                    <p>Anlık Kontrol</p>
                </div>
                <div class="feature-item">
                    <div class="icon-circle"><i class="fa-solid fa-fingerprint"></i></div>
                    <h4>Güven</h4>
                    <p>Uçtan Uca Zırh</p>
                </div>
                <div class="feature-item">
                    <div class="icon-circle"><i class="fa-solid fa-microchip"></i></div>
                    <h4>Zeka</h4>
                    <p>Akıllı Analiz</p>
                </div>
                <div class="feature-item">
                    <div class="icon-circle"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h4>Bulut</h4>
                    <p>Her Yerden Erişim</p>
                </div>
            </div>
        </div>

        <div class="auth-form-area">
            <h3 style="color: white; margin-bottom: 25px; text-align: center;">Giriş Yap</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website_url_trap" style="display:none;" tabindex="-1">
                
                <div class="input-group">
                    <i class="fa-solid fa-user-astronaut"></i>
                    <input type="text" name="username" class="auth-input" placeholder="Kullanıcı Adı / E-posta" required autofocus>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-shuttle-space"></i>
                    <input type="password" name="password" class="auth-input" placeholder="Şifre" required>
                </div>

                <button type="submit" class="btn-submit">SİSTEME GİRİŞ YAP</button>
            </form>
            <p style="text-align: center; margin-top: 20px; color: #94a3b8; font-size: 13px;">
                Henüz kayıt olmadın mı? <a href="register" style="color: #06b6d4; text-decoration: none; font-weight: bold;">Hemen Katıl</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script><?php echo $swal_script; ?></script>

<?php include 'footer.php'; ?>