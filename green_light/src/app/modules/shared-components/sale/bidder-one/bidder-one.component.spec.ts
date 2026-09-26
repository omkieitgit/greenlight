import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { BidderOneComponent } from './bidder-one.component';

describe('BidderOneComponent', () => {
  let component: BidderOneComponent;
  let fixture: ComponentFixture<BidderOneComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ BidderOneComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BidderOneComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
