import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        //console.log(document.getElementsByName('perpage')[0]);
        document.getElementsByName('perpage')[0].addEventListener('change', (e) => {
            this.element.requestSubmit();
        });
        document.getElementsByName('search')[0].addEventListener('change', (e) => {
            if(e.target.value == ''){
                //console.log('locos');
                this.element.requestSubmit();
            }
        });
    }
}
