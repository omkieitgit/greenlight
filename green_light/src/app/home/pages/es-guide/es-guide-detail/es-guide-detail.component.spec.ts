import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { EsGuideDetailComponent } from './es-guide-detail.component';

describe('EsGuideDetailComponent', () => {
  let component: EsGuideDetailComponent;
  let fixture: ComponentFixture<EsGuideDetailComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ EsGuideDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EsGuideDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
