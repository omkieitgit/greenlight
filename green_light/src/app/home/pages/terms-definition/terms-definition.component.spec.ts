import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { TermsDefinitionComponent } from './terms-definition.component';

describe('TermsDefinitionComponent', () => {
  let component: TermsDefinitionComponent;
  let fixture: ComponentFixture<TermsDefinitionComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ TermsDefinitionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TermsDefinitionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
