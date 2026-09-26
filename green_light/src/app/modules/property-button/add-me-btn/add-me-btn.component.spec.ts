import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AddMeBtnComponent } from './add-me-btn.component';

describe('AddMeBtnComponent', () => {
  let component: AddMeBtnComponent;
  let fixture: ComponentFixture<AddMeBtnComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AddMeBtnComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddMeBtnComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
