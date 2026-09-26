import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MscFormComponent } from './msc-form.component';

describe('MscFormComponent', () => {
  let component: MscFormComponent;
  let fixture: ComponentFixture<MscFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MscFormComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(MscFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
