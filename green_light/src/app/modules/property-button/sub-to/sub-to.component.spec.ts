import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { SubToComponent } from './sub-to.component';

describe('SubToComponent', () => {
  let component: SubToComponent;
  let fixture: ComponentFixture<SubToComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ SubToComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SubToComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
