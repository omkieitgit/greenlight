import { ComponentFixture, TestBed } from '@angular/core/testing';

import { McdUserComponent } from './mcd-user.component';

describe('McdUserComponent', () => {
  let component: McdUserComponent;
  let fixture: ComponentFixture<McdUserComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ McdUserComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(McdUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
