import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { DepositeLenderComponent } from './deposite-lender.component';

describe('DepositeLenderComponent', () => {
  let component: DepositeLenderComponent;
  let fixture: ComponentFixture<DepositeLenderComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ DepositeLenderComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DepositeLenderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
