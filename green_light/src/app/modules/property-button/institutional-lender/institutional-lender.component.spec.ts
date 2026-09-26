import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { InstitutionalLenderComponent } from './institutional-lender.component';

describe('InstitutionalLenderComponent', () => {
  let component: InstitutionalLenderComponent;
  let fixture: ComponentFixture<InstitutionalLenderComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ InstitutionalLenderComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InstitutionalLenderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
