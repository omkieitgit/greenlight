import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { RenoIncCarryCostsComponent } from './reno-inc-carry-costs.component';

describe('RenoIncCarryCostsComponent', () => {
  let component: RenoIncCarryCostsComponent;
  let fixture: ComponentFixture<RenoIncCarryCostsComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ RenoIncCarryCostsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RenoIncCarryCostsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
