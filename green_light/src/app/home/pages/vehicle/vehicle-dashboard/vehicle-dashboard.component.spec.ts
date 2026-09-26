import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VehicleDashboardComponent } from './vehicle-dashboard.component';

describe('VehicleDashboardComponent', () => {
  let component: VehicleDashboardComponent;
  let fixture: ComponentFixture<VehicleDashboardComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ VehicleDashboardComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(VehicleDashboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
