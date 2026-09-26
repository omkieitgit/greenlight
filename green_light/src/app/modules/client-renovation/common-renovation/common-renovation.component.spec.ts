import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CommonRenovationComponent } from './common-renovation.component';

describe('CommonRenovationComponent', () => {
  let component: CommonRenovationComponent;
  let fixture: ComponentFixture<CommonRenovationComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CommonRenovationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CommonRenovationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
