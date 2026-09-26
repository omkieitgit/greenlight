import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { OwnerBorrowComponent } from './owner-borrow.component';

describe('OwnerBorrowComponent', () => {
  let component: OwnerBorrowComponent;
  let fixture: ComponentFixture<OwnerBorrowComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ OwnerBorrowComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(OwnerBorrowComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
