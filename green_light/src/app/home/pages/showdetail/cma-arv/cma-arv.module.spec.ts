import { CmaArvModule } from './cma-arv.module';

describe('CmaArvModule', () => {
  let cmaArvModule: CmaArvModule;

  beforeEach(() => {
    cmaArvModule = new CmaArvModule();
  });

  it('should create an instance', () => {
    expect(cmaArvModule).toBeTruthy();
  });
});
