import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ReScheduleComponent } from './re-schedule.component';

describe('ReScheduleComponent', () => {
  let component: ReScheduleComponent;
  let fixture: ComponentFixture<ReScheduleComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ReScheduleComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ReScheduleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
