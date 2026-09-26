import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { NonHudExpendituresComponent } from './non-hud-expenditures.component';

describe('NonHudExpendituresComponent', () => {
  let component: NonHudExpendituresComponent;
  let fixture: ComponentFixture<NonHudExpendituresComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ NonHudExpendituresComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NonHudExpendituresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
