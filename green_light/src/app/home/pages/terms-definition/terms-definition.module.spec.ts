import { TermsDefinitionModule } from './terms-definition.module';

describe('TermsDefinitionModule', () => {
  let termsDefinitionModule: TermsDefinitionModule;

  beforeEach(() => {
    termsDefinitionModule = new TermsDefinitionModule();
  });

  it('should create an instance', () => {
    expect(termsDefinitionModule).toBeTruthy();
  });
});
