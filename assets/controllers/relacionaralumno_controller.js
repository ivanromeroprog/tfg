import { Controller } from '@hotwired/stimulus';
import animateCSS from '../animatecss.js'

export default class extends Controller {
    static dragElement = null;
    static dragId = 0;

    connect() {
        // document.querySelectorAll('.concepto-a-rel-b').forEach(conceptoarel => {
        //     if(conceptoarel.value != '')
        //     {
        //         const ida = conceptoarel.dataset.ida;
        //         const idb = conceptoarel.value;
                
        //         const conceptoa = document.getElementById('presentacion_actividad_concepto_' + ida);
        //         const conceptob = document.getElementById('presentacion_actividad_concepto_' + idb);
        
        //         //Mover elemento a dontro de b
        //         conceptob.parentNode.append(conceptoa);
        //         this.dragElement.style.visibility = 'visible';
        //         this.dragElement.classList.add('relacionado');
        //     }
        // });
    }


    conceptoaclick(e){
        //Si ya esta relacionado con un conceto b, lo reseteamos
        const conceptoarel = document.getElementById('presentacion_actividad_' + e.params.ida);
        if(conceptoarel.value != '')
        {
            conceptoarel.value = '';
            this.conceptoareset(conceptoarel);
            return;
        }

        //Volver todos los conceptos a a su estado original
        this.conceptosaclear();

        //Obtener datos del elemento seleccionado actualmente
        this.dragElement = document.getElementById('presentacion_actividad_concepto_' + e.params.ida);
        this.dragId = e.params.ida;
        
        //Establecer estilos y mensaje de elemento seleccioando
        this.dragElement.classList.add('bg-primary');
        let concepto_tooltip = bootstrap.Tooltip.getInstance(this.dragElement);
        if(concepto_tooltip) concepto_tooltip.setContent({ '.tooltip-inner': 'Haz clic en el concepto relacionado.' });
    }

    conceptoadragstart(e){
        this.dragElement = document.getElementById('presentacion_actividad_concepto_' + e.params.ida);
        this.dragId = e.params.ida;
        setTimeout(() => {
            this.dragElement.style.visibility = 'hidden';
        },0);
        // console.log('DRAG start');
        console.log(this.dragElement);
    }

    conceptoadragend(e){
        setTimeout(() => {
            this.dragElement = null;
            this.dragId = 0;
        },0);
        if(this.dragElement)this.dragElement.style.visibility = 'visible';
        // console.log('DRAG end');
        // console.log(dragElement);
    }

    conceptobdragover(e){
        e.preventDefault()
        // console.log('DRAG dragenter');
    }
    conceptobdragenter(e){
        // console.log('DRAG dragenter');
    }
    conceptobdragleave(e){
        // console.log('DRAG dragleave');
    }
    conceptobdrop(e){
        if(this.dragId){
            this.relacionarconceptos(this.dragId,e.params.idb);
            document.getElementById('modificado').value=this.dragId;
            this.conceptosaclear();
            setTimeout(
                ()=>{
                    this.element.requestSubmit();
                },100
            );
            
        }
    }

    conceptoareset(conceptoarel){
        const currentIda = conceptoarel.dataset.ida;
        const conceptoacontainer = conceptoarel.parentNode;
        const conceptoa = document.getElementById('presentacion_actividad_concepto_' + currentIda);
        conceptoa.classList.remove('relacionado');
        conceptoacontainer.append(conceptoa);

        let concepto_tooltip = bootstrap.Tooltip.getInstance(conceptoa);
        setTimeout(
            ()=>{
                if(concepto_tooltip) concepto_tooltip.hide();
            },10
        )
        
    }
    conceptosaclear(){
        this.element.querySelectorAll('.concepto-a').forEach((item) => {
            let concepto_tooltip = bootstrap.Tooltip.getInstance(item);
            if(concepto_tooltip) concepto_tooltip.setContent({ '.tooltip-inner': 'Haz clic o arrastra.' });
            item.classList.remove('bg-primary');
        });
        this.dragElement = null;
        this.dragId = 0;
    }

    relacionarconceptos(ida,idb){
        //Quitar concepto b de relación anterior
        document.querySelectorAll('.concepto-a-rel-b').forEach(item => {
            if(item.value == idb)
            {
                item.value = '';
                this.conceptoareset(item)
            }
        });

        //Relacionar a con b
        document.getElementById('presentacion_actividad_' + ida).value = idb;

        //Mover elemento a dontro de b
        const conceptob = document.getElementById('presentacion_actividad_concepto_' + idb);
        conceptob.parentNode.append(this.dragElement);
        this.dragElement.style.visibility = 'visible';
        this.dragElement.classList.add('relacionado');
    }
}
