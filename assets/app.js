/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

//Libs
window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js');
import 'animate.css';
window.Swal = require('sweetalert2');

// import customAlert from 'custom-alert/dist/js/custom-alert-debug.js';
// customAlert();

//Locales
//import './confirmbutton.js';
//import animateCSS from './animatecss.js'
//import mercureapp from './mercureapp.js'



//Eventos
const loadTooltips = (e) => {
  document.querySelectorAll('[role="tooltip"]').forEach((tooltip)=>{
    tooltip.remove();
  });

  let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl, {boundary: document.body});
  })
}

document.addEventListener('turbo:load', loadTooltips);
document.addEventListener('turbo:frame-load', loadTooltips);


//Funciones globales

window.PreguntarEliminar = function (texto, callBackF) {
  Swal.fire({
    title: '¿Está seguro?',
    text: texto,
    //icon: 'warning',
    focusCancel: true,
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    }
  }).then((result) => {callBackF(result)})
}

window.PreguntarSiNo= function (texto, callBackF) {
  Swal.fire({
    title: '¿Está seguro?',
    text: texto,
    //icon: 'warning',
    focusCancel: true,
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí',
    cancelButtonText: 'No',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    }
  }).then((result) => {callBackF(result)})
}

window.Alertar = function (texto, titulo = 'Atención') {
  Swal.fire({
    title: titulo,
    text: texto,
    //icon: 'warning',
    confirmButtonColor: '#6c757d',
    confirmButtonText: 'Aceptar',
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    }
  });
}
//HACK: Convertir animateCSS en variable global, hay otra forma?
//window.animateCSS = animateCSS.animateCSS;
//window.mercureapp = mercureapp;