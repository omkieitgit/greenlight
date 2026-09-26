import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AlertComponent } from './shared/_directives';

@NgModule({
  imports: [CommonModule],
  declarations: [AlertComponent],
  providers: [],
  exports: [AlertComponent]
})
export class AlertModule {}