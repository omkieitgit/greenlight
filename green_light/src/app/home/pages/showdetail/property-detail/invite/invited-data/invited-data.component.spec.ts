import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { InvitedDataComponent } from './invited-data.component';

describe('InvitedDataComponent', () => {
  let component: InvitedDataComponent;
  let fixture: ComponentFixture<InvitedDataComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ InvitedDataComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InvitedDataComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
