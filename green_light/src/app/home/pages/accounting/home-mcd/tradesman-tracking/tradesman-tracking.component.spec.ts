import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { TradesmanTrackingComponent } from './tradesman-tracking.component';

describe('TradesmanTrackingComponent', () => {
  let component: TradesmanTrackingComponent;
  let fixture: ComponentFixture<TradesmanTrackingComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ TradesmanTrackingComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TradesmanTrackingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
