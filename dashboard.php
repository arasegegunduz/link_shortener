<?php
require_once 'database.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])) {
    header("Location: index");
    exit;
}

$user_id = $_SESSION['user_id'];
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$t = [
    'tr' => [
        'db_title' => 'KONTROL PANELİ',
        'new_link' => 'YENİ LİNK',
        'total_links' => 'Toplam Link',
        'total_clicks' => 'Toplam Tıklama',
        'safe_links' => 'Şifreli Linkler',
        'col_short' => 'KISA LİNK',
        'col_long' => 'ORİJİNAL URL',
        'col_click' => 'TIKLAMA',
        'col_status' => 'DURUM',
        'col_date' => 'TARİH',
        'col_action' => 'İŞLEMLER',
        'status_pass' => 'Şifreli',
        'status_open' => 'Açık',
        'empty' => 'Henüz hiç link oluşturulmamış.',
        'confirm_title' => 'Emin misin?',
        'confirm_text' => 'Bu işlem geri alınamaz!',
        'confirm_btn' => 'Evet, Sil!',
        'cancel_btn' => 'Vazgeç',
        'copy_msg' => 'Kopyalandı!',
        'del_msg' => 'Bağlantı başarıyla kaldırıldı.'
    ],
    'en' => [
        'db_title' => 'USER DASHBOARD',
        'new_link' => 'NEW LINK',
        'total_links' => 'Total Links',
        'total_clicks' => 'Total Clicks',
        'safe_links' => 'Protected Links',
        'col_short' => 'SHORT LINK',
        'col_long' => 'ORIGINAL URL',
        'col_click' => 'CLICKS',
        'col_status' => 'STATUS',
        'col_date' => 'DATE',
        'col_action' => 'ACTIONS',
        'status_pass' => 'Locked',
        'status_open' => 'Public',
        'empty' => 'No links created yet.',
        'confirm_title' => 'Are you sure?',
        'confirm_text' => 'This action cannot be undone!',
        'confirm_btn' => 'Yes, Delete!',
        'cancel_btn' => 'Cancel',
        'copy_msg' => 'Copied!',
        'del_msg' => 'Link removed successfully.'
    ]
];
$txt = $t[$lang];

if(isset($_GET['delete']) && isset($_GET['token'])) {
    if(hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $link_id = (int)$_GET['delete'];
        $delete = $db->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
        $delete->execute([$link_id, $user_id]);
        header("Location: dashboard?success=deleted");
        exit;
    }
}

$stats_query = $db->prepare("SELECT COUNT(id) as total_links, SUM(clicks) as total_clicks, COUNT(password) as protected_links FROM links WHERE user_id = ?");
$stats_query->execute([$user_id]);
$stats = $stats_query->fetch();

$query = $db->prepare("SELECT * FROM links WHERE user_id = ? ORDER BY created_at DESC");
$query->execute([$user_id]);
$my_links = $query->fetchAll();

include 'header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .dashboard-wrapper { max-width: 1300px; margin: 30px auto; padding: 0 20px; }
    
    /* İstatistik Kartları */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--card-bg); border-radius: 12px; padding: 25px; border: 1px solid rgba(6, 182, 212, 0.1); display: flex; align-items: center; gap: 20px; transition: 0.3s; }
    .stat-card:hover { border-color: var(--accent); box-shadow: 0 0 15px rgba(6, 182, 212, 0.1); }
    .stat-icon { width: 50px; height: 50px; background: rgba(6, 182, 212, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--accent); }
    .stat-info h4 { font-size: 13px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px; }
    .stat-info p { font-size: 22px; font-weight: 700; color: var(--text-main); }

    /* Ana Tablo Kartı */
    .main-card { background: var(--card-bg); border-radius: 12px; padding: 30px; border: 1px solid rgba(6, 182, 212, 0.2); box-shadow: 0 10px 30px rgba(0,0,0,0.4); }
    .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 15px; color: var(--accent); font-size: 12px; border-bottom: 2px solid rgba(6, 182, 212, 0.3); }
    td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
    tr:hover { background: rgba(255,255,255,0.02); }

    .short-link-box { display: flex; align-items: center; gap: 10px; color: var(--accent); font-weight: bold; }
    .btn-copy-mini { background: none; border: 1px solid #334155; color: var(--text-muted); padding: 5px 8px; border-radius: 4px; cursor: pointer; transition: 0.3s; }
    .btn-copy-mini:hover { color: var(--accent); border-color: var(--accent); }

    .badge-click { background: rgba(6, 182, 212, 0.1); color: var(--accent); padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 12px; }
    .btn-del { color: #ef4444; font-size: 18px; transition: 0.3s; opacity: 0.6; }
    .btn-del:hover { opacity: 1; transform: scale(1.1); }
</style>

<div class="dashboard-wrapper">
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-link"></i></div>
            <div class="stat-info">
                <h4><?php echo $txt['total_links']; ?></h4>
                <p><?php echo $stats['total_links']; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
            <div class="stat-info">
                <h4><?php echo $txt['total_clicks']; ?></h4>
                <p><?php echo (int)$stats['total_clicks']; ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="stat-info">
                <h4><?php echo $txt['safe_links']; ?></h4>
                <p><?php echo $stats['protected_links']; ?></p>
            </div>
        </div>
    </div>

    <div class="main-card">
        <div class="card-header">
            <h2 class="db-title"><?php echo $txt['db_title']; ?></h2>
            <a href="index" style="text-decoration:none; color:var(--accent); font-weight:700; font-size:13px;">
                <i class="fa-solid fa-plus-circle"></i> <?php echo $txt['new_link']; ?>
            </a>
        </div>

        <div class="table-responsive">
            <?php if(count($my_links) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th><?php echo $txt['col_short']; ?></th>
                        <th><?php echo $txt['col_long']; ?></th>
                        <th><?php echo $txt['col_click']; ?></th>
                        <th><?php echo $txt['col_status']; ?></th>
                        <th><?php echo $txt['col_date']; ?></th>
                        <th style="text-align:right;"><?php echo $txt['col_action']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($my_links as $row): ?>
                    <?php $full_link = SITE_URL . $row['short_code']; ?>
                    <tr>
                        <td>
                            <div class="short-link-box">
                                <?php echo $row['short_code']; ?>
                                <button class="btn-copy-mini" onclick="copyDash('<?php echo $full_link; ?>')"><i class="fa-regular fa-copy"></i></button>
                            </div>
                        </td>
                        <td><div style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text-muted);"><?php echo htmlspecialchars($row['long_url']); ?></div></td>
                        <td><span class="badge-click"><?php echo $row['clicks']; ?></span></td>
                        <td>
                            <?php if(!empty($row['password'])): ?>
                                <i class="fa-solid fa-lock" style="color:#f59e0b;" title="<?php echo $txt['status_pass']; ?>"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-lock-open" style="color:var(--text-muted); opacity:0.3;" title="<?php echo $txt['status_open']; ?>"></i>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px; color:var(--text-muted);"><?php echo date("d.m.y", strtotime($row['created_at'])); ?></td>
                        <td style="text-align:right;">
                            <a href="javascript:void(0)" onclick="delLink(<?php echo $row['id']; ?>)" class="btn-del"><i class="fa-solid fa-trash-can"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                    <i class="fa-solid fa-folder-open" style="font-size:40px; margin-bottom:15px; display:block; opacity:0.2;"></i>
                    <?php echo $txt['empty']; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const dashLang = {
        title: "<?php echo $txt['confirm_title']; ?>",
        text: "<?php echo $txt['confirm_text']; ?>",
        btn: "<?php echo $txt['confirm_btn']; ?>",
        cancel: "<?php echo $txt['cancel_btn']; ?>",
        copy: "<?php echo $txt['copy_msg']; ?>",
        del: "<?php echo $txt['del_msg']; ?>"
    };

    function delLink(id) {
        Swal.fire({
            title: dashLang.title,
            text: dashLang.text,
            icon: 'warning',
            showCancelButton: true,
            background: '#1e293b', color: '#f8fafc',
            confirmButtonColor: '#ef4444', cancelButtonColor: '#334155',
            confirmButtonText: dashLang.btn, cancelButtonText: dashLang.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "dashboard?delete=" + id + "&token=<?php echo $_SESSION['csrf_token']; ?>";
            }
        })
    }

    function copyDash(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({ icon: 'success', title: dashLang.copy, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#0f172a', color: '#06b6d4' });
        });
    }

    <?php if(isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
        Swal.fire({ icon: 'success', title: dashLang.del, background: '#1e293b', color: '#f8fafc', confirmButtonColor: '#06b6d4' });
    <?php endif; ?>
</script>

</body>
</html>

<?php include 'footer.php'; ?>