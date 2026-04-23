<?php
session_start();
require_once 'database.php';

if (isset($_SESSION['last_action']) && (time() - $_SESSION['last_action']) < 5) {
    echo json_encode(['status' => 'error', 'message' => 'spam_cooldown']);
    exit;
}

    $link_password = !empty($_POST['link_password']) ? password_hash($_POST['link_password'], PASSWORD_DEFAULT) : NULL;

if($_POST) {
    $_SESSION['last_action'] = time();

    if (!empty($_POST['website_url_trap'])) {
        echo json_encode(['status' => 'error', 'message' => 'bot_detected']);
        exit;
    }

    $long_url = trim($_POST['long_url'] ?? '');
    $custom_alias = trim($_POST['custom_alias'] ?? '');
    $is_logged_in = isset($_SESSION['user_id']);
    
    if(!$is_logged_in) {
        if(!isset($_SESSION['guest_count'])) $_SESSION['guest_count'] = 0;
        if($_SESSION['guest_count'] >= 5) {
            echo json_encode(['status' => 'limit_reached']);
            exit;
        }
    }

    $long_url = filter_var($long_url, FILTER_SANITIZE_URL);
    if(!filter_var($long_url, FILTER_VALIDATE_URL)) {
        echo json_encode(['status' => 'error', 'message' => 'invalid_url']);
        exit;
    }

    if (preg_match('/^(javascript|data|vbscript):/i', $long_url)) {
        echo json_encode(['status' => 'error', 'message' => 'invalid_url']);
        exit;
    }

    if(!empty($custom_alias)) {
        $custom_alias = preg_replace('/[^a-zA-Z0-9-]/', '', $custom_alias);
        
        if(strlen($custom_alias) < 3 || strlen($custom_alias) > 20) {
            echo json_encode(['status' => 'error', 'message' => 'alias_length']);
            exit;
        }

        $check = $db->prepare("SELECT id FROM links WHERE short_code = ?");
        $check->execute([$custom_alias]);
        if($check->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'alias_taken']);
            exit;
        }
        $short_code = $custom_alias;
    } else {
        $short_code = substr(md5(uniqid(rand(), true)), 0, 6);
    }

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    $insert = $db->prepare("INSERT INTO links (long_url, short_code, password, user_id) VALUES (?, ?, ?, ?)");
    if($insert->execute([$long_url, $short_code, $link_password, $user_id])) {
        $site_url = "http://localhost/link-kisaltici/"; 
        $final_link = $site_url . $short_code;
        
        if(!$is_logged_in) $_SESSION['guest_count']++;
        $remaining = $is_logged_in ? 'Unlimited' : (5 - $_SESSION['guest_count']);

        echo json_encode([
            'status' => 'success', 
            'short_link' => $final_link,
            'qr_url' => "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($final_link),
            'remaining' => $remaining
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'system_error']);
    }
}
?>