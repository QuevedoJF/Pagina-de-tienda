<?php
require_once 'vendor/autoload.php';
include 'core/auth.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';
try { $results = DB::query("SELECT * FROM cliente"); }
catch (Exception $e) { $results = []; }
?>

<div class="page-card">
    <div class="page-header">
        <h1 class="page-title"><i class="bi bi-people"></i>Clientes</h1>
        <button class="btn-add" onclick="window.open('generar_pdf_clientes.php', '_blank')">
            <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
        </button>
        <button class="btn-add" onclick="abrirModalNuevoCliente()">
            <i class="bi bi-plus-circle"></i> Nuevo
        </button>
    </div>

    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
            <i class="bi bi-people stat-icon" style="color: #28a745;"></i>
            <div class="stat-info">
                <span class="stat-value" id="totalClientes">-</span>
                <span class="stat-label">Clientes Registrados</span>
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>RFC</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaClientes">
            <?php foreach ($results as $r): ?>
            <tr id="cliente-<?php echo $r['id']; ?>">
                <td style="color: #888; font-weight: 500;">#<?php echo $r['id']; ?></td>
                <td style="font-weight: 500;"><?php echo htmlspecialchars($r['nombre']); ?></td>
                <td style="color: #666;"><?php echo htmlspecialchars($r['apellido_paterno']); ?></td>
                <td style="color: #666;"><?php echo htmlspecialchars($r['apellido_materno']); ?></td>
                <td><span style="font-family: monospace; background: #f5f5f5; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem;"><?php echo htmlspecialchars($r['rfc']); ?></span></td>
                <td>
                    <button class="btn-action btn-view" onclick="verCliente(<?php echo $r['id']; ?>)"><i class="bi bi-eye"></i></button>
                    <button class="btn-action btn-edit" onclick="editarCliente(<?php echo $r['id']; ?>)"><i class="bi bi-pencil"></i></button>
                    <button class="btn-action btn-delete" onclick="borrarCliente(<?php echo $r['id']; ?>)"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalNuevoCliente" class="form-modal">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3 id="tituloModalCliente">Nuevo Cliente</h3>
            <button class="modal-close" onclick="cerrarModalCliente()"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="formCliente">
            <input type="hidden" name="id" id="clienteId">
            <div class="form-group">
                <label>Nombre(s)</label>
                <input type="text" name="nombre" id="nombreCliente" required>
            </div>
            <div class="form-group">
                <label>Apellido Paterno</label>
                <input type="text" name="ap_paterno" id="apPaternoCliente" required>
            </div>
            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="ap_materno" id="apMaternoCliente">
            </div>
            <div class="form-group">
                <label>RFC</label>
                <input type="text" name="rfc" id="rfcCliente" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-submit" onclick="guardarCliente()">Guardar</button>
                <button type="button" class="btn-cancel" onclick="cerrarModalCliente()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalVerCliente" class="form-modal">
    <div class="modal-content-custom" style="max-width: 400px;">
        <div class="modal-header-custom">
            <h3>Detalle del Cliente</h3>
            <button class="modal-close" onclick="cerrarModalVerCliente()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div style="text-align: center; padding: 20px 0;">
            <i class="bi bi-person-circle" style="font-size: 4rem; color: #ccc;"></i>
        </div>
        <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
            <label style="font-size: 0.7rem; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">Nombre Completo</label>
            <p id="verNombreCompleto" style="font-weight: 500; margin: 4px 0 0 0;"></p>
        </div>
        <div style="background: #f9f9f9; padding: 16px; border-radius: 8px;">
            <label style="font-size: 0.7rem; text-transform: uppercase; color: #888; letter-spacing: 0.5px;">RFC</label>
            <p id="verRfcCliente" style="font-family: monospace; text-align: center; margin: 4px 0 0 0;"></p>
        </div>
        <div class="form-actions" style="justify-content: center;">
            <button class="btn-cancel" onclick="cerrarModalVerCliente()">Cerrar</button>
        </div>
    </div>
</div>

<script>
function abrirModalNuevoCliente() {
    document.getElementById('formCliente').reset();
    document.getElementById('clienteId').value = '';
    document.getElementById('tituloModalCliente').textContent = 'Nuevo Cliente';
    document.getElementById('modalNuevoCliente').classList.add('active');
}

function cerrarModalCliente() {
    document.getElementById('modalNuevoCliente').classList.remove('active');
}

function cerrarModalVerCliente() {
    document.getElementById('modalVerCliente').classList.remove('active');
}

function guardarCliente() {
    var datos = $('#formCliente').serialize();
    $.ajax({
        url: 'guardar_cliente.php',
        type: 'POST',
        data: datos,
        success: function() {
            cerrarModalCliente();
            llamarClientes();
        },
        error: function(xhr) { alert('Error: ' + xhr.responseText); }
    });
}

function editarCliente(id) {
    $.ajax({
        url: 'obtener_cliente.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            document.getElementById('clienteId').value = data.id;
            document.getElementById('nombreCliente').value = data.nombre;
            document.getElementById('apPaternoCliente').value = data.apellido_paterno;
            document.getElementById('apMaternoCliente').value = data.apellido_materno || '';
            document.getElementById('rfcCliente').value = data.rfc;
            document.getElementById('tituloModalCliente').textContent = 'Editar Cliente';
            document.getElementById('modalNuevoCliente').classList.add('active');
        },
        error: function() { alert('Error al obtener datos'); }
    });
}

function verCliente(id) {
    $.ajax({
        url: 'obtener_cliente.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            var nombreCompleto = data.nombre + ' ' + data.apellido_paterno + (data.apellido_materno ? ' ' + data.apellido_materno : '');
            document.getElementById('verNombreCompleto').textContent = nombreCompleto;
            document.getElementById('verRfcCliente').textContent = data.rfc;
            document.getElementById('modalVerCliente').classList.add('active');
        },
        error: function() { alert('Error al obtener detalles'); }
    });
}

function borrarCliente(id) {
    if (confirm('¿Eliminar este cliente?')) {
        $.ajax({
            url: 'borrar_cliente.php',
            type: 'POST',
            data: { id: id },
            success: function() {
                document.getElementById('cliente-' + id).remove();
            },
            error: function(xhr) { alert('Error: ' + xhr.responseText); }
        });
    }
}

$.get('api_clientes_stats.php', function(data) {
    if (data && data.total !== undefined) {
        document.getElementById('totalClientes').textContent = data.total;
    }
}, 'json');
</script>