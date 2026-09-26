import { ShowdetailMenuModule } from './showdetail-menu.module';

describe('ShowdetailMenuModule', () => {
  let showdetailMenuModule: ShowdetailMenuModule;

  beforeEach(() => {
    showdetailMenuModule = new ShowdetailMenuModule();
  });

  it('should create an instance', () => {
    expect(showdetailMenuModule).toBeTruthy();
  });
});
