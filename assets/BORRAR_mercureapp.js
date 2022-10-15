import animateCSS from './animatecss.js'

export default {
    asistenciaDocente: event => {
        console.log(JSON.parse(event.data));
        let data = JSON.parse(event.data);
        let frame = document.getElementById('frameasistencia_' + data.id);
        let ael = frame.getElementsByTagName('a')[0];
        let spanno = frame.getElementsByClassName('nolink')[0];
        let spansi = frame.getElementsByClassName('silink')[0];
        let regex = /\/[0-9]+$/ig
    
        console.log(ael);
    
        ael.href = ael.href.replace(regex,data.estado ? '/0' : '/1');
        
        if(data.estado)
        {
            spanno.setAttribute('class', 'nolink d-none');
            spansi.setAttribute('class', 'silink');
            //spansi.classList.add('animate__animated', 'animate__flash');
            animateCSS.animateCSS(spansi,'flash');
            window.notaudio.volume = 0.2
            window.notaudio.play();
        }else{
            spanno.setAttribute('class', 'nolink');
            spansi.setAttribute('class', 'silink d-none');
            //window.animateCSS(spanno,'backOutDown');
        }
    
        console.log(frame);
    }
}