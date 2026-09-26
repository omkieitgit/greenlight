import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PropertySearchComponent } from './property-search.component';

describe('PropertySearchComponent', () => {
  let component: PropertySearchComponent;
  let fixture: ComponentFixture<PropertySearchComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PropertySearchComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PropertySearchComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
