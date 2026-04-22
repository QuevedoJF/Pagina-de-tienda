<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: components/login.html");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <a href="#" class="header-brand">
            <i class="bi bi-building"></i>
            Mi Empresa
        </a>
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="font-size: 0.85rem; color: #888;"><?php echo $_SESSION['usuario']; ?></span>
            <a href="vendor/logout.php" style="color: #fff; text-decoration: none; font-size: 0.85rem;">
                <i class="bi bi-box-arrow-right"></i> Cerrar
            </a>
        </div>
    </header>

    <aside class="sidebar">
        <nav class="nav-section">
            <div class="nav-section-title">Menu</div>
            <a class="nav-item active" href="#" data-page="dashboard" onclick="showPage('dashboard'); return false;">
                <i class="bi bi-house"></i>
                <span>Dashboard</span>
            </a>
            <a class="nav-item" href="#" data-page="documentos" onclick="showPage('documentos'); return false;">
                <i class="bi bi-file-earmark-text"></i>
                <span>Documentos</span>
            </a>
            <a class="nav-item" href="#" data-page="clientes" onclick="showPage('clientes'); return false;">
                <i class="bi bi-people"></i>
                <span>Clientes</span>
            </a>
            <a class="nav-item" href="#" data-page="productos" onclick="showPage('productos'); return false;">
                <i class="bi bi-cart3"></i>
                <span>Productos</span>
            </a>
        </nav>
        <div class="sidebar-divider"></div>
        <nav class="nav-section">
            <a class="nav-item" href="vendor/logout.php">
                <i class="bi bi-door-closed"></i>
                <span>Cerrar Sesión</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div id="cuerpo"></div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/funciones.js"></script>
    <script>
        $(document).ready(function() {
            showPage('dashboard');
        });

        function showPage(page) {
            $('.nav-item').removeClass('active');
            $('.nav-item[data-page="' + page + '"]').addClass('active');

            $('#cuerpo').fadeOut(0.15, function() {
                if (page === 'dashboard') {
                    cargarDashboard();
                } else if (page === 'documentos') {
                    llamarDocumentos();
                } else if (page === 'clientes') {
                    llamarClientes();
                } else if (page === 'productos') {
                    llamarProducto();
                }
                $('#cuerpo').fadeIn(0.2);
            });
        }

        function cargarDashboard() {
            $.get('api_stats.php', function(data) {
                if (data.error) {
                    $('#cuerpo').html('<div class="page-card"><p>Error cargando datos</p></div>');
                    return;
                }
                $('#cuerpo').html(`
                    <div class="page-card">
                        <div class="page-header">
                            <h1 class="page-title"><i class="bi bi-house"></i>Dashboard</h1>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <i class="bi bi-cart3 stat-icon" style="color: #007bff;"></i>
                                <div class="stat-info">
                                    <span class="stat-value">${data.total_productos}</span>
                                    <span class="stat-label">Productos</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <i class="bi bi-people stat-icon" style="color: #28a745;"></i>
                                <div class="stat-info">
                                    <span class="stat-value">${data.total_clientes}</span>
                                    <span class="stat-label">Clientes</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <i class="bi bi-box-seam stat-icon" style="color: #ffc107;"></i>
                                <div class="stat-info">
                                    <span class="stat-value">${data.total_stock}</span>
                                    <span class="stat-label">Stock Total</span>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 20px;">
                            <div style="max-width: 500px; margin: 0 auto;">
                                <div class="chart-container" style="max-width: 100%; padding: 24px;">
                                    <div class="chart-header">
                                        <i class="bi bi-bar-chart-fill"></i>
                                        <h4 class="chart-title">Stock por Categoría</h4>
                                    </div>
                                    <canvas id="dashboardChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                if (data.categorias && data.categorias.length > 0) {
                    new Chart(document.getElementById('dashboardChart'), {
                        type: 'bar',
                        data: {
                            labels: data.categorias.map(c => c.categoria),
                            datasets: [{
                                label: 'Cantidad',
                                data: data.categorias.map(c => c.cantidad),
                                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2'],
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            }, 'json');
        }
    </script>
</body>
</html>