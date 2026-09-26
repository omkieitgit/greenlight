import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { SearchDetailComponent } from './search-detail.component';

describe('SearchDetailComponent', () => {
  let component: SearchDetailComponent;
  let fixture: ComponentFixture<SearchDetailComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ SearchDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SearchDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
