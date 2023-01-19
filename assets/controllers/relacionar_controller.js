import { Controller } from '@hotwired/stimulus';
import animateCSS from '../animatecss.js'

export default class extends Controller {
    static values = {
        //respuesta: String,
        pareja: String,
        nuevo: Boolean
    }

    connect() {
        //console.log(this.nuevoValue);
        if (this.nuevoValue) {
            //console.log('Nuevo registro, agregar pareja.')
            this.agregarpareja(-1, -2);
        }
    }

    eliminarparejaclick(e) {
        //console.log(e.params.pid);
        let pid = e.params.pid;
        let rid = e.params.rid;
        e.preventDefault();

        //window.id_pareja_eliminar = pid;
        window.PreguntarEliminar(
            "¿Quiere eliminar esta pareja?",
            (result) => {
                if (result.isConfirmed) {
                    if (this.element.getElementsByClassName('pareja_div').length < 2) {
                        window.Alertar(
                            'La actividad debe tener por lo menos una pareja.',
                            'No se puede eliminar'
                        )
                    }
                    else {
                        if (pid > 0) {
                            document.getElementById('detalle_eliminar').value += '|' + pid + '|' + rid
                        }
                        document.getElementById('pareja_div_' + pid).parentElement.remove();
                    }
                }
            }
        )
    }

    agregarparejaclick(e) {
        let pid = null;
        let rid = null;
        for (let i = 1; i < 50; i++) {
            if (null === document.getElementById('detalle_parejas_-' + i)) {
                pid = i * -1;
                rid = pid - 1;
                break;
            }
        }

        //let pnum = this.element.getElementsByClassName('pareja_div').length + 1;

        if (pid === null) {
            alert('No puede agregar más parejas');
        }
        else {
            this.agregarpareja(pid, rid);
            //let detallepregel = document.getElementById('detalle_parejas_' + pid);
            //detallepregel
            //detallepregel;
            document.getElementById('detalle_parejas_' + pid).focus();
            document.getElementById('detalle_parejas_' + pid).scrollIntoView();;
        }
    }


    cambiartipo(e) {
        //console.log(e.params.pid);
        let id = e.params.id;
        let val = e.target.value;

        if (val == 0)
        {
            document.getElementById('contenedor_imagen_' + id).classList.add("d-none");
            document.getElementById('contenedor_texto_' + id).classList.remove("d-none");
            document.getElementById('detalle_imagenes_' + id).disabled = true;
            document.getElementById('detalle_parejas_' + id).disabled = false;
        }
        else
        {
            document.getElementById('contenedor_imagen_' + id).classList.remove("d-none");
            document.getElementById('contenedor_texto_' + id).classList.add("d-none");
            document.getElementById('detalle_imagenes_' + id).disabled = false;
            document.getElementById('detalle_parejas_' + id).disabled = true;
        }
    }
    //Helpers
    agregarpareja(pid, rid) {

        //console.log('Agregar pareja '+ pid + " " + pnum);

        let ph = document.createElement("div");
        let tmpl = this.parejaValue;

        tmpl = tmpl.replace(/%_pid_%/g, pid);
        tmpl = tmpl.replace(/%_rid_%/g, rid);
        tmpl = tmpl.replace(/%_rtext_%/g, '');
        tmpl = tmpl.replace(/%_ptext_%/g, '');

        ph.innerHTML = tmpl;
        this.element.append(ph)

        //this.agregarrespuesta(pid, -1)
        //this.agregarrespuesta(pid, -2)
    }

}
