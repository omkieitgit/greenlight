import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PropertyButtonComponent } from './property-button.component';

describe('PropertyButtonComponent', () => {
  let component: PropertyButtonComponent;
  let fixture: ComponentFixture<PropertyButtonComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PropertyButtonComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PropertyButtonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
