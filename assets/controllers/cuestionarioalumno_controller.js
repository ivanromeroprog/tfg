import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    let formu = this.element.getElementsByTagName('form')[0];
    let formsub = document.getElementById('form_Submit');
    formsub.addEventListener('click', (e) => {
      e.preventDefault();

      let campos = formu.querySelectorAll('input');
      let vacio = true;

      console.log(campos);

      campos.forEach(campo => {
        console.log(campo.checked);
        if (campo.checked) {
          vacio = false;
        }
      });

      if (vacio) {
        window.PreguntarSiNo('No hay respuesta seleccionada\n¿Deséa continuar de todas formas?', (result) => {
          if(result.isConfirmed){
            console.log(result);
            formu.requestSubmit();
          }
        })
      }
      else
      {
          formu.requestSubmit();
      }

    });
  }
}
