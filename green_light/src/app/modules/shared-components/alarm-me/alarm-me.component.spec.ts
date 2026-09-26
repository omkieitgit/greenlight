import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AlarmMeComponent } from './alarm-me.component';

describe('AlarmMeComponent', () => {
  let component: AlarmMeComponent;
  let fixture: ComponentFixture<AlarmMeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AlarmMeComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AlarmMeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
