import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { PropertyInviteSettingComponent } from './property-invite-setting.component';

describe('PropertyInviteSettingComponent', () => {
  let component: PropertyInviteSettingComponent;
  let fixture: ComponentFixture<PropertyInviteSettingComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ PropertyInviteSettingComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PropertyInviteSettingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
