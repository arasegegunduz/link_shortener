<?php
define('SITE_URL', 'http://localhost/link-kisaltici/'); 

define('RECAPTCHA_SITE_KEY', '6Lfux8UsAAAAAHKXenlsYHDHusTx52laXBG2wRr5');   
define('RECAPTCHA_SECRET_KEY', '6Lfux8UsAAAAAOU2jrNYwro0b8nnK2q7Kx0MKtnT'); 

$host = 'localhost'; //Kendi Bilgilerin İle Değiştir!
$dbname = 'link_kisaltici';
$user = 'root';
$pass = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Sistem Hatası: Veritabanı bağlantısı kurulamadı!");
}
?>