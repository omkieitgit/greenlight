import { AccountingModule } from './accounting.module';

describe('AccountingModule', () => {
  let accountingModule: AccountingModule;

  beforeEach(() => {
    accountingModule = new AccountingModule();
  });

  it('should create an instance', () => {
    expect(accountingModule).toBeTruthy();
  });
});
