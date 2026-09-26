import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PayoutCategoryListComponent } from './payout-category-list.component';

describe('PayoutCategoryListComponent', () => {
  let component: PayoutCategoryListComponent;
  let fixture: ComponentFixture<PayoutCategoryListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PayoutCategoryListComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(PayoutCategoryListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
