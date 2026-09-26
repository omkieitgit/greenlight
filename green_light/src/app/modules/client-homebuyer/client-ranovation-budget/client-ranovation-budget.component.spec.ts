import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ClientRanovationBudgetComponent } from './client-ranovation-budget.component';

describe('ClientRanovationBudgetComponent', () => {
  let component: ClientRanovationBudgetComponent;
  let fixture: ComponentFixture<ClientRanovationBudgetComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ClientRanovationBudgetComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClientRanovationBudgetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
