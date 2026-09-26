import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { RenovationCostsComponent } from './renovation-costs.component';

describe('RenovationCostsComponent', () => {
  let component: RenovationCostsComponent;
  let fixture: ComponentFixture<RenovationCostsComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ RenovationCostsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RenovationCostsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
