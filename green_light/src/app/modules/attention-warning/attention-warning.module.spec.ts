import { AttentionWarningModule } from './attention-warning.module';

describe('AttentionWarningModule', () => {
  let attentionWarningModule: AttentionWarningModule;

  beforeEach(() => {
    attentionWarningModule = new AttentionWarningModule();
  });

  it('should create an instance', () => {
    expect(attentionWarningModule).toBeTruthy();
  });
});
