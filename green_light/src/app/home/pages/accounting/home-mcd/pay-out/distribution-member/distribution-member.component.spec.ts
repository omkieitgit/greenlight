import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { DistributionMemberComponent } from './distribution-member.component';

describe('DistributionMemberComponent', () => {
  let component: DistributionMemberComponent;
  let fixture: ComponentFixture<DistributionMemberComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ DistributionMemberComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DistributionMemberComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
