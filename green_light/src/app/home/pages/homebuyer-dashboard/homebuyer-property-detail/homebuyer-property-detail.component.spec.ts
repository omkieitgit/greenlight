import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { HomebuyerPropertyDetailComponent } from './homebuyer-property-detail.component';

describe('HomebuyerPropertyDetailComponent', () => {
  let component: HomebuyerPropertyDetailComponent;
  let fixture: ComponentFixture<HomebuyerPropertyDetailComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ HomebuyerPropertyDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HomebuyerPropertyDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
