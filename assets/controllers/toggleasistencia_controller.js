import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        ida: Number
    }

    change() {
        console.log(this.idaValue);
    }
}
