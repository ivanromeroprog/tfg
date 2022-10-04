import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        let h = document.createElement('input');
        h.setAttribute('type', 'text');
        h.setAttribute('id', this.element.getAttribute('id') + '_h');
        h.setAttribute('name', this.element.getAttribute('name'));
        this.element.removeAttribute('name');
        //this.element.removeAttribute('required');
        this.element.parentNode.appendChild(h);
        this.element.setAttribute('data-action', 'change->datetimefix#change');
        this.change();
    }

    change() {
        var c = document.getElementById(this.element.getAttribute('id'));
        var ch =  document.getElementById(this.element.getAttribute('id') + '_h');
        //c.type = 'text';
        if (((c.value.match(/:/g) || []).length) < 2)
        {
            ch.value = c.value = c.value + ':00';
        }
        else
        {
            ch.value = c.value;
        }
        //c.type = 'datetime-local';
    }
}
