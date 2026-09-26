import { TestBed } from '@angular/core/testing';

import { HomebuyerPropertyService } from './homebuyer-property.service';

describe('HomebuyerPropertyService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: HomebuyerPropertyService = TestBed.get(HomebuyerPropertyService);
    expect(service).toBeTruthy();
  });
});
