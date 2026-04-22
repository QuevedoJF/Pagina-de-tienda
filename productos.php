<?php
require_once 'vendor/autoload.php';
include 'core/auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

$results = [];
$error_msg = "";

try {
    $query = DB::query("SELECT * FROM producto");
    if (is_array($query)) {
        $results = $query;
    }
} catch (Exception $e) {
    $error_msg = "Error de BD: " . $e->getMessage();
}
?>

<div class="page-card">
    <div class="page-header">
        <h1 class="page-title"><i class="bi bi-cart3"></i>Productos</h1>
        <button class="btn-add" onclick="abrirModalNuevoProducto()">
            <i class="bi bi-plus-circle"></i> Nuevo
        </button>
    </div>

    <?php if($error_msg != ""): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th style="text-align: center;">Cantidad</th>
                <th style="text-align: right;">Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaProductos">
            <?php if (count($results) > 0): ?>
                <?php foreach ($results as $p):
                    $idProd = isset($p['id_producto']) ? $p['id_producto'] : (isset($p['id']) ? $p['id'] : 0);
                ?>
                <tr id="producto-<?php echo $idProd; ?>">
                    <td style="color: #888; font-weight: 500;">#<?php echo $idProd; ?></td>
                    <td style="font-weight: 500;"><?php echo htmlspecialchars($p['nombre']); ?></td>
                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($p['categoria']); ?></span></td>
                    <td style="text-align: center;">
                        <span style="font-weight: 600;"><?php echo $p['cantidad']; ?></span>
                        <span style="color: #aaa; font-size: 0.8rem;">uds</span>
                    </td>
                    <td style="text-align: right; color: #66bb6a; font-weight: 600;">$<?php echo number_format($p['precio'], 2); ?></td>
                    <td>
                        <button class="btn-action btn-view" onclick="verProducto(<?php echo $idProd; ?>)"><i class="bi bi-eye"></i></button>
                        <button class="btn-action btn-edit" onclick="editarProducto(<?php echo $idProd; ?>)"><i class="bi bi-pencil"></i></button>
                        <button class="btn-action btn-delete" onclick="borrarProducto(<?php echo $idProd; ?>)"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="empty-state">No hay productos registrados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="chart-section">
        <div class="chart-container">
            <div class="chart-header">
                <i class="bi bi-pie-chart-fill"></i>
                <h4 class="chart-title">Productos por Categoría</h4>
            </div>
            <canvas id="chartProductos"></canvas>
        </div>
    </div>
</div>

<div id="modalNuevoProducto" class="form-modal">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3 id="tituloModalProducto">Nuevo Producto</h3>
            <button class="modal-close" onclick="cerrarModalProducto()"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="formProducto">
            <input type="hidden" name="id" id="productoId">
            <div class="form-group">
                <label>Nombre del Producto</label>
                <input type="text" name="nombre" id="nombreProducto" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <input type="text" name="categoria" id="categoriaProducto" required>
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <input type="number" name="cantidad" id="cantidadProducto" required min="0">
            </div>
            <div class="form-group">
                <label>Precio</label>
                <input type="number" name="precio" id="precioProducto" required min="0" step="0.01">
            </div>
            <div class="form-actions">
                <button type="button" class="btn-submit" onclick="guardarProducto()">Guardar</button>
                <button type="button" class="btn-cancel" onclick="cerrarModalProducto()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalVerProducto" class="form-modal">
    <div class="modal-content-custom" style="max-width: 400px;">
        <div class="modal-header-custom">
            <h3>Detalle del Producto</h3>
            <button class="modal-close" onclick="cerrarModalVerProducto()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; padding: 20px 0;">
            <i class="bi bi-box-seam" style="font-size: 4rem; color: #ccc;"></i>
        </div>
        <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin-bottom: 12px;">
            <label style="font-size: 0.7rem; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">Nombre</label>
            <p id="verNombreProducto" style="font-weight: 500; margin: 4px 0 0 0;"></p>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="background: #f9f9f9; padding: 12px; border-radius: 8px;">
                <label style="font-size: 0.7rem; text-transform: uppercase; color: #888;">Categoría</label>
                <p id="verCategoriaProducto" style="margin: 4px 0 0 0; font-weight: 500;"></p>
            </div>
            <div style="background: #f9f9f9; padding: 12px; border-radius: 8px;">
                <label style="font-size: 0.7rem; text-transform: uppercase; color: #888;">Cantidad</label>
                <p id="verCantidadProducto" style="margin: 4px 0 0 0; font-weight: 500;"></p>
            </div>
        </div>
        <div style="background: #e8f5e9; padding: 16px; border-radius: 8px; text-align: center; margin-top: 12px;">
            <label style="font-size: 0.7rem; text-transform: uppercase; color: #66bb6a;">Precio</label>
            <p id="verPrecioProducto" style="font-size: 1.5rem; font-weight: 600; color: #66bb6a; margin: 4px 0 0 0;"></p>
        </div>
        <div class="form-actions" style="justify-content: center; margin-top: 20px;">
            <button class="btn-cancel" onclick="cerrarModalVerProducto()">Cerrar</button>
        </div>
    </div>
</div>

<script>
function abrirModalNuevoProducto() {
    document.getElementById('formProducto').reset();
    document.getElementById('productoId').value = '';
    document.getElementById('tituloModalProducto').textContent = 'Nuevo Producto';
    document.getElementById('modalNuevoProducto').classList.add('active');
}

function cerrarModalProducto() {
    document.getElementById('modalNuevoProducto').classList.remove('active');
}

function cerrarModalVerProducto() {
    document.getElementById('modalVerProducto').classList.remove('active');
}

function guardarProducto() {
    var datos = $('#formProducto').serialize();
    $.ajax({
        url: 'guardar_producto.php',
        type: 'POST',
        data: datos,
        success: function() {
            cerrarModalProducto();
            llamarProducto();
        },
        error: function(xhr) { alert('Error: ' + xhr.responseText); }
    });
}

function editarProducto(id) {
    $.ajax({
        url: 'obtener_producto.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            var idProd = data.id_producto || data.id;
            document.getElementById('productoId').value = idProd;
            document.getElementById('nombreProducto').value = data.nombre;
            document.getElementById('categoriaProducto').value = data.categoria;
            document.getElementById('cantidadProducto').value = data.cantidad;
            document.getElementById('precioProducto').value = data.precio;
            document.getElementById('tituloModalProducto').textContent = 'Editar Producto';
            document.getElementById('modalNuevoProducto').classList.add('active');
        },
        error: function() { alert('Error al obtener datos'); }
    });
}

function verProducto(id) {
    $.ajax({
        url: 'obtener_producto.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            document.getElementById('verNombreProducto').textContent = data.nombre;
            document.getElementById('verCategoriaProducto').textContent = data.categoria;
            document.getElementById('verCantidadProducto').textContent = data.cantidad + ' uds';
            var precio = parseFloat(data.precio).toFixed(2);
            document.getElementById('verPrecioProducto').textContent = '$' + precio;
            document.getElementById('modalVerProducto').classList.add('active');
        },
        error: function() { alert('Error al obtener detalles'); }
    });
}

function borrarProducto(id) {
    if (confirm('¿Eliminar este producto?')) {
        $.ajax({
            url: 'borrar_producto.php',
            type: 'POST',
            data: { id: id },
            success: function() {
                document.getElementById('producto-' + id).remove();
            },
            error: function(xhr) { alert('Error: ' + xhr.responseText); }
        });
    }
}

$.get('api_productos_chart.php', function(data) {
    if (data && data.length > 0) {
        new Chart(document.getElementById('chartProductos'), {
            type: 'doughnut',
            data: {
                labels: data.map(c => c.categoria),
                datasets: [{
                    data: data.map(c => c.cantidad),
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
}, 'json');
</script>