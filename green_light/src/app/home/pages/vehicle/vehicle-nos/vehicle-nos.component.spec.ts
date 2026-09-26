import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VehicleNosComponent } from './vehicle-nos.component';

describe('VehicleNosComponent', () => {
  let component: VehicleNosComponent;
  let fixture: ComponentFixture<VehicleNosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ VehicleNosComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(VehicleNosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
