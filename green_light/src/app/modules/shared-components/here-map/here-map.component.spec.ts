import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { HereMapComponent } from './here-map.component';

describe('HereMapComponent', () => {
  let component: HereMapComponent;
  let fixture: ComponentFixture<HereMapComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ HereMapComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HereMapComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
