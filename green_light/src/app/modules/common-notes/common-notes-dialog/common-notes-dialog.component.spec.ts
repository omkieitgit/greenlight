import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CommonNotesDialogComponent } from './common-notes-dialog.component';

describe('CommonNotesDialogComponent', () => {
  let component: CommonNotesDialogComponent;
  let fixture: ComponentFixture<CommonNotesDialogComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CommonNotesDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CommonNotesDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
