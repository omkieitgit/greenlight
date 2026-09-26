import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SubtoPropertyComponent } from './subto-property.component';

describe('SubtoPropertyComponent', () => {
  let component: SubtoPropertyComponent;
  let fixture: ComponentFixture<SubtoPropertyComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SubtoPropertyComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SubtoPropertyComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
