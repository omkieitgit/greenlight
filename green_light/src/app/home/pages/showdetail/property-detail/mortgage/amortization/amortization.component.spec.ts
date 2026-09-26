import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AmortizationComponent } from './amortization.component';

describe('AmortizationComponent', () => {
  let component: AmortizationComponent;
  let fixture: ComponentFixture<AmortizationComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AmortizationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AmortizationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
