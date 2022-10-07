import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        open: String
    }
    connect() {
        if (this.openValue == 'true') {
            let myModal = new bootstrap.Modal(this.element, {});
            myModal.show();
        }
    }
}
