import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { LienTaxComponent } from './lien-tax.component';

describe('LienTaxComponent', () => {
  let component: LienTaxComponent;
  let fixture: ComponentFixture<LienTaxComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ LienTaxComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LienTaxComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
