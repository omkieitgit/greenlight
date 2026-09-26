import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { CmaArvDetailComponent } from './cma-arv-detail.component';

describe('CmaArvDetailComponent', () => {
  let component: CmaArvDetailComponent;
  let fixture: ComponentFixture<CmaArvDetailComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ CmaArvDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CmaArvDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
