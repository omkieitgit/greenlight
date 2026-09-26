import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DepositLinkComponent } from './deposit-link.component';

describe('DepositLinkComponent', () => {
  let component: DepositLinkComponent;
  let fixture: ComponentFixture<DepositLinkComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DepositLinkComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DepositLinkComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
