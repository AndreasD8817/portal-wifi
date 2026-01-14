<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Wifi Sekretariat DPRD Surabaya</title>

    <!-- 1. FAVICON (Ikon Tab Browser) -->
    <!-- Pastikan file gambar ada di: public/img/favicon.png -->
    <link rel="icon" href="public/img/wifi.png" type="image/png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* CSS Tema Biru Laut */
        :root { --primary-bg: #1e293b; --glass-bg: rgba(255, 255, 255, 0.1); --accent-color: #38bdf8; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh; color: white; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .glass-container {
            background: var(--glass-bg); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px; box-shadow: 0 8px 32px 0 rgba(0,0,0,0.3); padding: 40px; width: 100%; max-width: 900px;
        }
        .form-control { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: white; }
        .form-control:focus { background: rgba(255,255,255,0.15); border-color: var(--accent-color); color: white; box-shadow: 0 0 10px rgba(56,189,248,0.3); }
        .btn-custom { background: linear-gradient(90deg, #0284c7, #38bdf8); border: none; color: white; padding: 12px; border-radius: 50px; width: 100%; }
        
        /* --- STYLE TABEL (DIPERBARUI) --- */
        .table-glass {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            color: white;
            margin-top: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        .table-glass th, .table-glass td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: middle;
        }
        .table-glass th:last-child, .table-glass td:last-child { border-right: none; }
        
        .table-glass thead th {
            background: rgba(0, 0, 0, 0.4); 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        
        /* BARIS TABEL: Transparan Gelap (60% Opacity) */
        .table-glass tbody tr {
            background: rgba(0, 0, 0, 0.6); /* Lebih gelap agar tidak silau */
            transition: all 0.2s ease-in-out;
        }
        
        /* HOVER: Lebih gelap lagi saat disorot */
        .table-glass tbody tr:hover {
            background: rgba(0, 0, 0, 0.8); 
        }
        
        .input-group-text { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: white; border-left: none; cursor: pointer; }
        .pagination .page-link { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.1); color: white; }
        .pagination .page-item.active .page-link { background: #0ea5e9; border-color: #0ea5e9; }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="text-center mb-4">
            <i class="fas fa-wifi fa-3x mb-2" style="color: var(--accent-color);"></i>
            <h4>Sekretariat DPRD<br>Kota Surabaya</h4>
        </div>