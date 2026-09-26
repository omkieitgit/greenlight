import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AaReportComponent } from './aa-report.component';

describe('AaReportComponent', () => {
  let component: AaReportComponent;
  let fixture: ComponentFixture<AaReportComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AaReportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AaReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
