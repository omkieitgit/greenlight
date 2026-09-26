import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MscFormListComponent } from './msc-form-list.component';

describe('MscFormListComponent', () => {
  let component: MscFormListComponent;
  let fixture: ComponentFixture<MscFormListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MscFormListComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(MscFormListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
