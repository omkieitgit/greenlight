import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { WholesaleRetailDialogComponent } from './wholesale-retail-dialog.component';

describe('WholesaleRetailDialogComponent', () => {
  let component: WholesaleRetailDialogComponent;
  let fixture: ComponentFixture<WholesaleRetailDialogComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ WholesaleRetailDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(WholesaleRetailDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
