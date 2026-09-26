import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SqftCalculationComponent } from './sqft-calculation.component';

describe('SqftCalculationComponent', () => {
  let component: SqftCalculationComponent;
  let fixture: ComponentFixture<SqftCalculationComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SqftCalculationComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SqftCalculationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
