<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Wifi Sekretariat DPRD Surabaya</title>
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
        .table-glass { color: white; vertical-align: middle; margin-top: 20px; }
        .table-glass td, .table-glass th { background: rgba(255,255,255,0.05); border-color: white; }
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