import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
        
    static targets = ['source']
        
    copy() {
        // Get the text field
        var copyText = this.sourceTarget;

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);
    }
}
