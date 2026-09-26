import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CommonNotesComponent } from './common-notes.component';

describe('CommonNotesComponent', () => {
  let component: CommonNotesComponent;
  let fixture: ComponentFixture<CommonNotesComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CommonNotesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CommonNotesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
