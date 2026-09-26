import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { MoreFieldComponent } from './more-field.component';

describe('MoreFieldComponent', () => {
  let component: MoreFieldComponent;
  let fixture: ComponentFixture<MoreFieldComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ MoreFieldComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MoreFieldComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
