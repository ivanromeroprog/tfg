import { Controller } from '@hotwired/stimulus';
export default class extends Controller {
    submitForm(e){
        // console.log(e);
        this.element.requestSubmit();
    }

}