import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomebuyerPayoutComponent } from './homebuyer-payout.component';

describe('HomebuyerPayoutComponent', () => {
  let component: HomebuyerPayoutComponent;
  let fixture: ComponentFixture<HomebuyerPayoutComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ HomebuyerPayoutComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(HomebuyerPayoutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
