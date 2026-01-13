<?php
$search = $_GET['search'] ?? '';
$pageNo = $_GET['p'] ?? 1;
$limit  = 10;
$offset = ($pageNo - 1) * $limit;

$sql = "SELECT u.*, (SELECT COUNT(*) FROM devices d WHERE d.user_nik = u.nik) as used FROM users u WHERE u.nama LIKE ? OR u.nik LIKE ? LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%"]);
$usersList = $stmt->fetchAll();

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nama LIKE ? OR nik LIKE ?");
$stmtCount->execute(["%$search%", "%$search%"]);
$totalPages = ceil($stmtCount->fetchColumn() / $limit);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="fas fa-shield-alt me-2"></i>Panel Admin</h4>
    <a href="/" class="btn btn-sm btn-outline-light rounded-pill">Kembali</a>
</div>

<?php 
$reqs = $pdo->query("SELECT r.*, u.nama FROM requests r JOIN users u ON r.user_nik = u.nik WHERE r.status='Pending'")->fetchAll();
if($reqs): ?>
<div class="alert alert-info bg-transparent border-info text-white mb-4">
    <i class="fas fa-bell me-2"></i> <strong>Pengajuan Masuk</strong>
    <table class="table table-sm table-borderless text-white mt-2">
        <?php foreach($reqs as $r): ?>
        <tr>
            <td><?= $r['created_at'] ?></td>
            <td><?= $r['nama'] ?> (<?= $r['user_nik'] ?>)</td>
            <td><a href="public/uploads/<?= $r['file_path'] ?>" target="_blank" class="btn btn-sm btn-light text-danger"><i class="fas fa-file-pdf"></i> PDF</a></td>
            <td><a href="/request_done?id=<?= $r['id'] ?>" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Selesai</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-6 mb-2">
        <a href="/global_lock?status=0" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Kunci SEMUA user?')">Kunci Semua</a>
        <a href="/global_lock?status=1" class="btn btn-sm btn-success rounded-pill" onclick="return confirm('Buka SEMUA user?')">Buka Semua</a>
    </div>
    <div class="col-md-6">
        <form action="/admin" method="GET">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-glass table-sm">
        <thead><tr class="text-center"><th>NIK</th><th>Nama</th><th>Terpakai</th><th>Kuota</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach($usersList as $u): ?>
            <tr>
                <td><?= $u['nik'] ?></td>
                <td><?= $u['nama'] ?></td>
                <td class="text-center"><?= $u['used'] ?></td>
                <td class="text-center" width="100">
                    <form action="/admin?act=update_quota" method="POST" class="d-flex">
                        <input type="hidden" name="nik" value="<?= $u['nik'] ?>">
                        <input type="number" name="quota" value="<?= $u['quota'] ?>" class="form-control form-control-sm text-center bg-dark text-white">
                        <button type="submit" class="btn btn-sm btn-link text-white"><i class="fas fa-save"></i></button>
                    </form>
                </td>
                <td class="text-center">
                    <a href="/admin?act=toggle_lock&nik=<?= $u['nik'] ?>&status=<?= $u['can_edit'] ? 0 : 1 ?>" class="btn btn-sm <?= $u['can_edit'] ? 'btn-success' : 'btn-danger' ?>"><i class="fas <?= $u['can_edit'] ? 'fa-lock-open' : 'fa-lock' ?>"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<nav class="mt-3">
    <ul class="pagination justify-content-center" style="flex-wrap: wrap;">
        <?php

        $num_links_to_show = 5;
        $half_links = floor($num_links_to_show / 2);

        $start = $pageNo - $half_links;
        $end = $pageNo + $half_links;

        if ($start < 1) {
            $start = 1;
            $end = $num_links_to_show;
        }

        if ($end > $totalPages) {
            $end = $totalPages;
            $start = $totalPages - $num_links_to_show + 1;
        }
        
        if ($start < 1) {
            $start = 1;
        }

        // Previous link
        if ($pageNo > 1) {
            echo '<li class="page-item"><a class="page-link" href="/admin?search=' . $search . '&p=' . ($pageNo - 1) . '">&laquo;</a></li>';
        }

        // First page link
        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="/admin?search=' . $search . '&p=1">1</a></li>';
            if ($start > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Page number links
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $pageNo) {
                echo '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="/admin?search=' . $search . '&p=' . $i . '">' . $i . '</a></li>';
            }
        }

        // Last page link
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="/admin?search=' . $search . '&p=' . $totalPages . '">' . $totalPages . '</a></li>';
        }

        // Next link
        if ($pageNo < $totalPages) {
            echo '<li class="page-item"><a class="page-link" href="/admin?search=' . $search . '&p=' . ($pageNo + 1) . '">&raquo;</a></li>';
        }

        ?>
    </ul>
</nav>