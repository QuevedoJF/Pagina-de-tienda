<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: components/login.html");
    exit();
}

require_once 'vendor/autoload.php';

DB::$host = 'localhost';
DB::$user = 'root';
DB::$password = '1234';
DB::$dbName = 'tienda';
DB::$encoding = 'utf8';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'msg' => 'Error al subir archivo']);
        exit();
    }

    $nombreOriginal = basename($archivo['name']);
    $nombreTemporal = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", $nombreOriginal);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    if (!is_dir('documentos')) {
        mkdir('documentos', 0755, true);
    }

    $rutaDestino = 'documentos/' . $nombreTemporal;

    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        DB::insert('documento', [
            'nombre' => trim($_POST['nombre']) ?: $nombreOriginal,
            'nombre_temporal' => $nombreTemporal,
            'ruta' => $rutaDestino,
            'tipo' => $extension,
            'tamano' => $archivo['size'],
            'descripcion_corta' => trim($_POST['descripcion_corta']),
            'descripcion_larga' => trim($_POST['descripcion_larga']),
            'descargas' => 0,
            'fk_user_id' => $_SESSION['usuario_id']
        ]);
        echo json_encode(['success' => true, 'msg' => 'Documento subido']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Error al guardar']);
    }
    exit();
}

$results = [];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if ($search) {
        $query = DB::query("SELECT d.*, u.nombre as usuario_nombre FROM documento d LEFT JOIN usuario u ON d.fk_user_id = u.id WHERE d.nombre LIKE %ss ORDER BY d.created DESC", $search);
    } else {
        $query = DB::query("SELECT d.*, u.nombre as usuario_nombre FROM documento d LEFT JOIN usuario u ON d.fk_user_id = u.id ORDER BY d.created DESC");
    }
    $results = is_array($query) ? $query : [];
} catch (Exception $e) {
    $results = [];
}
error_log("Documentos encontrados: " . count($results));

function getIconClass($ext) {
    $iconos = [
        'pdf' => 'bi bi-file-earmark-pdf-fill',
        'doc' => 'bi bi-file-earmark-word-fill',
        'docx' => 'bi bi-file-earmark-word-fill',
        'xls' => 'bi bi-file-earmark-excel-fill',
        'xlsx' => 'bi bi-file-earmark-excel-fill',
        'ppt' => 'bi bi-file-earmark-ppt-fill',
        'pptx' => 'bi bi-file-earmark-ppt-fill',
        'jpg' => 'bi bi-file-earmark-image-fill',
        'jpeg' => 'bi bi-file-earmark-image-fill',
        'png' => 'bi bi-file-earmark-image-fill',
        'gif' => 'bi bi-file-earmark-image-fill',
        'zip' => 'bi bi-file-earmark-zip-fill',
        'rar' => 'bi bi-file-earmark-zip-fill',
        'txt' => 'bi bi-file-earmark-text-fill'
    ];
    return $iconos[$ext] ?? 'bi bi-file-earmark-fill';
}

function formatSize($bytes) {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1024 * 1024) return number_format($bytes / 1024, 1) . ' KB';
    return number_format($bytes / (1024 * 1024), 1) . ' MB';
}
?>

<div class="page-card">
    <div class="page-header">
        <h1 class="page-title"><i class="bi bi-folder-fill"></i>Documentos</h1>
        <button class="btn-add" id="btnToggleForm" onclick="toggleFormulario()">
            <i class="bi bi-plus-circle"></i> Subir documento
        </button>
    </div>

    <div id="formContainer" style="display: none; margin-bottom: 24px;">
        <div class="page-card" style="background: #f9f9f9; border: 1px solid #e0e0e0;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-cloud-upload" style="color: #4a90d9;"></i> Subir nuevo documento
            </h3>
            <form id="formDocumento">
                <div class="upload-zone" id="dropZone" onclick="document.getElementById('archivo').click()">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p style="font-weight: 500;">Arrastra cualquier archivo aqui</p>
                    <p style="font-size: 0.85rem;">o haz clic para seleccionar</p>
                    <input type="file" name="archivo" id="archivo" class="d-none" required onchange="mostrarNombre(this)">
                </div>

                <div id="infoArchivo" class="file-info d-none">
                    <i class="bi bi-file-earmark-fill" id="iconoArchivo"></i>
                    <span id="nombreArchivo" class="file-info-name"></span>
                    <span id="tamanoArchivo" class="file-info-size"></span>
                    <button type="button" onclick="limpiarArchivo()"><i class="bi bi-x-lg"></i></button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label>Nombre del documento</label>
                        <input type="text" name="nombre" id="inputNombre" required placeholder="Nombre del archivo">
                    </div>
                    <div class="form-group">
                        <label>Descripción corta</label>
                        <input type="text" name="descripcion_corta" maxlength="150" placeholder="Breve descripción">
                    </div>
                    <div class="form-group">
                        <label>Descripción larga</label>
                        <input type="text" name="descripcion_larga" placeholder="Detalles adicionales...">
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-cloud-upload"></i> Subir Documento
                    </button>
                    <button type="button" class="btn-cancel" onclick="toggleFormulario()">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="search-box">
        <form id="formBusqueda" style="display: flex; gap: 8px; width: 100%;">
            <input type="text" name="search" id="inputSearch" placeholder="Buscar documento..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="bi bi-search"></i></button>
            <?php if($search): ?>
            <a href="#" onclick="limpiarBusqueda(); return false;" style="padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; display: inline-block;"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <div id="listaDocumentos">
        <?php if(count($results) > 0): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            <?php foreach($results as $doc): ?>
            <?php $ext = isset($doc['tipo']) ? strtolower($doc['tipo']) : ''; ?>
            <div class="doc-card" id="doc-<?php echo $doc['id']; ?>">
                <div class="doc-card-header">
                    <div class="doc-icon">
                        <i class="<?php echo getIconClass($ext); ?>"></i>
                    </div>
                    <div class="doc-info">
                        <h4 class="text-truncate"><?php echo htmlspecialchars($doc['nombre']); ?></h4>
                        <?php if($doc['descripcion_corta']): ?>
                        <p class="text-truncate"><?php echo htmlspecialchars($doc['descripcion_corta']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="doc-meta">
                    <span><i class="bi bi-person"></i><?php echo htmlspecialchars($doc['usuario_nombre'] ?? 'Desconocido'); ?></span>
                    <span><i class="bi bi-calendar3"></i><?php echo date('d M Y', strtotime($doc['created'])); ?></span>
                    <span><i class="bi bi-download"></i><?php echo $doc['descargas']; ?></span>
                    <span><i class="bi bi-hdd"></i><?php echo formatSize($doc['tamano']); ?></span>
                </div>
                <div class="doc-actions">
                    <button class="btn-action btn-view" onclick="verDocumento(<?php echo $doc['id']; ?>)">
                        <i class="bi bi-eye"></i> Ver
                    </button>
                    <a href="descargar_documento.php?id=<?php echo $doc['id']; ?>" class="btn-action btn-edit" style="text-decoration: none;">
                        <i class="bi bi-download"></i>
                    </a>
                    <button class="btn-action btn-delete" onclick="borrarDocumento(<?php echo $doc['id']; ?>)">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-folder2-open"></i>
            <h3>No hay documentos</h3>
            <p>Sube tu primer documento para comenzar</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalVerDocumento" class="form-modal">
    <div class="modal-content-custom" style="max-width: 800px;">
        <div class="modal-header-custom">
            <h3 id="tituloDocumento"></h3>
            <button class="modal-close" onclick="cerrarModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="visorContainer" style="height: 70vh; background: #f5f5f5; border-radius: 8px; overflow: auto; display: flex; align-items: center; justify-content: center;">
            <iframe id="visorDocumento" src="" style="border: none; display: none; width: 100%; height: 100%;"></iframe>
            <img id="visorImagen" src="" style="max-width: 100%; max-height: 100%; display: none; object-fit: contain;">
        </div>
        <div class="form-actions">
            <a id="btnDescargarModal" href="#" class="btn-submit" target="_blank" style="text-decoration: none;">
                <i class="bi bi-download"></i> Descargar
            </a>
            <button class="btn-cancel" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>
</div>

<script>
function toggleFormulario() {
    var formContainer = document.getElementById('formContainer');
    var btn = document.getElementById('btnToggleForm');
    
    if (formContainer.style.display === 'none') {
        formContainer.style.display = 'block';
        formContainer.style.animation = 'fadeInSlide 0.3s ease forwards';
        btn.innerHTML = '<i class="bi bi-dash-circle"></i> Ocultar';
    } else {
        formContainer.style.display = 'none';
        btn.innerHTML = '<i class="bi bi-plus-circle"></i> Subir documento';
    }
}

$('#formDocumento').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'documentos.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#formDocumento')[0].reset();
                    $('#infoArchivo').addClass('d-none');
                    $('#inputNombre').val('');
                    llamarDocumentos();
                } else {
                    alert(data.msg);
                }
            } catch(e) {
                alert('Error al procesar respuesta');
            }
        },
        error: function(xhr) {
            alert('Error al subir: ' + xhr.responseText);
        }
    });
});

$('#formBusqueda').submit(function(e) {
    e.preventDefault();
    llamarDocumentos();
});

function llamarDocumentos() {
    $.ajax({
        url: 'documentos.php',
        type: 'GET',
        success: function(data) {
            $('#cuerpo').html(data);
        }
    });
}

function limpiarBusqueda() {
    $('#inputSearch').val('');
    llamarDocumentos();
}

function llamarDocumentos() {
    var search = $('#inputSearch').val();
    $.ajax({
        url: 'documentos.php',
        type: 'GET',
        data: { search: search },
        success: function(data) {
            $('#cuerpo').html(data);
        }
    });
}

function mostrarNombre(input) {
    if (input.files.length > 0) {
        var file = input.files[0];
        var extension = file.name.split('.').pop().toLowerCase();
        var icono = getIconoPorExtension(extension);
        $('#iconoArchivo').attr('class', icono);
        $('#nombreArchivo').text(file.name);
        $('#tamanoArchivo').text(formatTamano(file.size));
        $('#infoArchivo').removeClass('d-none');
        $('#inputNombre').val(file.name.replace('.' + extension, ''));
    }
}

function getIconoPorExtension(ext) {
    var iconos = {
        'pdf': 'bi bi-file-earmark-pdf-fill',
        'doc': 'bi bi-file-earmark-word-fill',
        'docx': 'bi bi-file-earmark-word-fill',
        'xls': 'bi bi-file-earmark-excel-fill',
        'xlsx': 'bi bi-file-earmark-excel-fill',
        'ppt': 'bi bi-file-earmark-ppt-fill',
        'pptx': 'bi bi-file-earmark-ppt-fill',
        'jpg': 'bi bi-file-earmark-image-fill',
        'jpeg': 'bi bi-file-earmark-image-fill',
        'png': 'bi bi-file-earmark-image-fill',
        'gif': 'bi bi-file-earmark-image-fill',
        'zip': 'bi bi-file-earmark-zip-fill',
        'rar': 'bi bi-file-earmark-zip-fill',
        'txt': 'bi bi-file-earmark-text-fill'
    };
    return iconos[ext] || 'bi bi-file-earmark-fill';
}

function formatTamano(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

$('#dropZone').on('dragover', function(e) {
    e.preventDefault();
    $(this).css('border-color', '#4a90d9');
});

$('#dropZone').on('dragleave', function() {
    $(this).css('border-color', '#ddd');
});

$('#dropZone').on('drop', function(e) {
    e.preventDefault();
    $(this).css('border-color', '#ddd');
    if (e.originalEvent.dataTransfer.files.length) {
        $('#archivo')[0].files = e.originalEvent.dataTransfer.files;
        mostrarNombre($('#archivo')[0]);
    }
});

function verDocumento(id) {
    $.ajax({
        url: 'obtener_documento.php?id=' + id,
        type: 'GET',
        success: function(data) {
            var doc = JSON.parse(data);
            $('#tituloDocumento').text(doc.nombre);
            $('#btnDescargarModal').attr('href', 'descargar_documento.php?id=' + id);
            
            var ext = doc.tipo ? doc.tipo.toLowerCase() : '';
            var esImagen = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
            
            if (esImagen) {
                $('#visorDocumento').hide();
                $('#visorImagen').attr('src', doc.ruta).show();
            } else {
                $('#visorImagen').hide();
                $('#visorDocumento').attr('src', doc.ruta).show();
            }
            
            $('#modalVerDocumento').addClass('active');
        },
        error: function() {
            alert('Error al cargar documento');
        }
    });
}

function cerrarModal() {
    $('#modalVerDocumento').removeClass('active');
    setTimeout(function() {
        $('#visorDocumento').attr('src', '').hide();
        $('#visorImagen').attr('src', '').hide();
    }, 300);
}

function borrarDocumento(id) {
    if (confirm('¿Eliminar este documento?')) {
        $.ajax({
            url: 'borrar_documento.php',
            type: 'POST',
            data: { id: id },
            success: function() {
                $('#doc-' + id).remove();
                verificarDocs();
            },
            error: function() {
                alert('Error al eliminar');
            }
        });
    }
}

function verificarDocs() {
    if ($('.doc-card').length === 0) {
        $('#listaDocumentos').html('<div class="empty-state"><i class="bi bi-folder2-open"></i><h3>No hay documentos</h3><p>Sube tu primer documento para comenzar</p></div>');
    }
}
</script>