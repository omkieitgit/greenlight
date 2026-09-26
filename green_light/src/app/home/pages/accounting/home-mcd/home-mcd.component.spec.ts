import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { HomeMcdComponent } from './home-mcd.component';

describe('HomeMcdComponent', () => {
  let component: HomeMcdComponent;
  let fixture: ComponentFixture<HomeMcdComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ HomeMcdComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HomeMcdComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
