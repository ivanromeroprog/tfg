import { Controller } from '@hotwired/stimulus';
import animateCSS from '../animatecss.js'

export default class extends Controller {
    static values = {
        audio: String,
        source: String,
    }

    connect() {
        let audiof = new Audio(this.audioValue);
        this.es = new EventSource(this.sourceValue);

        this.es.onmessage = event => {
            //console.log(JSON.parse(event.data));
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
                //spansi.classList.add('animate__animated', 'animate__flash');
                animateCSS.animateCSS(spansi,'flash');
                audiof.volume = 0.2
                audiof.play();
            }else{
                spanno.setAttribute('class', 'nolink');
                spansi.setAttribute('class', 'silink d-none');
                //window.animateCSS(spanno,'backOutDown');
            }
        }
    }
    disconnect() {
        this.es.close();
    }
}
