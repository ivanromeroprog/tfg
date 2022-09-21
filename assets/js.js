import $ from "jquery";

const dtlang = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "infoThousands": ",",
    "loadingRecords": "Cargando...",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
    },
    "aria": {
        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
        "copy": "Copiar",
        "colvis": "Visibilidad",
        "collection": "Colección",
        "colvisRestore": "Restaurar visibilidad",
        "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
        "copySuccess": {
            "1": "Copiada 1 fila al portapapeles",
            "_": "Copiadas %ds fila al portapapeles"
        },
        "copyTitle": "Copiar al portapapeles",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Mostrar todas las filas",
            "_": "Mostrar %d filas"
        },
        "pdf": "PDF",
        "print": "Imprimir",
        "renameState": "Cambiar nombre",
        "updateState": "Actualizar",
        "createState": "Crear Estado",
        "removeAllStates": "Remover Estados",
        "removeState": "Remover",
        "savedStates": "Estados Guardados",
        "stateRestore": "Estado %d"
    },
    "autoFill": {
        "cancel": "Cancelar",
        "fill": "Rellene todas las celdas con <i>%d<\/i>",
        "fillHorizontal": "Rellenar celdas horizontalmente",
        "fillVertical": "Rellenar celdas verticalmentemente"
    },
    "decimal": ",",
    "searchBuilder": {
        "add": "Añadir condición",
        "button": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
        },
        "clearAll": "Borrar todo",
        "condition": "Condición",
        "conditions": {
            "date": {
                "after": "Despues",
                "before": "Antes",
                "between": "Entre",
                "empty": "Vacío",
                "equals": "Igual a",
                "notBetween": "No entre",
                "notEmpty": "No Vacio",
                "not": "Diferente de"
            },
            "number": {
                "between": "Entre",
                "empty": "Vacio",
                "equals": "Igual a",
                "gt": "Mayor a",
                "gte": "Mayor o igual a",
                "lt": "Menor que",
                "lte": "Menor o igual que",
                "notBetween": "No entre",
                "notEmpty": "No vacío",
                "not": "Diferente de"
            },
            "string": {
                "contains": "Contiene",
                "empty": "Vacío",
                "endsWith": "Termina en",
                "equals": "Igual a",
                "notEmpty": "No Vacio",
                "startsWith": "Empieza con",
                "not": "Diferente de",
                "notContains": "No Contiene",
                "notStarts": "No empieza con",
                "notEnds": "No termina con"
            },
            "array": {
                "not": "Diferente de",
                "equals": "Igual",
                "empty": "Vacío",
                "contains": "Contiene",
                "notEmpty": "No Vacío",
                "without": "Sin"
            }
        },
        "data": "Data",
        "deleteTitle": "Eliminar regla de filtrado",
        "leftTitle": "Criterios anulados",
        "logicAnd": "Y",
        "logicOr": "O",
        "rightTitle": "Criterios de sangría",
        "title": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
        },
        "value": "Valor"
    },
    "searchPanes": {
        "clearMessage": "Borrar todo",
        "collapse": {
            "0": "Paneles de búsqueda",
            "_": "Paneles de búsqueda (%d)"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Sin paneles de búsqueda",
        "loadMessage": "Cargando paneles de búsqueda",
        "title": "Filtros Activos - %d",
        "showMessage": "Mostrar Todo",
        "collapseMessage": "Colapsar Todo"
    },
    "select": {
        "cells": {
            "1": "1 celda seleccionada",
            "_": "%d celdas seleccionadas"
        },
        "columns": {
            "1": "1 columna seleccionada",
            "_": "%d columnas seleccionadas"
        },
        "rows": {
            "1": "1 fila seleccionada",
            "_": "%d filas seleccionadas"
        }
    },
    "thousands": ".",
    "datetime": {
        "previous": "Anterior",
        "next": "Proximo",
        "hours": "Horas",
        "minutes": "Minutos",
        "seconds": "Segundos",
        "unknown": "-",
        "amPm": [
            "AM",
            "PM"
        ],
        "months": {
            "0": "Enero",
            "1": "Febrero",
            "10": "Noviembre",
            "11": "Diciembre",
            "2": "Marzo",
            "3": "Abril",
            "4": "Mayo",
            "5": "Junio",
            "6": "Julio",
            "7": "Agosto",
            "8": "Septiembre",
            "9": "Octubre"
        },
        "weekdays": [
            "Dom",
            "Lun",
            "Mar",
            "Mie",
            "Jue",
            "Vie",
            "Sab"
        ]
    },
    "editor": {
        "close": "Cerrar",
        "create": {
            "button": "Nuevo",
            "title": "Crear Nuevo Registro",
            "submit": "Crear"
        },
        "edit": {
            "button": "Editar",
            "title": "Editar Registro",
            "submit": "Actualizar"
        },
        "remove": {
            "button": "Eliminar",
            "title": "Eliminar Registro",
            "submit": "Eliminar",
            "confirm": {
                "_": "¿Está seguro que desea eliminar %d filas?",
                "1": "¿Está seguro que desea eliminar 1 fila?"
            }
        },
        "error": {
            "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
        },
        "multi": {
            "title": "Múltiples Valores",
            "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
            "restore": "Deshacer Cambios",
            "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
        }
    },
    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
    "stateRestore": {
        "creationModal": {
            "button": "Crear",
            "name": "Nombre:",
            "order": "Clasificación",
            "paging": "Paginación",
            "search": "Busqueda",
            "select": "Seleccionar",
            "columns": {
                "search": "Búsqueda de Columna",
                "visible": "Visibilidad de Columna"
            },
            "title": "Crear Nuevo Estado",
            "toggleLabel": "Incluir:"
        },
        "emptyError": "El nombre no puede estar vacio",
        "removeConfirm": "¿Seguro que quiere eliminar este %s?",
        "removeError": "Error al eliminar el registro",
        "removeJoiner": "y",
        "removeSubmit": "Eliminar",
        "renameButton": "Cambiar Nombre",
        "renameLabel": "Nuevo nombre para %s",
        "duplicateError": "Ya existe un Estado con este nombre.",
        "emptyStates": "No hay Estados guardados",
        "removeTitle": "Remover Estado",
        "renameTitle": "Cambiar Nombre Estado"
    }
};


window.addEventListener("load", function () {

    //Choises.js aplicar a clase js-choice
    var slides = document.getElementsByClassName("js-choice");
    for (var i = 0; i < slides.length; i++) {
        newChoisesJs(slides.item(i));
    }

    //Boton agregar a clase add_item_link
    document
        .querySelectorAll('.add_item_link')
        .forEach(btn => {
            btn.addEventListener("click", addFormToCollection)
        });

    //Boton eliminar detalles
    //Actualizar Stock al iniciar
    document
        .querySelectorAll('tbody.detalles tr')
        .forEach((item) => {
            addFormDeleteLink(item);
            updateStock(item);
        });

    //Actualizar stock cuando cambia valor
    /*
     document
     .querySelectorAll('tbody.detalles tr')
     .forEach((item) => {
     
     });
     */

    //Data Table
    if ($('#tabla')) {
        $('#tabla').DataTable({
            language: dtlang,
            order: []
        });
    }
    if ($('#tablaventa')) {
        $('#tablaventa').DataTable({
            language: dtlang,
            order: []
        });
    }

    refreshTotal();
});

const newChoisesJs = (item) => {
    return new Choices(item, {
        loadingText: 'Cargando...',
        noResultsText: 'No hay resultados',
        noChoicesText: 'No hay opciones para seleccionar',
        itemSelectText: 'Presione para seleccionar'
    });
};

const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('tr');
    item.classList.add('animate__animated')
    item.classList.add('animate__flash')

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );
    collectionHolder.insertBefore(item, collectionHolder.firstChild);
    ;



    //Agregar link para borrar
    addFormDeleteLink(item);



    //Agregar choises.js
    item.querySelectorAll('.js-choice')
        .forEach((item) => {
            item.focus();
            tmp = newChoisesJs(item);
        });

    //Actualizar Stock
    updateStock(item);

    //Total
    refreshTotal();

    collectionHolder.dataset.index++;
};

const addFormDeleteLink = (item) => {
    const removeFormButton = document.createElement('td');
    removeFormButton.innerHTML = '<button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>';
    item.append(removeFormButton);
    $(removeFormButton.firstChild).confirmButton({

        confirm: "¿Esta seguro de quitar este producto?",
        canceltxt: "Cancelar",
        confirmtxt: "Confirmar",
        titletxt: "Atención"

    });

    removeFormButton.firstChild.addEventListener('click', (e) => {
        e.preventDefault();
        item.remove();
        refreshTotal();
    });
}

const updateStock = (item) => {

    var stockEl;
    var prodEl;
    var cantEl;

    //Buscar el control de stock
    item.querySelectorAll('.producto_stock')
        .forEach((stock) => {
            stockEl = stock;
        });

    //Buscar el control del producto
    item.querySelectorAll('.js-choice')
        .forEach((prod) => {
            prodEl = prod;
        });

    //Actualizar el control de Stock con el valor del producto seleccionado
    ps.forEach((v) => {
        if (v.id == prodEl.value) {
            stockEl.value = v.Stock;
        }
    });

    //Buscar el control de Cantidad y agregarle el tooltip de Stock
    item.querySelectorAll('.producto_cantidad')
        .forEach((cant) => {
            cantEl = cant;
            cantEl.setAttribute('title', 'Stock Actual: ' + stockEl.value);
            new bootstrap.Tooltip(cantEl);
        });

    //Configurar el valor máximo y el actual para no superar el Stock
    cantEl.setAttribute('max', stockEl.value);
    if (cantEl.value == '') {
        cantEl.value = 1;
    }
    if (parseInt(cantEl.value) > parseInt(stockEl.value)) {
        //alert(cantEl.value + ' ' + stockEl.value + (parseInt(cantEl.value) > parseInt(stockEl.value)));
        cantEl.value = stockEl.value;
    }

    //Agregar esta función al evento de cambio de Valor del Producto
    prodEl.onchange = function () {
        updateStock(item);
        refreshTotal();
    };

    //Agregar refreshTotal al evento de cambio de Valor de Cantidad
    cantEl.onchange = function () {
        refreshTotal();
    };
};

const refreshTotal = () => {

    var total = 0;

    document
        .querySelectorAll('tbody.detalles tr')
        .forEach((item) => {
            var prodEl;
            var cantEl;

            //Buscar el control del producto
            item.querySelectorAll('.js-choice')
                .forEach((prod) => {
                    prodEl = prod;
                });

            //Precio
            precio = 0;
            pp.forEach((v) => {
                if (v.id == prodEl.value) {
                    precio = parseFloat(v.Precio);
                }
            });

            //Buscar el control de Productos
            item.querySelectorAll('.producto_cantidad')
                .forEach((cant) => {
                    cantEl = cant;
                });

            total += (parseFloat(cantEl.value) * precio);
        });

    if (document.getElementById('totalhtml')) {
        let nf = Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS',

            // These options are needed to round to whole numbers if that's what you want.
            //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
            //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        });

        document.getElementById('totalhtml').innerHTML = nf.format(parseFloat(total));
    }
}


const fechacel = (campo) => {
    var c = document.getElementById(campo);
    var ch = document.getElementById(campo + '_h');
    //c.type = 'text';
    if (((c.value.match(/:/g) || []).length) < 2) {
        ch.value = c.value + ':00';
    }
    else {
        ch.value = c.value;
    }
    //c.type = 'datetime-local';
}