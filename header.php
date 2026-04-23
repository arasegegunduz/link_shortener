<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET['lang'])) {
    $allowed_langs = ['tr', 'en'];
    if(in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    $current_page = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $current_page");
    exit;
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArasPHP | Link Management</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --accent: #06b6d4;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Chakra Petch', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); }

        .navbar {
            background-color: var(--card-bg);
            padding: 15px 30px;
            border-bottom: 2px solid var(--accent);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            position: sticky; top: 0; z-index: 1000;
        }

        .nav-container {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* LOGO BOYUTU GÜNCELLENDİ */
        .logo-area { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-img { 
            width: 90px; /* Logo burada büyütüldü */
            height: auto; 
            filter: drop-shadow(0 0 8px var(--accent)); 
        }

        .nav-menu { display: flex; align-items: center; gap: 20px; }
        .nav-link { 
            text-decoration: none; color: var(--text-muted); 
            font-size: 14px; font-weight: 600; transition: 0.3s;
            text-transform: uppercase;
        }
        .nav-link:hover { color: var(--accent); }
        
        .auth-btns { display: flex; gap: 10px; align-items: center; }
        .btn-auth {
            padding: 10px 20px; border-radius: 6px; text-decoration: none;
            font-size: 13px; font-weight: 700; transition: 0.3s;
            border: 1px solid var(--accent);
        }
        .btn-login { color: var(--accent); background: transparent; }
        .btn-register { background: var(--accent); color: var(--bg-color); }
        .btn-register:hover { box-shadow: 0 0 15px var(--accent); }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index" class="logo-area">
            <img src="logo.png" alt="ArasPHP Logo" class="logo-img">
        </a>

        <div class="nav-menu">
            <div class="lang-switch" style="margin-right: 15px;">
                <a href="?lang=tr" class="nav-link" style="<?php echo $lang=='tr'?'color:var(--accent); border-bottom: 2px solid var(--accent);':'' ?>">TR</a>
                <span style="color: var(--text-muted); margin: 0 5px;">|</span>
                <a href="?lang=en" class="nav-link" style="<?php echo $lang=='en'?'color:var(--accent); border-bottom: 2px solid var(--accent);':'' ?>">EN</a>
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard" class="nav-link">Panel</a>
                <a href="logout" class="btn-auth btn-login" style="border-color: #ef4444; color: #ef4444;">LOGOUT</a>
            <?php else: ?>
                <div class="auth-btns">
                    <a href="login" class="btn-auth btn-login"><?php echo ($lang == 'tr' ? 'GİRİŞ YAP' : 'LOGIN'); ?></a>
                    <a href="register" class="btn-auth btn-register"><?php echo ($lang == 'tr' ? 'KAYIT OL' : 'REGISTER'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>