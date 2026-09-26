import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { DefectiveNotesComponent } from './defective-notes.component';

describe('DefectiveNotesComponent', () => {
  let component: DefectiveNotesComponent;
  let fixture: ComponentFixture<DefectiveNotesComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ DefectiveNotesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DefectiveNotesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
