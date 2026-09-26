import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { NetProfitComponent } from './net-profit.component';

describe('NetProfitComponent', () => {
  let component: NetProfitComponent;
  let fixture: ComponentFixture<NetProfitComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ NetProfitComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NetProfitComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
