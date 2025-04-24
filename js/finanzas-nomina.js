/**
 * Script para gestionar las operaciones CRUD de nómina
 */
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const tablaEmpleados = document.getElementById('tabla-empleados');
    const tablaNominas = document.getElementById('tabla-nominas');
    const formularioNomina = document.getElementById('formulario-nomina');
    const formularioEditarNomina = document.getElementById('formulario-editar-nomina');
    const botonGuardar = document.getElementById('boton-guardar');
    const botonCancelar = document.getElementById('boton-cancelar');
    const botonGenerarReporte = document.getElementById('boton-generar-reporte');
    const filtroArea = document.getElementById('filtro-area');
    const formularioTitulo = document.getElementById('formulario-titulo');
    
    // Referencias a elementos del modal de edición
    const modalEditar = document.getElementById('modal-editar');
    const botonActualizar = document.getElementById('boton-actualizar');
    const botonCancelarEditar = document.getElementById('boton-cancelar-editar');
    
    // Referencias a elementos del modal de confirmación
    const modalConfirmacion = document.getElementById('modal-confirmacion');
    const modalConfirmar = document.getElementById('modal-confirmar');
    const modalCancelar = document.getElementById('modal-cancelar');
    
    // Referencias a los nuevos botones para la tabla de nóminas
    const botonEditarNomina = document.getElementById('boton-editar-nomina');
    const botonEliminarNomina = document.getElementById('boton-eliminar-nomina');
    
    // Variables globales
    let empleadoSeleccionado = null;
    let nominaSeleccionada = null;
    let nominaSeleccionadaRadio = null;
    
    // Inicializar eventos
    if (filtroArea) {
        filtroArea.addEventListener('change', cargarEmpleados);
    }
    
    if (formularioNomina) {
        formularioNomina.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarNomina();
        });
    }
    
    if (botonCancelar) {
        botonCancelar.addEventListener('click', resetearFormulario);
    }
    
    if (botonGenerarReporte) {
        botonGenerarReporte.addEventListener('click', generarReporte);
    }
    
    if (botonActualizar) {
        botonActualizar.addEventListener('click', actualizarNomina);
    }
    
    if (botonCancelarEditar) {
        botonCancelarEditar.addEventListener('click', cerrarModalEditar);
    }
    
    if (modalConfirmar) {
        modalConfirmar.addEventListener('click', confirmarEliminarNomina);
    }
    
    if (modalCancelar) {
        modalCancelar.addEventListener('click', cerrarModalConfirmacion);
    }
    
    // Eventos para los nuevos botones de editar y eliminar en la tabla de nóminas
    if (botonEditarNomina) {
        botonEditarNomina.addEventListener('click', function() {
            if (nominaSeleccionadaRadio) {
                editarNomina(nominaSeleccionadaRadio.nomina_id);
            }
        });
    }
    
    if (botonEliminarNomina) {
        botonEliminarNomina.addEventListener('click', function() {
            if (nominaSeleccionadaRadio) {
                eliminarNomina(nominaSeleccionadaRadio.nomina_id, nominaSeleccionadaRadio.usuario_id);
            }
        });
    }
    
    // Inicializar validaciones de campos
    inicializarValidaciones();
    
    // Cargar datos iniciales
    cargarEmpleados();
    cargarNominas();
    
    /**
     * Configura las validaciones de los campos del formulario
     */
    function inicializarValidaciones() {
        // Validación de montos (solo números positivos con 2 decimales)
        const camposMonto = ['sueldo', 'imss', 'sar', 'infonavit', 'editar_sueldo', 'editar_imss', 'editar_sar', 'editar_infonavit'];
        camposMonto.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.addEventListener('input', function() {
                    // Permitir solo números y un punto decimal
                    this.value = this.value.replace(/[^\d.]/g, '');
                    
                    // Asegurar que solo hay un punto decimal
                    const parts = this.value.split('.');
                    if (parts.length > 2) {
                        this.value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    
                    // Limitar a 2 decimales
                    if (parts.length > 1 && parts[1].length > 2) {
                        this.value = parts[0] + '.' + parts[1].substring(0, 2);
                    }
                });
            }
        });
    }
    
    /**
     * Carga los empleados en la tabla
     * Exportada al scope global para que pueda ser usada por otros scripts
     */
    window.cargarEmpleados = cargarEmpleados;
    function cargarEmpleados() {
        if (!tablaEmpleados) return;
        
        // Obtener filtro de área
        const areaSeleccionada = filtroArea ? filtroArea.value : '';
        
        // Mostrar indicador de carga
        tablaEmpleados.innerHTML = '<tr><td colspan="6">Cargando empleados...</td></tr>';
        
        // Obtener empleados del servidor
        fetch(`../php/consulta-finanzas-nomina.php?accion=listar_empleados&area=${areaSeleccionada}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Filtrar empleados sin nómina
                    filtrarEmpleadosSinNomina(data.empleados);
                } else {
                    throw new Error(data.error || 'Error al cargar empleados');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tablaEmpleados.innerHTML = `<tr><td colspan="6">Error al cargar empleados: ${error.message}</td></tr>`;
            });
    }
    
    /**
     * Filtra empleados para mostrar solo aquellos sin nómina registrada
     */
    function filtrarEmpleadosSinNomina(empleados) {
        if (empleados.length === 0) {
            tablaEmpleados.innerHTML = '<tr><td colspan="6">No hay empleados disponibles</td></tr>';
            return;
        }
        
        // Obtener todas las nóminas para identificar empleados con nómina
        fetch('../php/consulta-finanzas-nomina.php?accion=listar')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Crear un conjunto con IDs de empleados que ya tienen nómina
                    const empleadosConNomina = new Set();
                    data.nominas.forEach(nomina => {
                        empleadosConNomina.add(nomina.usuario_id);
                    });
                    
                    // Filtrar empleados que no tienen nómina
                    const empleadosFiltrados = empleados.filter(empleado => 
                        !empleadosConNomina.has(empleado.usuario_id)
                    );
                    
                    // Mostrar empleados filtrados
                    mostrarEmpleados(empleadosFiltrados);
                } else {
                    throw new Error(data.error || 'Error al cargar nóminas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarEmpleados(empleados); // En caso de error, mostrar todos
            });
    }
    
    /**
     * Carga las nóminas registradas
     */
    function cargarNominas() {
        if (!tablaNominas) return;
        
        // Mostrar indicador de carga
        tablaNominas.innerHTML = '<tr><td colspan="7">Cargando nóminas...</td></tr>';
        
        // Obtener nóminas del servidor
        fetch('../php/consulta-finanzas-nomina.php?accion=listar')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarNominas(data.nominas);
                } else {
                    throw new Error(data.error || 'Error al cargar nóminas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tablaNominas.innerHTML = `<tr><td colspan="7">Error al cargar nóminas: ${error.message}</td></tr>`;
            });
    }
    
    /**
     * Muestra los empleados en la tabla
     */
    function mostrarEmpleados(empleados) {
        if (!tablaEmpleados) return;
        
        if (empleados.length === 0) {
            tablaEmpleados.innerHTML = '<tr><td colspan="6">No hay empleados disponibles sin nómina</td></tr>';
            return;
        }
        
        let html = '';
        empleados.forEach(empleado => {
            html += `
                <tr data-id="${empleado.usuario_id}" class="fila-empleado">
                    <td class="seleccion-empleado">
                        <input type="radio" name="seleccion-empleado" id="radio-${empleado.usuario_id}" class="radio-empleado">
                    </td>
                    <td>${empleado.nombre_completo}</td>
                    <td>${empleado.correo}</td>
                    <td>${empleado.telefono}</td>
                    <td>${empleado.area}</td>
                    <td>${empleado.cargo}</td>
                </tr>
            `;
        });
        
        tablaEmpleados.innerHTML = html;
        
        // Agregar evento click a cada fila
        document.querySelectorAll('.fila-empleado').forEach(fila => {
            fila.addEventListener('click', function() {
                // Remover selección anterior
                document.querySelectorAll('.fila-empleado').forEach(f => {
                    f.classList.remove('fila-seleccionada');
                });
                
                // Quitar selección de todos los radios
                document.querySelectorAll('.radio-empleado').forEach(radio => {
                    radio.checked = false;
                });
                
                // Aplicar selección a la fila actual
                this.classList.add('fila-seleccionada');
                
                // Marcar el radio button de esta fila
                const radioButton = this.querySelector('.radio-empleado');
                if (radioButton) {
                    radioButton.checked = true;
                }
                
                // Guardar datos del empleado seleccionado
                const empleadoId = this.getAttribute('data-id');
                const nombreEmpleado = this.cells[1].textContent;
                
                empleadoSeleccionado = {
                    id: empleadoId,
                    nombre: nombreEmpleado
                };
                
                // Actualizar campo de nombre en el formulario
                document.getElementById('nombre-empleado').value = nombreEmpleado;
                document.getElementById('usuario_id').value = empleadoId;
                
                // Habilitar botón de guardar
                botonGuardar.disabled = false;
            });
        });
        
        // Agregar evento click a los radio buttons
        document.querySelectorAll('.radio-empleado').forEach(radio => {
            radio.addEventListener('click', function(e) {
                // Evitar propagación para no activar el evento de la fila
                e.stopPropagation();
                
                // Simular clic en la fila padre
                this.closest('tr').click();
            });
        });
    }
    
    /**
     * Muestra las nóminas en la tabla - FUNCIÓN MODIFICADA
     */
    function mostrarNominas(nominas) {
        if (!tablaNominas) return;
        
        if (nominas.length === 0) {
            tablaNominas.innerHTML = '<tr><td colspan="7">No hay nóminas registradas</td></tr>';
            return;
        }
        
        let html = '';
        nominas.forEach(nomina => {
            // Formato de montos con 2 decimales y separador de miles
            const sueldo = parseFloat(nomina.sueldo).toLocaleString('es-MX', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
            const imss = parseFloat(nomina.imss).toLocaleString('es-MX', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
            const sar = parseFloat(nomina.sar).toLocaleString('es-MX', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
            const infonavit = parseFloat(nomina.infonavit).toLocaleString('es-MX', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
            
            // Formato de fecha
            const fechaIngreso = new Date(nomina.fecha_ingreso).toLocaleDateString('es-MX');
            
            html += `
                <tr data-id="${nomina.nomina_id}" data-usuario="${nomina.usuario_id}" class="fila-nomina">
                    <td class="seleccion-nomina">
                        <input type="radio" name="seleccion-nomina" id="radio-nomina-${nomina.nomina_id}" class="radio-nomina">
                    </td>
                    <td>${nomina.nombre_completo}</td>
                    <td>${fechaIngreso}</td>
                    <td>$${sueldo}</td>
                    <td>$${imss}</td>
                    <td>$${sar}</td>
                    <td>$${infonavit}</td>
                </tr>
            `;
        });
        
        tablaNominas.innerHTML = html;
        
        // Resetear la nómina seleccionada
        nominaSeleccionadaRadio = null;
        if (botonEditarNomina) botonEditarNomina.disabled = true;
        if (botonEliminarNomina) botonEliminarNomina.disabled = true;
        
        // Agregar evento click a cada fila de nómina
        document.querySelectorAll('.fila-nomina').forEach(fila => {
            fila.addEventListener('click', function() {
                // Remover selección anterior
                document.querySelectorAll('.fila-nomina').forEach(f => {
                    f.classList.remove('fila-seleccionada');
                });
                
                // Quitar selección de todos los radios
                document.querySelectorAll('.radio-nomina').forEach(radio => {
                    radio.checked = false;
                });
                
                // Aplicar selección a la fila actual
                this.classList.add('fila-seleccionada');
                
                // Marcar el radio button de esta fila
                const radioButton = this.querySelector('.radio-nomina');
                if (radioButton) {
                    radioButton.checked = true;
                }
                
                // Guardar datos de la nómina seleccionada
                const nominaId = this.getAttribute('data-id');
                const usuarioId = this.getAttribute('data-usuario');
                
                nominaSeleccionadaRadio = {
                    nomina_id: nominaId,
                    usuario_id: usuarioId
                };
                
                // Habilitar botones de acción
                if (botonEditarNomina) botonEditarNomina.disabled = false;
                if (botonEliminarNomina) botonEliminarNomina.disabled = false;
            });
        });
        
        // Agregar evento click a los radio buttons
        document.querySelectorAll('.radio-nomina').forEach(radio => {
            radio.addEventListener('click', function(e) {
                // Evitar propagación para no activar el evento de la fila
                e.stopPropagation();
                
                // Simular clic en la fila padre
                this.closest('tr').click();
            });
        });
    }
    
    /**
     * Verifica si el empleado ya tiene una nómina registrada
     */
    function verificarNominaExistente(empleadoId) {
        fetch(`../php/consulta-finanzas-nomina.php?accion=listar&usuario_id=${empleadoId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.nominas && data.nominas.length > 0) {
                    // Ya tiene nómina, deshabilitar formulario y mostrar mensaje
                    formularioNomina.reset();
                    document.getElementById('nombre-empleado').value = empleadoSeleccionado.nombre;
                    botonGuardar.disabled = true;
                    mostrarMensaje('Este empleado ya tiene una nómina registrada. Utilice la opción de editar en la tabla de nóminas.', 'error');
                    
                    // Recargar empleados para filtrar
                    cargarEmpleados();
                } else {
                    // No tiene nómina, habilitar formulario
                    resetearFormulario(true);
                    document.getElementById('nombre-empleado').value = empleadoSeleccionado.nombre;
                    document.getElementById('usuario_id').value = empleadoSeleccionado.id;
                    botonGuardar.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al verificar nómina existente: ' + error.message, 'error');
            });
    }
    
    /**
     * Guarda una nueva nómina
     */
    function guardarNomina() {
        if (!validarFormulario(formularioNomina)) {
            return;
        }
        
        // Obtener datos del formulario
        const formData = new FormData(formularioNomina);
        formData.append('modo', 'registrar');
        
        // Convertir FormData a objeto para enviar como JSON
        const datos = {};
        formData.forEach((value, key) => {
            datos[key] = value;
        });
        
        // Deshabilitar botón mientras se procesa
        botonGuardar.disabled = true;
        
        fetch('../php/consulta-finanzas-nomina.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje(data.message, 'exito');
                resetearFormulario();
                cargarNominas();
                // Recargar empleados para actualizar la lista de empleados sin nómina
                cargarEmpleados();
            } else {
                throw new Error(data.error || 'Error al guardar la nómina');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al guardar la nómina: ' + error.message, 'error');
            botonGuardar.disabled = false;
        });
    }
    
    /**
     * Abre el modal de edición de nómina
     */
    window.editarNomina = function(nominaId) {
        fetch(`../php/consulta-finanzas-nomina.php?accion=obtener&id=${nominaId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Llenar el formulario de edición
                    const nomina = data.nomina;
                    document.getElementById('editar_nomina_id').value = nomina.nomina_id;
                    document.getElementById('editar_usuario_id').value = nomina.usuario_id;
                    document.getElementById('editar_nombre_empleado').value = nomina.nombre_completo;
                    document.getElementById('editar_fecha_ingreso').value = nomina.fecha_ingreso;
                    document.getElementById('editar_sueldo').value = nomina.sueldo;
                    document.getElementById('editar_imss').value = nomina.imss;
                    document.getElementById('editar_sar').value = nomina.sar;
                    document.getElementById('editar_infonavit').value = nomina.infonavit;
                    
                    // Mostrar modal
                    modalEditar.style.display = 'block';
                    
                    // Guardar referencia a la nómina seleccionada
                    nominaSeleccionada = nomina;
                } else {
                    throw new Error(data.error || 'Error al cargar los datos de la nómina');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar los datos de la nómina: ' + error.message, 'error');
            });
    };
    
    /**
     * Actualiza una nómina existente
     */
    function actualizarNomina() {
        if (!validarFormulario(formularioEditarNomina)) {
            return;
        }
        
        // Obtener datos del formulario
        const formData = new FormData(formularioEditarNomina);
        formData.append('modo', 'actualizar');
        
        // Convertir FormData a objeto para enviar como JSON
        const datos = {};
        formData.forEach((value, key) => {
            datos[key] = value;
        });
        
        // Deshabilitar botón mientras se procesa
        botonActualizar.disabled = true;
        
        fetch('../php/consulta-finanzas-nomina.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje(data.message, 'exito');
                cerrarModalEditar();
                cargarNominas();
            } else {
                throw new Error(data.error || 'Error al actualizar la nómina');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al actualizar la nómina: ' + error.message, 'error');
        })
        .finally(() => {
            botonActualizar.disabled = false;
        });
    }
    
    /**
     * Prepara el modal de confirmación para eliminar nómina
     */
    window.eliminarNomina = function(nominaId, usuarioId) {
        nominaSeleccionada = {
            nomina_id: nominaId,
            usuario_id: usuarioId
        };
        
        // Mostrar modal de confirmación
        modalConfirmacion.style.display = 'block';
    };
    
    /**
     * Confirma eliminación de nómina
     */
    function confirmarEliminarNomina() {
        if (!nominaSeleccionada) {
            cerrarModalConfirmacion();
            return;
        }
        
        const datos = {
            modo: 'eliminar',
            nomina_id: nominaSeleccionada.nomina_id
        };
        
        fetch('../php/consulta-finanzas-nomina.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarMensaje(data.message, 'exito');
                cargarNominas();
                // Recargar empleados para actualizar la lista después de eliminar una nómina
                cargarEmpleados();
            } else {
                throw new Error(data.error || 'Error al eliminar la nómina');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al eliminar la nómina: ' + error.message, 'error');
        })
        .finally(() => {
            cerrarModalConfirmacion();
        });
    }
    
    /**
     * Genera un reporte de nómina (simulado)
     */
    function generarReporte() {
        if (!empleadoSeleccionado) {
            mostrarMensaje('Debe seleccionar un empleado para generar el reporte', 'error');
            return;
        }
        
        // Aquí se implementaría la lógica para generar un reporte o PDF
        // Por ahora, solo mostramos un mensaje
        mostrarMensaje('Generando reporte de nómina para ' + empleadoSeleccionado.nombre, 'exito');
        
        // Esta funcionalidad requeriría una implementación adicional para generar PDFs
    }
    
    /**
     * Cierra el modal de edición
     */
    function cerrarModalEditar() {
        modalEditar.style.display = 'none';
        formularioEditarNomina.reset();
        nominaSeleccionada = null;
    }
    
    /**
     * Cierra el modal de confirmación
     */
    function cerrarModalConfirmacion() {
        modalConfirmacion.style.display = 'none';
    }
    
    /**
     * Resetea el formulario
     */
    function resetearFormulario(mantenerSeleccion = false) {
        formularioNomina.reset();
        
        if (!mantenerSeleccion) {
            // Limpiar selección de empleado si no se indica lo contrario
            document.querySelectorAll('.fila-empleado').forEach(f => {
                f.classList.remove('fila-seleccionada');
            });
            
            // Deseleccionar radio buttons
            document.querySelectorAll('.radio-empleado').forEach(radio => {
                radio.checked = false;
            });
            
            empleadoSeleccionado = null;
            document.getElementById('usuario_id').value = '';
            botonGuardar.disabled = true;
        }
    }
    
    /**
     * Valida los campos del formulario
     */
    function validarFormulario(formulario) {
        let esValido = true;
        
        // Verificar que se haya seleccionado un empleado (solo para el formulario principal)
        if (formulario.id === 'formulario-nomina' && !empleadoSeleccionado) {
            mostrarMensaje('Debe seleccionar un empleado', 'error');
            return false;
        }
        
        // Validar campos requeridos
        const camposRequeridos = formulario.querySelectorAll('[required]');
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                campo.classList.add('campo-error');
                esValido = false;
            } else {
                campo.classList.remove('campo-error');
            }
        });
        
        // Validar que los montos sean números positivos
        const camposMonto = formulario.querySelectorAll('input[type="number"]');
        camposMonto.forEach(campo => {
            const valor = parseFloat(campo.value);
            if (isNaN(valor) || valor < 0) {
                campo.classList.add('campo-error');
                esValido = false;
            }
        });
        
        // Validar fecha de ingreso (no futura)
        const campoFecha = formulario.querySelector('input[type="date"]');
        if (campoFecha) {
            const fechaIngreso = new Date(campoFecha.value);
            const fechaActual = new Date();
            fechaActual.setHours(0, 0, 0, 0); // Ignorar hora, solo comparar fecha
            
            if (fechaIngreso > fechaActual) {
                campoFecha.classList.add('campo-error');
                esValido = false;
                mostrarMensaje('La fecha de ingreso no puede ser futura', 'error');
            }
        }
        
        if (!esValido) {
            mostrarMensaje('Por favor complete todos los campos requeridos correctamente', 'error');
        }
        
        return esValido;
    }
    
    /**
     * Muestra un mensaje de notificación
     * Exportada al scope global para que pueda ser usada por otros scripts
     */
    window.mostrarMensaje = mostrarMensaje;
    function mostrarMensaje(texto, tipo) {
        const mensaje = document.createElement('div');
        mensaje.className = `mensaje ${tipo}`;
        mensaje.textContent = texto;
        
        // Insertar al inicio del contenido principal
        const contenidoPrincipal = document.querySelector('.contenido-principal');
        contenidoPrincipal.insertBefore(mensaje, contenidoPrincipal.firstChild);
        
        // Quitar mensaje después de 3 segundos
        setTimeout(() => {
            mensaje.remove();
        }, 3000);
    }
});