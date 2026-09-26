import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { RenovationCategoryComponent } from './renovation-category.component';

describe('RenovationCategoryComponent', () => {
  let component: RenovationCategoryComponent;
  let fixture: ComponentFixture<RenovationCategoryComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ RenovationCategoryComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RenovationCategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
