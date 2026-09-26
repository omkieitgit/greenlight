import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { QuickinputComponent } from './quickinput.component';

describe('QuickinputComponent', () => {
  let component: QuickinputComponent;
  let fixture: ComponentFixture<QuickinputComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ QuickinputComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(QuickinputComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
