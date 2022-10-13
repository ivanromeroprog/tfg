import { Controller } from '@hotwired/stimulus';

//TODO: eliminar esto si no se usa, tambien de templates/asistencia/edit
export default class extends Controller {
    static values = {
        ida: Number
    }

    change() {
        console.log(this.idaValue);
    }
}
