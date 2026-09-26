import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { MoreAssessmentComponent } from './more-assessment.component';

describe('MoreAssessmentComponent', () => {
  let component: MoreAssessmentComponent;
  let fixture: ComponentFixture<MoreAssessmentComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ MoreAssessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MoreAssessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
