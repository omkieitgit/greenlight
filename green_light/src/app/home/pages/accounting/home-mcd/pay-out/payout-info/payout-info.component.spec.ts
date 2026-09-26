import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PayoutInfoComponent } from './payout-info.component';

describe('PayoutInfoComponent', () => {
  let component: PayoutInfoComponent;
  let fixture: ComponentFixture<PayoutInfoComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PayoutInfoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PayoutInfoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
