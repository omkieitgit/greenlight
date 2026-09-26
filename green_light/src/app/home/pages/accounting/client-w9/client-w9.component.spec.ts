import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { ClientW9Component } from './client-w9.component';

describe('ClientW9Component', () => {
  let component: ClientW9Component;
  let fixture: ComponentFixture<ClientW9Component>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ ClientW9Component ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClientW9Component);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
