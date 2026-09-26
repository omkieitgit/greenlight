import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { BuyItListComponent } from './buy-it-list.component';

describe('BuyItListComponent', () => {
  let component: BuyItListComponent;
  let fixture: ComponentFixture<BuyItListComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ BuyItListComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BuyItListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
