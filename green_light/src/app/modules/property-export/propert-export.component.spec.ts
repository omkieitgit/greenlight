import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PropertExportComponent } from './propert-export.component';

describe('PropertExportComponent', () => {
  let component: PropertExportComponent;
  let fixture: ComponentFixture<PropertExportComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PropertExportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PropertExportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
