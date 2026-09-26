import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CarryCostsComponent } from './carry-costs.component';

describe('CarryCostsComponent', () => {
  let component: CarryCostsComponent;
  let fixture: ComponentFixture<CarryCostsComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CarryCostsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CarryCostsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
