import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AccountingDocumentComponent } from './accounting-document.component';

describe('AccountingDocumentComponent', () => {
  let component: AccountingDocumentComponent;
  let fixture: ComponentFixture<AccountingDocumentComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AccountingDocumentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AccountingDocumentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
