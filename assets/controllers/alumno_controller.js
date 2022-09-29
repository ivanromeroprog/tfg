import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() { 
        var agral = document.getElementById('agregar_alumno_form');
        var fields = agral.querySelectorAll('.form-control');
        
        fields.forEach((e) => {
            e.removeAttribute('required');
        });
        
        agral.addEventListener('show.bs.collapse', function(){
            fields.forEach((e) => {
                e.setAttribute('required','required');
            });
        });
              
        agral.addEventListener('hidden.bs.collapse', function(){
            fields.forEach((e) => {
                e.removeAttribute('required');
            });
        });
    }
}
