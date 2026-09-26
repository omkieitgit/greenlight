import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AccountingTrackingComponent } from './accounting-tracking.component';

describe('AccountingTrackingComponent', () => {
  let component: AccountingTrackingComponent;
  let fixture: ComponentFixture<AccountingTrackingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AccountingTrackingComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AccountingTrackingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
