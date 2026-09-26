import { ComponentFixture, TestBed } from '@angular/core/testing';

import { McdOtherInfoComponent } from './mcd-other-info.component';

describe('McdOtherInfoComponent', () => {
  let component: McdOtherInfoComponent;
  let fixture: ComponentFixture<McdOtherInfoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ McdOtherInfoComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(McdOtherInfoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
