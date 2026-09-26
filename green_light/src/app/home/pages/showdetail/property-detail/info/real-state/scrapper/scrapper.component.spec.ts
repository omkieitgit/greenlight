import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ScrapperComponent } from './scrapper.component';

describe('ScrapperComponent', () => {
  let component: ScrapperComponent;
  let fixture: ComponentFixture<ScrapperComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ScrapperComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ScrapperComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
