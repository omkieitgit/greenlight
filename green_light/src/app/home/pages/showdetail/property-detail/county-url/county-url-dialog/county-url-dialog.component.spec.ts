import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CountyUrlDialogComponent } from './county-url-dialog.component';

describe('CountyUrlDialogComponent', () => {
  let component: CountyUrlDialogComponent;
  let fixture: ComponentFixture<CountyUrlDialogComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CountyUrlDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CountyUrlDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
