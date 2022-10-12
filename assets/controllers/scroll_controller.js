import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        location: String
    }
    connect() {
        window.location.href="#"+this.locationValue;
        console.log(this.locationValue);
    }
}
