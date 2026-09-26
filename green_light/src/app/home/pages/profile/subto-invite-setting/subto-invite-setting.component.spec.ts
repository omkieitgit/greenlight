import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubtoInviteSettingComponent } from './subto-invite-setting.component';

describe('SubtoInviteSettingComponent', () => {
  let component: SubtoInviteSettingComponent;
  let fixture: ComponentFixture<SubtoInviteSettingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SubtoInviteSettingComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SubtoInviteSettingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
