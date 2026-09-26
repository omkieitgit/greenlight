import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PropertyQueueComponent } from './property-queue.component';

describe('PropertyQueueComponent', () => {
  let component: PropertyQueueComponent;
  let fixture: ComponentFixture<PropertyQueueComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PropertyQueueComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PropertyQueueComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
