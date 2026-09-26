import { PropertyButtonModule } from './property-button.module';

describe('PropertyButtonModule', () => {
  let propertyButtonModule: PropertyButtonModule;

  beforeEach(() => {
    propertyButtonModule = new PropertyButtonModule();
  });

  it('should create an instance', () => {
    expect(propertyButtonModule).toBeTruthy();
  });
});
