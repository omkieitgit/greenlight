import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ExportTypeComponent } from './export-type.component';

describe('ExportTypeComponent', () => {
  let component: ExportTypeComponent;
  let fixture: ComponentFixture<ExportTypeComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ExportTypeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ExportTypeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
