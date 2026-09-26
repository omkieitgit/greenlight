import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TrandesmanUserComponent } from './trandesman-user.component';

describe('TrandesmanUserComponent', () => {
  let component: TrandesmanUserComponent;
  let fixture: ComponentFixture<TrandesmanUserComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ TrandesmanUserComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(TrandesmanUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
