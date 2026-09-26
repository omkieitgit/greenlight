import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DepositSheetComponent } from './deposit-sheet.component';

describe('DepositSheetComponent', () => {
  let component: DepositSheetComponent;
  let fixture: ComponentFixture<DepositSheetComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DepositSheetComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(DepositSheetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
