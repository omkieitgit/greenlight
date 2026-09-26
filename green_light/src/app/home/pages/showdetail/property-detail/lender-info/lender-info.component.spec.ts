import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LenderInfoComponent } from './lender-info.component';

describe('LenderInfoComponent', () => {
  let component: LenderInfoComponent;
  let fixture: ComponentFixture<LenderInfoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ LenderInfoComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(LenderInfoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
