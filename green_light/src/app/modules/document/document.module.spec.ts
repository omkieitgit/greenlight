import { DocumentModule } from './document.module';

describe('DocumentModule', () => {
  let documentModule: DocumentModule;

  beforeEach(() => {
    documentModule = new DocumentModule();
  });

  it('should create an instance', () => {
    expect(documentModule).toBeTruthy();
  });
});
