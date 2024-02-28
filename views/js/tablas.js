$(document).ready(function () {
    // Agrega un evento de clic a las filas de la tabla
    $('table.tabla-programas tbody tr').on('click', function () {
        // Obtiene el valor del atributo data-id de la fila clicada
        var idPrograma = $(this).data('id');
        // Puedes realizar acciones basadas en el ID del programa
        
        console.log('ID del Programa:', idPrograma);
        // Aquí puedes redirigir, realizar una solicitud AJAX, etc.
        $.ajax({
            type: 'POST',
            url: '/reportphp/controllers/actualizacion.php', // Archivo PHP para manejar el clic
            data: { idPrograma: idPrograma },
            success: function(response) {
                // Puedes mostrar una respuesta o realizar otras acciones si es necesario
                console.log(response);
                window.location.href = '/reportphp/index.php';
            },
            error: function(error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});