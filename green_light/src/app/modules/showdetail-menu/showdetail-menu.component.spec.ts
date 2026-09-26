import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ShowdetailMenuComponent } from './showdetail-menu.component';

describe('ShowdetailMenuComponent', () => {
  let component: ShowdetailMenuComponent;
  let fixture: ComponentFixture<ShowdetailMenuComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ShowdetailMenuComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowdetailMenuComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
