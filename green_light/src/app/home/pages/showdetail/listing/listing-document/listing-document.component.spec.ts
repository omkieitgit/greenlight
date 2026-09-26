import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListingDocumentComponent } from './listing-document.component';

describe('ListingDocumentComponent', () => {
  let component: ListingDocumentComponent;
  let fixture: ComponentFixture<ListingDocumentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListingDocumentComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ListingDocumentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
