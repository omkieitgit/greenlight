import { Directive,Input,ElementRef,Renderer2 } from '@angular/core';

@Directive({
  selector: '[textValColor]'
})
export class TextValColorDirective {

  input;

  @Input() set textValColor(input) {
      if(input)
        this.input = input.value.replace(/\$|,+/g, '');
  }

  ngDoCheck() {
      let fn = (this.input && this.input) ? 'addClass' : 'removeClass';
      let className=(this.input>0)?'text-success':'text-danger';
      this.renderer[fn](this.el.nativeElement, className);
  }

  constructor(private el: ElementRef, private renderer: Renderer2) {}
}
