import { Controller } from '@hotwired/stimulus';
import animateCSS from '../animatecss.js'

export default class extends Controller {
    static values = {
        respuesta: String,
        pregunta: String,
        nuevo: Boolean
    }

    connect() {
        //console.log(this.nuevoValue);
        if (this.nuevoValue) {
            console.log('Nuevo registro, agregar pregunta.')
            this.agregarpregunta(-1, 1);
        }
    }

    eliminarrespuestaclick(e) {
        //console.log(e.params.pid, e.params.rid);
        let pid = e.params.pid;
        let rid = e.params.rid;

        e.preventDefault();

        window.PreguntarEliminar(
            "¿Quiere eliminar esta respuesta?",
            (result) => {
                if (result.isConfirmed) {
                    if (document.getElementById('pregunta_div_' + pid).getElementsByClassName('respuesta_div').length <= 2) {
                        window.Alertar(
                            'Cada pregunta debe tener al menos dos respuestas.',
                            'No se puede eliminar'
                        )
                    }
                    else {
                        document.getElementById('respuesta_div_' + pid + '_' + rid).parentElement.remove();
                    }
                }
            }
        )
    }

    eliminarpreguntaclick(e) {
        //console.log(e.params.pid);
        let pid = e.params.pid;

        e.preventDefault();

        //window.id_pregunta_eliminar = pid;
        window.PreguntarEliminar(
            "¿Quiere eliminar esta pregunta?",
            (result) => {
                if (result.isConfirmed) {
                    if (this.element.getElementsByClassName('pregunta_div').length < 2) {
                        window.Alertar(
                            'El cuestionario debe tener por lo menos una pregunta.',
                            'No se puede eliminar'
                        )
                    }
                    else
                    {
                        document.getElementById('pregunta_div_' + pid).parentElement.remove();
                    }
                }
            }
        )
    }

    agregarpreguntaclick(e) {
        let pid = null;
        for (let i = 1; i < 50; i++) {
            if (null === document.getElementById('pregunta_div_-' + i)) {
                pid = i * -1;
                break;
            }
        }

        let pnum = this.element.getElementsByClassName('pregunta_div').length + 1;

        if (pid === null) {
            alert('No puede agregar más preguntas');
        }
        else {
            this.agregarpregunta(pid, pnum);
            //let detallepregel = document.getElementById('detalle_preguntas_' + pid);
            //detallepregel
            //detallepregel;
            document.getElementById('detalle_preguntas_' + pid).focus();
            document.getElementById('btn_agregar_respuesta_' + pid).scrollIntoView();;
        }
    }

    agregarrespuestaclick(e) {
        let pid = e.params.pid;
        let rid = null;
        for (let i = 1; i < 50; i++) {
            if (null === document.getElementById('respuesta_div_' + pid + '_-' + i)) {
                rid = i * -1;
                break;
            }
        }

        if (pid === null) {
            alert('No puede agregar más preguntas');
        }
        else {
            this.agregarrespuesta(pid, rid);
            let detalleresptext = document.getElementById('detalle_respuestas_' + pid + '_' + rid + '_texto');
            detalleresptext.scrollIntoView();
            detalleresptext.focus();

        }
    }

    //Helpers
    agregarpregunta(pid, pnum) {
        
        console.log('Agregar pregunta '+ pid + " " + pnum);
        
        let ph = document.createElement("div");
        let tmpl = this.preguntaValue;

        tmpl = tmpl.replace(/%_pid_%/g, pid);
        tmpl = tmpl.replace(/%_pnum_%/g, pnum);
        tmpl = tmpl.replace(/%_ptext_%/g, '');
        tmpl = tmpl.replace(/%_resp_%/g, '');

        ph.innerHTML = tmpl;
        this.element.append(ph)

        this.agregarrespuesta(pid, -1)
        this.agregarrespuesta(pid, -2)
    }

    agregarrespuesta(pid, rid) {
        let ph = document.createElement("div");
        let tmpl = this.respuestaValue;
        tmpl = tmpl.replace(/%_pid_%/g, pid)
        tmpl = tmpl.replace(/%_rid_%/g, rid)
        tmpl = tmpl.replace(/%_rtext_%/g, '');
        tmpl = tmpl.replace(/%_rcorr_%/g, '');
        
        ph.innerHTML = tmpl;

        document.getElementById('pregunta_div_' + pid).append(ph);
    }
}
