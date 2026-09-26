import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { DefectiveNotesDetailComponent } from './defective-notes-detail.component';

describe('DefectiveNotesDetailComponent', () => {
  let component: DefectiveNotesDetailComponent;
  let fixture: ComponentFixture<DefectiveNotesDetailComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ DefectiveNotesDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DefectiveNotesDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
