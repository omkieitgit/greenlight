import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DepositLenderComponent } from './deposit-lender.component';

describe('DepositLenderComponent', () => {
  let component: DepositLenderComponent;
  let fixture: ComponentFixture<DepositLenderComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DepositLenderComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DepositLenderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
