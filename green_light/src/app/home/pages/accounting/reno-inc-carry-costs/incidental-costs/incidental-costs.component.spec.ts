import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { IncidentalCostsComponent } from './incidental-costs.component';

describe('IncidentalCostsComponent', () => {
  let component: IncidentalCostsComponent;
  let fixture: ComponentFixture<IncidentalCostsComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ IncidentalCostsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(IncidentalCostsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
