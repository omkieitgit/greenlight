import { ClientDocumentViewModule } from './client-document-view.module';

describe('ClientDocumentViewModule', () => {
  let clientDocumentViewModule: ClientDocumentViewModule;

  beforeEach(() => {
    clientDocumentViewModule = new ClientDocumentViewModule();
  });

  it('should create an instance', () => {
    expect(clientDocumentViewModule).toBeTruthy();
  });
});
