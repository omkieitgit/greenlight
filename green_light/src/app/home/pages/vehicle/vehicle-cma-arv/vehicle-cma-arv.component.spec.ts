import { ComponentFixture, TestBed } from '@angular/core/testing';

import { VehicleCmaArvComponent } from './vehicle-cma-arv.component';

describe('VehicleCmaArvComponent', () => {
  let component: VehicleCmaArvComponent;
  let fixture: ComponentFixture<VehicleCmaArvComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ VehicleCmaArvComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(VehicleCmaArvComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
