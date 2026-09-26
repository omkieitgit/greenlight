import { TestBed } from '@angular/core/testing';

import { AmortizationService } from './amortization.service';

describe('AmortizationService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: AmortizationService = TestBed.get(AmortizationService);
    expect(service).toBeTruthy();
  });
});
