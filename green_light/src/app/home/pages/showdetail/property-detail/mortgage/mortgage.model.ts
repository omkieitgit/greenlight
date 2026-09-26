export class MortgageModel{
    other_4: MortgateOtherPropTaxModel;
    hoa_liens: HoaLienModel;
    mortgage_liens: MortgageLienModel;
    other_liens: OtherLienModel;
    taxes: TaxModel;
    amortizationInfo:amortizationInfoModel;
    tax_liens:TaxLienModel;
    
    constructor() {
         this.other_4 = new MortgateOtherPropTaxModel();
         this.hoa_liens = new HoaLienModel();
         this.mortgage_liens = new MortgageLienModel();
         this.other_liens = new OtherLienModel();
         this.taxes = new TaxModel();
         this.amortizationInfo=new amortizationInfoModel();
         this.tax_liens=new TaxLienModel();
    }
}

export class amortizationInfoModel{
    cma_arv:number=null;
    rental_rate:number=null;
    sale_date:string=null;
    nos_data:string=null;
}

export class MortgateOtherPropTaxModel {
    county_rod_ur: string = null;
    manual_search: string = null;
    no_active_mortgage_lien: number = null;
    house_id: number = null;
}

export class OtherLienModel{
    assignment_bp: string = null;
    book_page_assignment_bp: string = null;
    date_recorded: string = null;
    lender:  string = null;
    lien_amount:  number = null;
    mortgage_other_document: string = null;
    house_id: number = null;
}
export class TaxModel{
    taxes: string = null;
}

export class HoaLienModel{
    company_not_ct_rcd: number = null;
    date_of_hoa_lien: string = null;
    dca_final_check: number = null;
    dca_second_check: number = null;
    defective_notice_hoa: number = null;
    dtc_first_check: number = null;
    hoa_lien_amount: number = null;
    hoa_lien_book_page: string = null;
    hoa_lien_foreclosing: number = null;
    hoa_name: string = null;
    house_id: number = null;
    manual_search_hoa: number = null;
    mortgage_hoa_document: string = null;
    no_str: number = null;
    prop_sign_owner_1: number = null;
    prop_sign_owner_2: number = null;
    prop_sign_owner_3: number = null;
    prop_sign_owner_4: number = null;
    str_book_page: string = null;
    str_date: string = null;
    trustee_hoa: string = null;
}

export class TaxLienModel{
    assignment_bp: string = null;
    book_page_assignment_bp: string = null;
    date_recorded: string = null;
    lender:  string = null;
    lien_amount:  number = null;
    mortgage_other_document: string = null;
    house_id: number = null;
}


export class MortgageLienModel {
    mortgage_id: number = null;
    house_id: number = null;
    lien_type: number = null;
    lien_foreclosing: number = null;
    no_str_no_appt: number = null;
    defective_lien: number = null;
    lender: string = null;
    lien_amount: number = null;
    date_recorded: string = null;
    instrument:string=null;
    dt_book_page: string = null;
    assignment_bp: string = null;
    loan_type: number = null;
    loan_term: string = null;
    maturity_date: string = null;
    right_to_cure: string = null;
    trustee_fees: number = null;
    str_book_page: string = null;
    str_date: string = null;
    trustee: string = null;
    reasonable_attorney_fees: number = null;
    estimated_a_match: number = null;
    dt_nos: number = null;
    est_late_payment_and_fees: number = null;
    est_equity: number = null;
    total_est_debt: number = null;
    amortization_calculation: number = null;
    amortization_annual_interest: number = null;
    amortization_monthly_payment: number = null;
    amortization_monthly_principal_payment: number = null;
    amortization_monthly_interest_payment: number = null;
    amortization_loan_estimate_balance: number = null;
    modification_agreement: number = null;
    modification_book_page: string = null;
    modification_date: string = null;
    modification_lien_amount: number = null;
    modification_loan_term: string = null;
    modification_maturity_date: string = null;
    modification_annual_interest: number = null;
    modification_monthly_payment: number = null;
    modification_loan_estimate_balance: number = null;
    modification_est_late_payment_and_fees: number = null;
    subordination_agreement: number = null;
    sub_a_book_page: string = null;
    sub_a_date: string = null;
    sub_lien_position: number = null;
    property_owner_1: number = null;
    property_owner_2: number = null;
    property_owner_3: number = null;
    property_owner_4: number = null;
    company_not_current: number = null;
    dtc_first_check: number = null;
    dca_second_check: number = null;
    dca_final_check: number = null;
}