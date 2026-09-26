import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { NonHudComponent } from './non-hud.component';

describe('NonHudComponent', () => {
  let component: NonHudComponent;
  let fixture: ComponentFixture<NonHudComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ NonHudComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NonHudComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
