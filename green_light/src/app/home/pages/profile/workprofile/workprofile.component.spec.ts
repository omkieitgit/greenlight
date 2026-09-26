import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { WorkprofileComponent } from './workprofile.component';

describe('WorkprofileComponent', () => {
  let component: WorkprofileComponent;
  let fixture: ComponentFixture<WorkprofileComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ WorkprofileComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(WorkprofileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
