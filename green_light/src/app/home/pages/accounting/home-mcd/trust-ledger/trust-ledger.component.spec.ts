import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { TrustLedgerComponent } from './trust-ledger.component';

describe('TrustLedgerComponent', () => {
  let component: TrustLedgerComponent;
  let fixture: ComponentFixture<TrustLedgerComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ TrustLedgerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TrustLedgerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
