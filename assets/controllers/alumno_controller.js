import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {

        let fields = this.element.querySelectorAll('.form-control');
        //console.log(fields);
        fields.forEach((e) => {
            e.removeAttribute('required');
        });

        this.cargar_options_alumno_modificar();

        /*
        let  ca = document.getElementById('curso_alumnos');
        ca.addEventListener('change',this.cargar_options_alumno_modificar);
*/
        //sa.innerHTML = ca.innerHTML;



        /*
        var agral = document.getElementById('agregar_alumno_form');
        var moal = document.getElementById('modificar_alumno_form');
        var fields = agral.querySelectorAll('.form-control');
        var fields2 = moal.querySelectorAll('.form-control');
         
        fields.forEach((e) => {
            e.removeAttribute('required');
        });
        
        fields2.forEach((e) => {
            e.removeAttribute('required');
        });
                
        moal.addEventListener('show.bs.collapse', function(){
            fields2.forEach((e) => {
                e.setAttribute('required','required');
            });
        });
              
        moal.addEventListener('hidden.bs.collapse', function(){
            fields2.forEach((e) => {
                e.removeAttribute('required');
            });
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
             
         */
    }

    cargar_options_alumno_modificar() {
        let ca = document.getElementById('curso_alumnos');
        let sa = document.getElementById('curso_alumno_mod_id');
        let sa_selected = sa.value;

        sa.innerHTML = '';

        let opt;
        opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = '';
        sa.appendChild(opt);

        for (let option of ca.options) {
            if (option.selected) {
                opt = document.createElement('option');
                opt.value = option.value;
                opt.innerHTML = option.innerHTML;
                if (opt.value == sa_selected) {
                    opt.selected = true;
                }
                sa.appendChild(opt);
            }
        }
        this.cargar_alumno_modificar();
    }

    cargar_alumno_modificar() {
        let sa = document.getElementById('curso_alumno_mod_id');
        let nombre = document.getElementById('curso_alumno_mod_nombre');
        let apellido = document.getElementById('curso_alumno_mod_apellido');
        let cua = document.getElementById('curso_alumno_mod_cua');
        let re = /([^,]+), ([^\(]+) \(([^\)]+)\)/g;
        let match;

        //TODO: hacerlo con ajax
        for (let option of sa.options) {
            if (option.selected) {
                //alert(option.textContent)
                match = re.exec(option.textContent);
                break;
            }
        }
        if (match != null) {
            nombre.value = match[2];
            apellido.value = match[1];
            cua.value = match[3];
        }
        else {
            nombre.value = '';
            apellido.value = '';
            cua.value = '';
        }
    }

    mostrar_agregar_alumno_e(){
        this.mostrar_agregar_alumno();
    }

    mostrar_modificar_alumno_e(){
        this.mostrar_modificar_alumno();
    }

    mostrar_agregar_alumno(estado = null) {
        let agel = document.getElementById('agregar_alumno_form');

        let fields = agel.querySelectorAll('.form-control');
        //console.log(estado);


        if (estado === true || (estado === null && agel.style.display == "none")) {
            agel.style.display = "flex";
            fields.forEach((e) => {
                e.setAttribute('required', 'required');
            });
            document.getElementById('curso_alumno_agregar').scrollIntoView();
        }
        else {
            agel.style.display = "none";
            fields.forEach((e) => {
                e.removeAttribute('required');
            });
        }
        if (estado === null) {
            this.mostrar_modificar_alumno(false);
        }
    }


    mostrar_modificar_alumno(estado = null) {
        let moel = document.getElementById('modificar_alumno_form');

        let fields = moel.querySelectorAll('.form-control');
        //console.log(estado);

        if (estado === true || (estado === null && moel.style.display == "none")) {
            moel.style.display = "flex";
            fields.forEach((e) => {
                e.setAttribute('required', 'required');
            });
            document.getElementById('curso_alumno_modificar').scrollIntoView();
        }
        else {
            moel.style.display = "none";
            fields.forEach((e) => {
                e.removeAttribute('required');
            });
        }
        if (estado === null) {
            this.mostrar_agregar_alumno(false);
        }

    }
}
