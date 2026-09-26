import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { AttentionWarningComponent } from './attention-warning.component';

describe('AttentionWarningComponent', () => {
  let component: AttentionWarningComponent;
  let fixture: ComponentFixture<AttentionWarningComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ AttentionWarningComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AttentionWarningComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
