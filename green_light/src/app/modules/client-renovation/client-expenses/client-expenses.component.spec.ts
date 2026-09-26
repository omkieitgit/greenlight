import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ClientExpensesComponent } from './client-expenses.component';

describe('ClientExpensesComponent', () => {
  let component: ClientExpensesComponent;
  let fixture: ComponentFixture<ClientExpensesComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ClientExpensesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClientExpensesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
