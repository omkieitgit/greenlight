import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { GetPictureComponent } from './get-picture.component';

describe('GetPictureComponent', () => {
  let component: GetPictureComponent;
  let fixture: ComponentFixture<GetPictureComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ GetPictureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GetPictureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
