import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ClientMscFormComponent } from './client-msc-form.component';

describe('ClientMscFormComponent', () => {
  let component: ClientMscFormComponent;
  let fixture: ComponentFixture<ClientMscFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ClientMscFormComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(ClientMscFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
