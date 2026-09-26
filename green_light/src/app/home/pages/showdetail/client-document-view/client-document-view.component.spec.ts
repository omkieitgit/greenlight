import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ClientDocumentViewComponent } from './client-document-view.component';

describe('ClientDocumentViewComponent', () => {
  let component: ClientDocumentViewComponent;
  let fixture: ComponentFixture<ClientDocumentViewComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ClientDocumentViewComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClientDocumentViewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
