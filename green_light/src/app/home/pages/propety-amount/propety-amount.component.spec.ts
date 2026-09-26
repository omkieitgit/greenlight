import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PropetyAmountComponent } from './propety-amount.component';

describe('PropetyAmountComponent', () => {
  let component: PropetyAmountComponent;
  let fixture: ComponentFixture<PropetyAmountComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PropetyAmountComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PropetyAmountComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
