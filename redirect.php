<?php
session_start();
require_once 'database.php';

if(isset($_GET['code'])) {
    
    $code = strip_tags(trim($_GET['code']));
    
    $query = $db->prepare("SELECT * FROM links WHERE short_code = ?");
    $query->execute([$code]);
    $link = $query->fetch(PDO::FETCH_ASSOC);

    if($link) {
        $target_url = $link['long_url'];
        $is_unlocked = false;
        $pass_error = false;

        if(!empty($link['password'])) {
            if(isset($_POST['link_password'])) {
                if(password_verify($_POST['link_password'], $link['password'])) {
                    $is_unlocked = true;
                } else {
                    $pass_error = true;
                }
            }
        } else {
            $is_unlocked = true;
        }

        if($is_unlocked) {
            $update = $db->prepare("UPDATE links SET clicks = clicks + 1 WHERE short_code = ?");
            $update->execute([$code]);
            
            ?>
            <!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Yönlendiriliyorsunuz... | ArasPHP</title>
                <meta http-equiv="refresh" content="5;url=<?php echo htmlspecialchars($target_url); ?>">
                <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <style>
                    :root { --bg-color: #0f172a; --card-bg: #1e293b; --accent: #06b6d4; --text-main: #f8fafc; --text-muted: #94a3b8; }
                    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Chakra Petch', sans-serif; }
                    body { background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); color: var(--text-main); height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
                    .cyber-lines { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(rgba(15, 23, 42, 0.9) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, 0.9) 1px, transparent 1px); background-size: 30px 30px; z-index: 0; opacity: 0.1; }
                    .redirect-box { background: var(--card-bg); padding: 40px 30px; border-radius: 12px; text-align: center; width: 90%; max-width: 450px; z-index: 1; border: 1px solid rgba(6, 182, 212, 0.2); box-shadow: 0 0 30px rgba(0, 0, 0, 0.5), inset 0 0 15px rgba(6, 182, 212, 0.05); }
                    .logo { font-size: 28px; font-weight: 700; margin-bottom: 20px; letter-spacing: 1px; }
                    .logo span { color: var(--accent); }
                    .spinner { width: 80px; height: 80px; border: 4px solid rgba(6, 182, 212, 0.1); border-left-color: var(--accent); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px auto; position: relative; }
                    .countdown-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 24px; font-weight: bold; color: var(--text-main); }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                    .message { font-size: 18px; margin-bottom: 10px; color: var(--text-muted); }
                    .destination { font-size: 14px; color: var(--accent); word-break: break-all; margin-bottom: 25px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 6px;}
                    .btn-manual { display: inline-block; padding: 12px 25px; background: transparent; color: var(--text-muted); border: 1px solid #334155; border-radius: 6px; text-decoration: none; font-size: 14px; transition: 0.3s; }
                    .btn-manual:hover { border-color: var(--accent); color: var(--accent); background: rgba(6, 182, 212, 0.1); }
                </style>
            </head>
            <body>
                <div class="cyber-lines"></div>
                <div class="redirect-box">
                    <div class="logo">&lt;Aras<span>PHP</span>/&gt;</div>
                    <div class="spinner"><div class="countdown-text" id="timer">5</div></div>
                    <div class="message">Hedef bağlantıya yönlendiriliyorsunuz...</div>
                    <div class="destination"><i class="fa-solid fa-lock-open"></i> Güvenli Bağlantı Hazırlanıyor</div>
                    <a href="<?php echo htmlspecialchars($target_url); ?>" class="btn-manual">Beklemek İstemiyorum</a>
                </div>
                <script>
                    let timeLeft = 5;
                    const timerElement = document.getElementById('timer');
                    const targetUrl = "<?php echo htmlspecialchars($target_url); ?>";
                    const countdown = setInterval(() => {
                        timeLeft--; timerElement.innerText = timeLeft;
                        if (timeLeft <= 0) { clearInterval(countdown); window.location.href = targetUrl; }
                    }, 1000);
                </script>
            </body>
            </html>
            <?php
            exit;
        }

        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Kilitli Bağlantı | ArasPHP</title>
            <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                :root { --bg-color: #0f172a; --card-bg: #1e293b; --accent: #06b6d4; --text-main: #f8fafc; --text-muted: #94a3b8; }
                * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Chakra Petch', sans-serif; }
                body { background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); color: var(--text-main); height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; }
                .cyber-lines { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(rgba(15, 23, 42, 0.9) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, 0.9) 1px, transparent 1px); background-size: 30px 30px; z-index: 0; opacity: 0.1; }
                .lock-box { background: var(--card-bg); padding: 50px 40px; border-radius: 12px; text-align: center; width: 90%; max-width: 450px; z-index: 1; border: 1px solid rgba(6, 182, 212, 0.3); box-shadow: 0 0 30px rgba(0, 0, 0, 0.7), inset 0 0 20px rgba(6, 182, 212, 0.1); position: relative; }
                .lock-icon { font-size: 50px; color: var(--accent); margin-bottom: 20px; filter: drop-shadow(0 0 10px var(--accent)); }
                .title { font-size: 24px; font-weight: 700; margin-bottom: 10px; }
                .desc { color: var(--text-muted); font-size: 15px; margin-bottom: 30px; }
                .input-group { position: relative; margin-bottom: 20px; }
                .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--accent); }
                .pass-input { width: 100%; padding: 15px 15px 15px 45px; background: rgba(15, 23, 42, 0.8); border: 1px solid #334155; color: white; border-radius: 6px; outline: none; font-size: 16px; transition: 0.3s; }
                .pass-input:focus { border-color: var(--accent); box-shadow: 0 0 10px rgba(6,182,212,0.2); }
                .btn-unlock { background: var(--accent); color: #0f172a; border: none; padding: 15px; width: 100%; font-size: 18px; font-weight: 700; border-radius: 6px; cursor: pointer; transition: 0.3s; letter-spacing: 1px; }
                .btn-unlock:hover { box-shadow: 0 0 20px var(--accent); transform: translateY(-2px); }
            </style>
        </head>
        <body>
            <div class="cyber-lines"></div>
            <div class="lock-box">
                <i class="fa-solid fa-shield-halved lock-icon"></i>
                <h2 class="title">Korunan Bağlantı</h2>
                <p class="desc">Bu linke erişmek için şifreyi girmelisiniz.</p>
                
                <form method="POST" action="">
                    <div class="input-group">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" name="link_password" class="pass-input" placeholder="Şifreyi Girin" required autofocus>
                    </div>
                    <button type="submit" class="btn-unlock"><i class="fa-solid fa-unlock-keyhole"></i> KİLİDİ AÇ</button>
                </form>
            </div>

            <?php if($pass_error): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erişim Reddedildi',
                    text: 'Hatalı bir şifre girdiniz!',
                    background: '#1e293b',
                    color: '#f8fafc',
                    confirmButtonColor: '#ef4444'
                });
            </script>
            <?php endif; ?>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo "<body style='background:#0f172a; color:#06b6d4; text-align:center; padding:100px; font-family:sans-serif;'>";
        echo "<h1>404 - BAĞLANTI BULUNAMADI</h1>";
        echo "<p>Aradığınız kısa link sistemde yok veya süresi dolmuş olabilir.</p>";
        echo "<br><a href='index' style='color:#f8fafc; padding:10px 20px; border:1px solid #06b6d4; text-decoration:none; border-radius:5px;'>Ana Sayfaya Dön</a>";
        echo "</body>";
    }
} else {
    header("Location: index");
    exit;
}
?>