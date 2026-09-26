import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AddMeComponent } from './add-me.component';

describe('AddMeComponent', () => {
  let component: AddMeComponent;
  let fixture: ComponentFixture<AddMeComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AddMeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddMeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
