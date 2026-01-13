<div class="card bg-transparent border-0">
    <div class="card-body">
        <h5 class="card-title text-center mb-4 text-white">Masuk Pegawai</h5>
        
        <!-- Form mengirim data ke index.php dengan parameter ?act=login -->
        <form action="/login" method="POST">
            <div class="mb-4">
                <label class="form-label text-white">NIK Pegawai</label>
                <div class="input-group">
                    <!-- Ikon ID Card untuk estetika -->
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <!-- Input tipe number agar di HP muncul keyboard angka -->
                    <input type="number" name="nik" class="form-control" placeholder="Contoh: 3578xxxxxxxx" required>
                </div>
                <div class="form-text text-white-50 small mt-2">
                    Gunakan NIK yang terdaftar di sistem kepegawaian.
                </div>
            </div>
            
            <button type="submit" class="btn btn-custom">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk
            </button>
        </form>
    </div>
</div>