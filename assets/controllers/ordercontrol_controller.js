import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
   static values = {
    order: Number
  }
  click(){
    console.log(this.orderValue);
    let orderel = document.getElementById('order');
    
    if(Number(orderel.value) === this.orderValue)
    {
        orderel.value = orderel.value * -1;
    }else{
        orderel.value = this.orderValue;
    }
    
    document.getElementById('formsearch').requestSubmit();
   }
}
