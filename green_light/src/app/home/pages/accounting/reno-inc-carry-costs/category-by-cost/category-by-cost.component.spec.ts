import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CategoryByCostComponent } from './category-by-cost.component';

describe('CategoryByCostComponent', () => {
  let component: CategoryByCostComponent;
  let fixture: ComponentFixture<CategoryByCostComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CategoryByCostComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(CategoryByCostComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
