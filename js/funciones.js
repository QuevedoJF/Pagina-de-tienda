function llamarDocumentos() {
  $.ajax({
    url: "documentos.php",
    type: "GET",
    success: function (data) {
      $("#cuerpo").html(data);
    },
    error: function () {
      $("#cuerpo").html(
        "<div class='alert alert-danger'>Error al conectar con documentos.php</div>",
      );
    },
  });
}

function llamarClientes() {
  $.ajax({
    url: "clientes.php",
    type: "GET",
    success: function (data) {
      $("#cuerpo").html(data);
      actualizarNavActivo('clientes');
    },
    error: function () {
      $("#cuerpo").html(
        "<div class='alert alert-danger'>Error al conectar con clientes.php</div>",
      );
    },
  });
}

function llamarProducto() {
  $.ajax({
    url: "productos.php",
    type: "GET",
    success: function (data) {
      $("#cuerpo").html(data);
      actualizarNavActivo('productos');
    },
    error: function () {
      $("#cuerpo").html(
        "<div class='alert alert-danger'>Error al conectar con productos.php</div>",
      );
    },
  });
}

function llamarDocumentos() {
  $.ajax({
    url: "documentos.php",
    type: "GET",
    success: function (data) {
      $("#cuerpo").html(data);
    },
    error: function () {
      $("#cuerpo").html(
        "<div class='alert alert-danger'>Error al conectar con documentos.php</div>",
      );
    },
  });
}

function llamarClientes() {
  $.ajax({
    url: "clientes.php",
    type: "GET",
    success: function (data) {
      $("#cuerpo").html(data);
    },
    error: function () {
      $("#cuerpo").html(
        "<div class='alert alert-danger'>Error al conectar con clientes.php</div>",
      );
    },
  });
}

function llamarProducto() {
  $.ajax({
    url: "productos.php",
    type: "GET",
    success: function (data) {
      $("#cuerpo").html(data);
    },
    error: function () {
      $("#cuerpo").html(
        "<div class='alert alert-danger'>Error al conectar con productos.php</div>",
      );
    },
  });
}

function actualizarNavActivo(modulo) {
  document.querySelectorAll('.nav-item').forEach(function(el) {
    el.classList.remove('active');
  });
  var selector = '';
  if (modulo === 'clientes') selector = 'onclick="llamarClientes()"';
  if (modulo === 'productos') selector = 'onclick="llamarProducto()"';
  if (modulo === 'documentos') selector = 'onclick="llamarDocumentos()"';
  var navItem = document.querySelector('.nav-item[' + selector + ']');
  if (navItem) navItem.classList.add('active');
}

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
  var datos = $("#formCliente").serialize();
  $.ajax({
    url: "guardar_cliente.php",
    type: "POST",
    data: datos,
    success: function () {
      cerrarModalCliente();
      llamarClientes();
    },
    error: function (xhr) {
      alert("Error al guardar: " + xhr.responseText);
    },
  });
}

function editarCliente(id) {
  $.ajax({
    url: "obtener_cliente.php",
    type: "GET",
    data: { id: id },
    dataType: "json",
    success: function (data) {
      $("#clienteId").val(data.id);
      $("#nombreCliente").val(data.nombre);
      $("#apPaternoCliente").val(data.apellido_paterno);
      $("#apMaternoCliente").val(data.apellido_materno);
      $("#rfcCliente").val(data.rfc);
      $("#tituloModalCliente").text("Editar Cliente");
      $("#modalNuevoCliente").classList.add('active');
    },
    error: function () {
      alert("Error al obtener los datos del cliente.");
    },
  });
}

function verCliente(id) {
  $.ajax({
    url: "obtener_cliente.php",
    type: "GET",
    data: { id: id },
    dataType: "json",
    success: function (data) {
      var nombreCompleto =
        data.nombre + " " + data.apellido_paterno +
        (data.apellido_materno ? " " + data.apellido_materno : "");
      $("#verNombreCompleto").text(nombreCompleto);
      $("#verRfcCliente").text(data.rfc);
      $("#modalVerCliente").classList.add('active');
    },
    error: function () {
      alert("Error al obtener los detalles del cliente.");
    },
  });
}

function borrarCliente(id) {
  if (confirm("¿Eliminar este cliente?")) {
    $.ajax({
      url: "borrar_cliente.php",
      type: "POST",
      data: { id: id },
      success: function () {
        llamarClientes();
      },
      error: function (xhr) {
        alert("Error al eliminar: " + xhr.responseText);
      },
    });
  }
}

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
  var datos = $("#formProducto").serialize();
  $.ajax({
    url: "guardar_producto.php",
    type: "POST",
    data: datos,
    success: function () {
      cerrarModalProducto();
      llamarProducto();
    },
    error: function (xhr) {
      alert("Error al guardar: " + xhr.responseText);
    },
  });
}

function editarProducto(id) {
  $.ajax({
    url: "obtener_producto.php",
    type: "GET",
    data: { id: id },
    dataType: "json",
    success: function (data) {
      var idProd = data.id_producto || data.id;
      $("#productoId").val(idProd);
      $("#nombreProducto").val(data.nombre);
      $("#categoriaProducto").val(data.categoria);
      $("#cantidadProducto").val(data.cantidad);
      $("#precioProducto").val(data.precio);
      $("#tituloModalProducto").text("Editar Producto");
      $("#modalNuevoProducto").classList.add('active');
    },
    error: function () {
      alert("Error al obtener los datos del producto.");
    },
  });
}

function verProducto(id) {
  $.ajax({
    url: "obtener_producto.php",
    type: "GET",
    data: { id: id },
    dataType: "json",
    success: function (data) {
      $("#verNombreProducto").text(data.nombre);
      $("#verCategoriaProducto").text(data.categoria);
      $("#verCantidadProducto").text(data.cantidad + " uds");
      var precio = parseFloat(data.precio).toFixed(2);
      $("#verPrecioProducto").text("$" + precio);
      $("#modalVerProducto").classList.add('active');
    },
    error: function () {
      alert("Error al obtener los detalles del producto.");
    },
  });
}

function borrarProducto(id) {
  if (confirm("¿Eliminar este producto?")) {
    $.ajax({
      url: "borrar_producto.php",
      type: "POST",
      data: { id: id },
      success: function () {
        llamarProducto();
      },
      error: function (xhr) {
        alert("Error al eliminar: " + xhr.responseText);
      },
    });
  }
}