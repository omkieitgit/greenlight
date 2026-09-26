import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AdditionFieldComponent } from './addition-field.component';

describe('AdditionFieldComponent', () => {
  let component: AdditionFieldComponent;
  let fixture: ComponentFixture<AdditionFieldComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AdditionFieldComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AdditionFieldComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
