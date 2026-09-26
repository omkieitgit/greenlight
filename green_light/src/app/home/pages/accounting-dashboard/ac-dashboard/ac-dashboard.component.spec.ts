import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AcDashboardComponent } from './ac-dashboard.component';

describe('AcDashboardComponent', () => {
  let component: AcDashboardComponent;
  let fixture: ComponentFixture<AcDashboardComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AcDashboardComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AcDashboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
