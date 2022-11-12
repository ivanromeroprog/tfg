import { Controller } from '@hotwired/stimulus';
import animateCSS from '../animatecss.js'

export default class extends Controller {
    static values = {
        audio: String,
        source: String,
    }

    connect() {
        let audiof = new Audio(this.audioValue);
        //inseguro
        //this.es = new EventSource(this.sourceValue);
        //seguro
        this.es = new EventSource(this.sourceValue, { withCredentials: true });

        this.es.onmessage = event => {
            console.log(JSON.parse(event.data));
            let data = JSON.parse(event.data);
            let name = 'respuesta_' + data.idpregunta + '_' + data.idalumno;
            console.log(name);
            let td = document.getElementById(name);
            console.log(td);
            let span = td.getElementsByTagName('span')[0];


            if (data.correcto) {
                span.innerHTML = '✔️';
                span.setAttribute('title', 'Correcto');
            }
            else {
                span.innerHTML = '❌';
                span.setAttribute('title', 'Incorrecto');
            }
            animateCSS.animateCSS(span, 'flash');
            audiof.volume = 0.2
            audiof.play();
            
            new bootstrap.Tooltip(span, {boundary: document.body});

            /*
            let data = JSON.parse(event.data);
            let frame = document.getElementById('frameasistencia_' + data.id);
            let ael = frame.getElementsByTagName('a')[0];
            let spanno = frame.getElementsByClassName('nolink')[0];
            let spansi = frame.getElementsByClassName('silink')[0];
            let regex = /\/[0-9]+$/ig
        
            ael.href = ael.href.replace(regex,data.estado ? '/0' : '/1');
            
            if(data.estado)
            {
                spanno.setAttribute('class', 'nolink d-none');
                spansi.setAttribute('class', 'silink');
                animateCSS.animateCSS(spansi,'flash');
                audiof.volume = 0.2
                audiof.play();
            }else{
                spanno.setAttribute('class', 'nolink');
                spansi.setAttribute('class', 'silink d-none');
            }
            */

        }
    }
    disconnect() {
        this.es.close();
    }
}
