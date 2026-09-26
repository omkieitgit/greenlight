import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ShortTermRentalComponent } from './short-term-rental.component';

describe('ShortTermRentalComponent', () => {
  let component: ShortTermRentalComponent;
  let fixture: ComponentFixture<ShortTermRentalComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ShortTermRentalComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ShortTermRentalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
