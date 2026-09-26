import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ShowAlarmComponent } from './show-alarm.component';

describe('ShowAlarmComponent', () => {
  let component: ShowAlarmComponent;
  let fixture: ComponentFixture<ShowAlarmComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ShowAlarmComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowAlarmComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
