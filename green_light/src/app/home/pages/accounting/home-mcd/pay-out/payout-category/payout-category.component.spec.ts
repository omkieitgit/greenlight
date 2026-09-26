import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PayoutCategoryComponent } from './payout-category.component';

describe('PayoutCategoryComponent', () => {
  let component: PayoutCategoryComponent;
  let fixture: ComponentFixture<PayoutCategoryComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PayoutCategoryComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PayoutCategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
