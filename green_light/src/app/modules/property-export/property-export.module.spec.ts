import { PropertyExportModule } from './property-export.module';

describe('PropertyExportModule', () => {
  let propertyExportModule: PropertyExportModule;

  beforeEach(() => {
    propertyExportModule = new PropertyExportModule();
  });

  it('should create an instance', () => {
    expect(propertyExportModule).toBeTruthy();
  });
});
