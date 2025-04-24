document.addEventListener('DOMContentLoaded', function () {
    const botonGenerarUsuario = document.getElementById('boton-generar-usuario');
    const botonGenerarContrasena = document.getElementById('boton-generar-contraseña');
    const inputUsuario = document.getElementById('usuario');
    const inputContrasena = document.getElementById('contraseña');
    const inputNombre = document.getElementById('nombre');
    const inputApellidoPaterno = document.getElementById('apellido-paterno');
    const inputApellidoMaterno = document.getElementById('apellido-materno');
    const selectTipoUsuario = document.getElementById('tipo-usuario');

    // Asociar eventos a botones
    botonGenerarUsuario.addEventListener('click', generarUsuario);
    botonGenerarContrasena.addEventListener('click', generarContrasena);

    /**
     * Generar el nombre de usuario basado en los datos proporcionados.
     */
    function generarUsuario() {
        // Obtener valores de los campos
        const nombre = inputNombre.value.trim().toUpperCase();
        const apellidoPaterno = inputApellidoPaterno.value.trim().toUpperCase();
        const tipoUsuario = selectTipoUsuario.value;

        // Validar que los campos necesarios estén completos
        if (!nombre || !apellidoPaterno || !tipoUsuario) {
            alert('Por favor, complete los campos de nombre, apellido paterno y tipo de usuario antes de generar el usuario.');
            return;
        }

        // Determinar prefijo basado en el tipo de usuario
        let prefijo;
        switch (tipoUsuario) {
            case 'Administrador de planeacion': prefijo = 'AP'; break;
            case 'Administrador de finanzas': prefijo = 'AF'; break;
            case 'Coordinador': prefijo = 'CO'; break;
            default: prefijo = 'XX'; // En caso de error
        }

        // Generar el usuario único
        const primeraLetraNombre = nombre.charAt(0);
        const primeraLetraApellido = apellidoPaterno.charAt(0);
        const numeroAleatorio = Math.floor(Math.random() * 10);
        const letraAleatoria = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
        const anioActual = new Date().getFullYear().toString().slice(-2);

        const usuarioGenerado = `${prefijo}${primeraLetraNombre}${primeraLetraApellido}${numeroAleatorio}${letraAleatoria}${anioActual}`;

        // Asignar el valor generado al campo correspondiente
        inputUsuario.value = usuarioGenerado;
    }

    /**
     * Generar contraseña aleatoria segura.
     */
    function generarContrasena() {
        const longitud = 8;
        // Solo incluimos letras, números y los caracteres especiales permitidos: ".", "-" y "_"
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789._-';
        let contrasena = '';

        // Generar contraseña
        for (let i = 0; i < longitud; i++) {
            contrasena += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        }

        // Asignar la contraseña generada al campo correspondiente
        inputContrasena.value = contrasena;
    }
});