import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CheckCmaArvComponent } from './check-cma-arv.component';

describe('CheckCmaArvComponent', () => {
  let component: CheckCmaArvComponent;
  let fixture: ComponentFixture<CheckCmaArvComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CheckCmaArvComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CheckCmaArvComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
