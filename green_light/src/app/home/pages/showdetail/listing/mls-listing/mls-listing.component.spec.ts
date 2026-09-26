import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { MlsListingComponent } from './mls-listing.component';

describe('MlsListingComponent', () => {
  let component: MlsListingComponent;
  let fixture: ComponentFixture<MlsListingComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ MlsListingComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MlsListingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
