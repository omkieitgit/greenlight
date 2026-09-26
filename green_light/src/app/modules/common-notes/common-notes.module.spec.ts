import { CommonNotesModule } from './common-notes.module';

describe('CommonNotesModule', () => {
  let commonNotesModule: CommonNotesModule;

  beforeEach(() => {
    commonNotesModule = new CommonNotesModule();
  });

  it('should create an instance', () => {
    expect(commonNotesModule).toBeTruthy();
  });
});
