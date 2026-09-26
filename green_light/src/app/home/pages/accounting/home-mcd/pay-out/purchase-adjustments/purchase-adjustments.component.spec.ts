import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PurchaseAdjustmentsComponent } from './purchase-adjustments.component';

describe('PurchaseAdjustmentsComponent', () => {
  let component: PurchaseAdjustmentsComponent;
  let fixture: ComponentFixture<PurchaseAdjustmentsComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PurchaseAdjustmentsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PurchaseAdjustmentsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
