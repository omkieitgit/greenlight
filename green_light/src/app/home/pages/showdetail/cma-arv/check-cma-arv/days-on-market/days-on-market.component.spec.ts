import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { DaysOnMarketComponent } from './days-on-market.component';

describe('DaysOnMarketComponent', () => {
  let component: DaysOnMarketComponent;
  let fixture: ComponentFixture<DaysOnMarketComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ DaysOnMarketComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DaysOnMarketComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
