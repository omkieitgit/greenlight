import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomeBuyerCategoryComponent } from './home-buyer-category.component';

describe('HomeBuyerCategoryComponent', () => {
  let component: HomeBuyerCategoryComponent;
  let fixture: ComponentFixture<HomeBuyerCategoryComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ HomeBuyerCategoryComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(HomeBuyerCategoryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
