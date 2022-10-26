import { Controller } from '@hotwired/stimulus';
import animateCSS from '../animatecss.js'

export default class extends Controller {
    static values = {
        respuesta: String,
        pregunta: String,
        nuevo: Boolean
    }

    connect() {
        if(this.nuevoValue){
            this.agregarpregunta(-1,1);
        }
    }
    disconnect() {
        this.es.close();
    }
    agregarpreguntaclick(e){
        //console.log(e);
        /*
        let preguntas = this.element.getElementsByClassName('pregunta_div');
        console.log(preguntas);
        */
       let pid = null;
       for(let i = 1; i<50; i++){
        //console.log(i);
        //console.log(document.getElementById('pregunta_div_-'+i))
        if(null === document.getElementById('pregunta_div_-'+i))
        {
            pid = i*-1;
            break;
        }
       }

       let pnum = this.element.getElementsByClassName('pregunta_div').length + 1;

       if(pid === null){
        alert('No puede agregar más preguntas');
       }
       else{
        this.agregarpregunta(pid,pnum);
        //console.log(document.getElementById('detalle_preguntas_'+pid));
        document.getElementById('detalle_preguntas_'+pid).focus();
       }
    }

    agregarrespuestaclick(e){
        console.log(e);
    }

    agregarpregunta(pid,pnum){
        let ph = document.createElement("div");
        let tmpl = this.preguntaValue;

        tmpl = tmpl.replace(/%_pid_%/g, pid)
        tmpl = tmpl.replace(/%_pnum_%/g, pnum)

        ph.innerHTML = tmpl;
        //console.log(this.element);
        this.element.append(ph)

        //console.log(ph)

        //document.getElementById('pregunta_div_'+pid).append(ph);

        let removeButton = document.getElementById('eliminar_pregunta_'+pid);
        
        removeButton.addEventListener('click', (e) => {
            e.preventDefault();
            
            //window.id_pregunta_eliminar = pid;
            window.Eliminar(
                "¿Quiere elminar esta pregunta?",
            (result) => {
                if (result.isConfirmed) {
                    document.getElementById('pregunta_div_'+pid).parentElement.remove();
                    //window.id_pregunta_eliminar = null;
                  }/*else{
                    window.id_pregunta_eliminar = null;
                  }*/
               }
            )

        });

        this.agregarrespuesta(pid,-1)
        this.agregarrespuesta(pid,-2)
    }

    agregarrespuesta(pid,rid){
        let ph = document.createElement("div");
        let tmpl = this.respuestaValue;
        tmpl = tmpl.replace(/%_pid_%/g, pid)
        tmpl = tmpl.replace(/%_rid_%/g, rid)

        ph.innerHTML = tmpl;

        document.getElementById('pregunta_div_'+pid).append(ph);

        let removeButton = document.getElementById('eliminar_respuesta_'+pid+'_'+rid);
        
        removeButton.addEventListener('click', (e) => {
            e.preventDefault();
            
            window.Eliminar(
                "¿Quiere elminar esta respuesta?",
            function(result){
                if (result.isConfirmed) {
                    Swal.fire(
                      'Deleted!',
                      'Your file has been deleted.',
                      'success'
                    )
                  }
               }
            )

        });
    }
}
