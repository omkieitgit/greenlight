import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { EsGuideComponent } from './es-guide.component';

describe('EsGuideComponent', () => {
  let component: EsGuideComponent;
  let fixture: ComponentFixture<EsGuideComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ EsGuideComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EsGuideComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
