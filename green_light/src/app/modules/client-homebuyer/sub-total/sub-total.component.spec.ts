import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubTotalComponent } from './sub-total.component';

describe('SubTotalComponent', () => {
  let component: SubTotalComponent;
  let fixture: ComponentFixture<SubTotalComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SubTotalComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SubTotalComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
