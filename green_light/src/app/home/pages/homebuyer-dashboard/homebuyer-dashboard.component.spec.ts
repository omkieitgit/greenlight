import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { HomebuyerDashboardComponent } from './homebuyer-dashboard.component';

describe('HomebuyerDashboardComponent', () => {
  let component: HomebuyerDashboardComponent;
  let fixture: ComponentFixture<HomebuyerDashboardComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ HomebuyerDashboardComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HomebuyerDashboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
