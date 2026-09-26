import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CountyUrlComponent } from './county-url.component';

describe('CountyUrlComponent', () => {
  let component: CountyUrlComponent;
  let fixture: ComponentFixture<CountyUrlComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CountyUrlComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CountyUrlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
