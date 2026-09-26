import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CountyReportComponent } from './county-report.component';

describe('CountyReportComponent', () => {
  let component: CountyReportComponent;
  let fixture: ComponentFixture<CountyReportComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CountyReportComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(CountyReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
