import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DepositLenderCatComponent } from './deposit-lender-cat.component';

describe('DepositLenderCatComponent', () => {
  let component: DepositLenderCatComponent;
  let fixture: ComponentFixture<DepositLenderCatComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DepositLenderCatComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DepositLenderCatComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
