import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateMcdUserComponent } from './create-mcd-user.component';

describe('CreateMcdUserComponent', () => {
  let component: CreateMcdUserComponent;
  let fixture: ComponentFixture<CreateMcdUserComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CreateMcdUserComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(CreateMcdUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
