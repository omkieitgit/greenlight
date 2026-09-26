import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AddToAlarmComponent } from './add-to-alarm.component';

describe('AddToAlarmComponent', () => {
  let component: AddToAlarmComponent;
  let fixture: ComponentFixture<AddToAlarmComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AddToAlarmComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddToAlarmComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
