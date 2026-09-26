import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { WholesaleRetailComponent } from './wholesale-retail.component';

describe('WholesaleRetailComponent', () => {
  let component: WholesaleRetailComponent;
  let fixture: ComponentFixture<WholesaleRetailComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ WholesaleRetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(WholesaleRetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
