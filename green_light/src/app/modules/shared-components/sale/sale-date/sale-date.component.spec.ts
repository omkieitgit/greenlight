import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { SaleDateComponent } from './sale-date.component';

describe('SaleDateComponent', () => {
  let component: SaleDateComponent;
  let fixture: ComponentFixture<SaleDateComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ SaleDateComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SaleDateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
