import { Directive, HostListener, ElementRef, EventEmitter, Output } from '@angular/core';

@Directive({
  selector: '[appOnBlurSave]'
})
export class OnBlurSaveDirective {

  @Output() updatedValue=new EventEmitter<any>();

  constructor(private el: ElementRef) { }

  @HostListener('change', ['$event.target'])
  onChange($event) {
    let name=this.el.nativeElement.getAttribute('formcontrolname');
    name=name?name:this.el.nativeElement.getAttribute('name');
    let value =this.el.nativeElement.value;
    let data={'name':name,'value':value,'el':this.el.nativeElement};
    this.updatedValue.emit(data);
  }

  
}
