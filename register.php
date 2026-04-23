<?php
require_once 'database.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

$swal_script = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { die("CSRF Error!"); }
    if (!empty($_POST['website_url_trap'])) { die("Bot activity."); }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['password_confirm'];

    if ($password !== $confirm) {
        $swal_script = "Swal.fire({ icon: 'error', title: 'Hata', text: 'Şifreler uyuşmuyor.', background: '#1e293b', color: '#f8fafc' });";
    } else {
        $check = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        
        if ($check->rowCount() > 0) {
            $swal_script = "Swal.fire({ icon: 'error', title: 'Hata', text: 'Bu bilgilerle zaten bir hesap var.', background: '#1e293b', color: '#f8fafc' });";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($ins->execute([$username, $email, $hashed])) {
                $swal_script = "Swal.fire({ icon: 'success', title: 'Başarılı!', text: 'Hesabınız oluşturuldu. Giriş yapabilirsiniz.', background: '#1e293b', color: '#f8fafc', timer: 2000, showConfirmButton: false }).then(() => { window.location.href = 'login'; });";
            }
        }
    }
}
include 'header.php'; 
?>

<style>
    .reg-wrapper {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
    }

    .reg-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        max-width: 1000px;
        width: 100%;
        background: #1e293b;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(6, 182, 212, 0.2);
        box-shadow: 0 0 40px rgba(0,0,0,0.5);
    }

    /* Sol Panel: Özellikler Gridi */
    .reg-info {
        padding: 50px;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(15, 23, 42, 1) 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-top: 30px;
    }

    .info-item {
        padding: 20px;
        background: rgba(15, 23, 42, 0.5);
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.05);
        transition: 0.3s;
    }

    .info-item:hover {
        border-color: #06b6d4;
        transform: translateY(-5px);
    }

    .info-item i {
        font-size: 24px;
        color: #06b6d4;
        margin-bottom: 10px;
    }

    .info-item h4 { color: white; margin-bottom: 5px; font-size: 14px; }
    .info-item p { color: #94a3b8; font-size: 12px; line-height: 1.4; }

    /* Sağ Panel: Form */
    .reg-form-area {
        padding: 50px;
        background: #1e293b;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .reg-input-group {
        position: relative;
        margin-bottom: 15px;
    }

    .reg-input-group i {
        position: absolute;
        left: 15px;
        top: 15px;
        color: #06b6d4;
    }

    .reg-input {
        width: 100%;
        padding: 14px 14px 14px 45px;
        background: #0f172a;
        border: 1px solid #334155;
        color: white;
        border-radius: 8px;
        outline: none;
        transition: 0.3s;
    }

    .reg-input:focus { border-color: #06b6d4; box-shadow: 0 0 10px rgba(6, 182, 212, 0.2); }

    .btn-reg {
        width: 100%;
        padding: 16px;
        background: #06b6d4;
        color: #0f172a;
        border: none;
        border-radius: 8px;
        font-weight: 800;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-reg:hover { box-shadow: 0 0 20px #06b6d4; transform: scale(1.02); }

    @media (max-width: 850px) {
        .reg-grid { grid-template-columns: 1fr; }
        .reg-info { display: none; }
    }
</style>

<div class="reg-wrapper">
    <div class="reg-grid">
        
        <div class="reg-info">
            <h2 style="color: white; font-size: 32px;">Aras<span>PHP</span></h2>
            <p style="color: #94a3b8; margin-top: 10px;">Link yönetimini bir üst seviyeye taşı. Ücretsiz üye ol ve avantajları yakala.</p>
            
            <div class="info-grid">
                <div class="info-item">
                    <i class="fa-solid fa-chart-line"></i>
                    <h4>İstatistikler</h4>
                    <p>Linklerini kimlerin tıkladığını anlık takip et.</p>
                </div>
                <div class="info-item">
                    <i class="fa-solid fa-qrcode"></i>
                    <h4>QR Kod</h4>
                    <p>Her linke özel karekod otomatik oluşturulur.</p>
                </div>
                <div class="info-item">
                    <i class="fa-solid fa-lock"></i>
                    <h4>Güvenlik</h4>
                    <p>Linklerini şifreleyerek erişimi kısıtla.</p>
                </div>
                <div class="info-item">
                    <i class="fa-solid fa-bolt"></i>
                    <h4>Hız</h4>
                    <p>Dünya çapında en hızlı yönlendirme motoru.</p>
                </div>
            </div>
        </div>

        <div class="reg-form-area">
            <h3 style="color: white; margin-bottom: 30px; font-size: 24px; text-align: center;">Yeni Hesap Oluştur</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="website_url_trap" style="display:none;" tabindex="-1">
                
                <div class="reg-input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" class="reg-input" placeholder="Kullanıcı Adı" required>
                </div>

                <div class="reg-input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" class="reg-input" placeholder="E-posta Adresi" required>
                </div>

                <div class="reg-input-group">
                    <i class="fa-solid fa-key"></i>
                    <input type="password" name="password" class="reg-input" placeholder="Şifre" required>
                </div>

                <div class="reg-input-group">
                    <i class="fa-solid fa-shield-check"></i>
                    <input type="password" name="password_confirm" class="reg-input" placeholder="Şifre Tekrar" required>
                </div>

                <button type="submit" class="btn-reg">KAYIT OL VE BAŞLA</button>
            </form>

            <p style="text-align: center; margin-top: 25px; color: #94a3b8; font-size: 14px;">
                Zaten bir hesabın var mı? <a href="login" style="color: #06b6d4; text-decoration: none; font-weight: 600;">Giriş Yap</a>
            </p>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script><?php echo $swal_script; ?></script>

<?php include 'footer.php'; ?>