import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { EstimatedLatePaymentsComponent } from './estimated-late-payments.component';

describe('EstimatedLatePaymentsComponent', () => {
  let component: EstimatedLatePaymentsComponent;
  let fixture: ComponentFixture<EstimatedLatePaymentsComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ EstimatedLatePaymentsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EstimatedLatePaymentsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
