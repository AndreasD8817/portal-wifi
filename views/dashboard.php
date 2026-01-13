<?php 
// Hitung perangkat yang sudah terpakai
$used = count($devices);

// Cek apakah user dikunci admin (1=Bisa Edit, 0=Terkunci)
// Jika can_edit = 0, maka $isLocked = true
$isLocked = !$currentUser['can_edit']; 

// Logika Mode Edit (Jika ada parameter ?edit_id di URL)
$editMode = isset($_GET['edit_id']);
$editData = ['username'=>'','password'=>'','device_name'=>'','id'=>''];

// Jika sedang edit, cari data perangkat yang sesuai ID
if ($editMode) {
    foreach($devices as $d) { 
        if($d['id'] == $_GET['edit_id']) {
            $editData = $d; 
            break;
        }
    }
}
?>

<!-- Navbar Mini (Atas) -->
<div class="d-flex justify-content-between align-items-center mb-4 p-2" style="background: rgba(0,0,0,0.3); border-radius: 50px;">
    <div class="px-3">
        <i class="fas fa-user-circle me-2"></i> <strong><?= htmlspecialchars($currentUser['nama']) ?></strong>
    </div>
    <div>
        <!-- Tombol Admin hanya muncul jika role ADMIN -->
        <?php if($currentUser['role'] === 'ADMIN'): ?>
            <a href="/admin" class="btn btn-sm btn-warning rounded-pill me-2">
                <i class="fas fa-cog"></i> Admin Panel
            </a>
        <?php endif; ?>
        
        <a href="/logout" class="btn btn-sm btn-outline-light rounded-pill">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<!-- Info Kuota -->
<div class="alert alert-info bg-transparent border-info text-white text-center">
    Kuota Perangkat: <strong><?= $used ?> / <?= $currentUser['quota'] ?></strong>
</div>

<!-- 
    FORM INPUT / EDIT 
    Muncul jika: Kuota belum penuh ATAU sedang dalam mode edit 
-->
<?php if($used < $currentUser['quota'] || $editMode): ?>
<div id="formContainer" class="mb-4">
    <h5 class="mb-3">
        <i class="fas fa-<?= $editMode ? 'edit' : 'plus-circle' ?> me-2"></i> 
        <?= $editMode ? 'Edit Wifi' : 'Daftar Perangkat Baru' ?>
    </h5>
    
    <form action="/save_device" method="POST">
        <!-- ID sembunyi untuk update -->
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <!-- Username Readonly jika Edit Mode (Tidak bisa diganti) -->
                <input type="text" name="username" class="form-control" placeholder="Tanpa Spasi" 
                       value="<?= htmlspecialchars($editData['username']) ?>" required 
                       oninput="this.value = this.value.replace(/\s/g, '')"
                       <?= $editMode ? 'readonly style="background: rgba(255,255,255,0.05); color: #aaa;"' : '' ?>>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Perangkat</label>
                <!-- Nama Perangkat selalu bisa diedit -->
                <input type="text" name="device_name" class="form-control" placeholder="Cth: Samsung A54" 
                       value="<?= htmlspecialchars($editData['device_name']) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <!-- 
                    Password Readonly JIKA: Sedang Edit DAN User dikunci Admin ($isLocked) 
                -->
                <input type="password" name="password" id="inputPass" class="form-control" 
                       value="<?= htmlspecialchars($editData['password']) ?>" required
                       <?= ($editMode && $isLocked) ? 'readonly style="background: rgba(255,255,255,0.05); color: #aaa;"' : '' ?>>
                
                <!-- Tombol Mata (Lihat Password) -->
                <span class="input-group-text" onclick="togglePass('inputPass', this)">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            
            <!-- Pesan Bantuan Berbeda Tergantung Status Kunci -->
            <?php if($editMode && $isLocked): ?>
                <small class="text-warning">
                    <i class="fas fa-lock"></i> Edit Password dikunci Admin. Anda hanya bisa mengganti nama perangkat.
                </small>
            <?php else: ?>
                <small class="text-white-50">
                    * Wajib: Huruf Besar, Huruf Kecil & Angka (Contoh: DprD2025)
                </small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-custom">
            <?= $editMode ? 'Update Data' : 'Simpan Data' ?>
        </button>
        
        <?php if($editMode): ?>
            <a href="/" class="btn btn-secondary w-100 rounded-pill mt-2">Batal Edit</a>
        <?php endif; ?>
    </form>
</div>
<?php endif; ?>

<hr class="border-white opacity-25">

<!-- TABEL DAFTAR PERANGKAT -->
<h5 class="mb-3 text-center">Perangkat Terdaftar</h5>
<div class="table-responsive">
    <table class="table table-glass">
        <thead>
            <tr>
                <th>Username</th>
                <th>Perangkat</th>
                <th>Password</th>
                <th class="text-center" width="1%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($devices as $idx => $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['username']) ?></td>
                <td><?= htmlspecialchars($d['device_name']) ?></td>
                <td>
                    <!-- Fitur Intip Password di Tabel -->
                    <div class="d-flex align-items-center justify-content-between" style="min-width: 150px;">
                        <span id="pass-txt-<?= $idx ?>" class="me-2">***</span>
                        <button type="button" class="btn btn-sm btn-link text-white p-0" onclick="toggleTablePass('pass-txt-<?= $idx ?>', '<?= htmlspecialchars($d['password']) ?>', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
                <td class="text-center">
                    <a href="/?edit_id=<?= $d['id'] ?>" class="btn btn-sm btn-info text-white rounded-circle" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if(empty($devices)): ?>
                <tr>
                    <td colspan="4" class="text-center text-white-50 py-3">Belum ada perangkat terdaftar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Tombol Pengajuan Tambah Kuota -->
<div class="mt-4 text-center">
    <button onclick="showUploadModal()" class="btn btn-outline-light rounded-pill w-100 p-2">
        <i class="fas fa-paper-plane me-2"></i> Pengajuan Tambah Device
    </button>
</div>

<!-- Script Javascript Khusus Dashboard -->
<script>
// Fungsi Toggle Mata di Input Form
function togglePass(id, btn) {
    let inp = document.getElementById(id);
    let icon = btn.querySelector('i');
    if (inp.type === "password") {
        inp.type = "text";
        icon.className = "fas fa-eye-slash";
    } else {
        inp.type = "password";
        icon.className = "fas fa-eye";
    }
}

// Fungsi Toggle Mata di Tabel
function toggleTablePass(spanId, realPass, btn) {
    let span = document.getElementById(spanId);
    let icon = btn.querySelector('i');
    if (span.innerText === "***") {
        span.innerText = realPass;
        icon.className = "fas fa-eye-slash";
    } else {
        span.innerText = "***";
        icon.className = "fas fa-eye";
    }
}

// Fungsi Pop-up Upload PDF
function showUploadModal() {
    Swal.fire({
        title: 'Pengajuan Tambah Device',
        html: `
            <div class='text-start'>
                <p class='small mb-1'>1. Download Form Pengajuan <a href='public/form_download/FORMULIR PERMOHONAN AKSES JARINGAN WiFi.pdf' class='text-info text-decoration-none' download>Disini</a></p>
                <p class='small mb-2'>2. Upload Formulir PDF (Max 10MB)</p>
                <form id='uploadForm' action='/upload_request' method='POST' enctype='multipart/form-data'>
                    <input type='file' name='pdf_file' class='form-control bg-dark text-white border-secondary' accept='application/pdf' required>
                </form>
            </div>
        `,
        background: '#1e293b', 
        color: '#fff',
        showCancelButton: true,
        confirmButtonText: 'Kirim Pengajuan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#38bdf8',
        cancelButtonColor: '#d33',
        preConfirm: () => {
            const fileInput = document.querySelector('input[name="pdf_file"]');
            if (!fileInput.files.length) {
                Swal.showValidationMessage('Mohon pilih file PDF terlebih dahulu');
                return false;
            }
            document.getElementById('uploadForm').submit();
        }
    });
}
</script>