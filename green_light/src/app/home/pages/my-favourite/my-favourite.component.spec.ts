import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { MyFavouriteComponent } from './my-favourite.component';

describe('MyFavouriteComponent', () => {
  let component: MyFavouriteComponent;
  let fixture: ComponentFixture<MyFavouriteComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ MyFavouriteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MyFavouriteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
