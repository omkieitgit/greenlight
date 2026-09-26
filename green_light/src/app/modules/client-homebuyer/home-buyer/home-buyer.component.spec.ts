import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HomeBuyerComponent } from './home-buyer.component';

describe('HomeBuyerComponent', () => {
  let component: HomeBuyerComponent;
  let fixture: ComponentFixture<HomeBuyerComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ HomeBuyerComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(HomeBuyerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
