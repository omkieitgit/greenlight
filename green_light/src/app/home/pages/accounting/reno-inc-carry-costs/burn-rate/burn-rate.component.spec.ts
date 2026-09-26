import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BurnRateComponent } from './burn-rate.component';

describe('BurnRateComponent', () => {
  let component: BurnRateComponent;
  let fixture: ComponentFixture<BurnRateComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ BurnRateComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(BurnRateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
