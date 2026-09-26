import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VehicleSaleInfoComponent } from './vehicle-sale-info.component';

describe('VehicleSaleInfoComponent', () => {
  let component: VehicleSaleInfoComponent;
  let fixture: ComponentFixture<VehicleSaleInfoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ VehicleSaleInfoComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(VehicleSaleInfoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
