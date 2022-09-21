import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        let orderel = document.getElementById('order');
        let i = 1;
        this.element.querySelectorAll("th.canorder").forEach(e => {
            //console.log('th: ', e);
            //data-controller="ordercontrol" data-ordercontrol-order-value="1" data-action="click->ordercontrol#click" role="button"
            e.setAttribute('data-controller', 'ordercontrol');
            e.setAttribute('data-ordercontrol-order-value', i);
            e.setAttribute('data-action', 'click->ordercontrol#click');
            e.setAttribute('role', 'button');
            if(Math.abs((Number(orderel.value))) === i){
                if(Number(orderel.value) < 0){
                    e.innerHTML += '<i class="bi bi-caret-up-fill"></i>';
                }
                else
                {
                    e.innerHTML += '<i class="bi bi-caret-down-fill"></i>';
                }
            }
            i++;
        });
    }
}
