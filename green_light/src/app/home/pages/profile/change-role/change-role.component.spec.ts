import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ChangeRoleComponent } from './change-role.component';

describe('ChangeRoleComponent', () => {
  let component: ChangeRoleComponent;
  let fixture: ComponentFixture<ChangeRoleComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ChangeRoleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ChangeRoleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
