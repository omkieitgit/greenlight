export class LocalRealStateDetaillModel {
    har_url:string = null;
    har_est:number = null;
    zillow_url:string = null;
    zestimate:number = null;
    redfin_url:string = null;
    redfin_est:number = null;
    realtor_url:string = null;
    realtor_est:number = null;
    truila_url:string = null;
    truila_est:number = null;
    trulia_est:number = null;
    beenverified_url:string = null;
}

export class PropertyDescriptionsModel{
    property_description: string = null;
    legal_description: string = null;
}

export class NotesModel{
    acquisition_manager_note: string = null;
    buyer_notes: string = null;
}

export class PropertyDocumentModel{
    case_number: string = null;
    created_by: string = null;
    created_at: string = null;
    document_date: string = null;
    document_type: string = null;
    org_name: string = null;
    url: string = null;
}

export class SchoolModel{
    elementry_school: string = null;
    middle_school: string = null;
    high_school: string = null;
}

export class InfoModel {
    ac: number = null;
    address: string = null;
    basement_area: number = 0;
    bath: number = null;
    bed: number = null;
    bonus_room: number = 0;
    building_style: number = null;
    choose_prc: string = null;
    city: string = null;
    cost_per_sqft: number = null;
    cost_sqft: number = null;
    country_assessor_url: string = null;
    county: string = null;
    enclosed_porch: string = null;
    ext_wall_type: number = null;
    finished_attic: number = 0;
    finished_basement_area: number = 0;
    fireplaces: number = null;
    full_bath: number = null;
    garage_sf: number = 0;
    garage_types: number = null;
    garages: number = null;
    gis_url: string = null;
    tax_bill_url: string = null;
    treasurer_url: string = null;
    half_bath: number = null;
    heating: number = null;
    house_id: number = null;
    lot_acreage_sf: number = null;
    main_floor_area: number = 0;
    of_families: number = null;
    of_kitchen: number = null;
    parcel_id1: string = null;
    parcel_id2: string = null;
    pool: number = null;
    prc_url: string = null;
    property_type: number = null;
    property_document_type: number = null;
    doc_other_name: string;
    prop_document_date: string = null;
    case_no: string = "";
    property_file: number = null;
    record_number: string = null;
    roofing: number = null;
    second_floor_area: number = 0;
    spa: number = null;
    specific_property_type: number = null;
    state: string = null;
    stories: number = null;
    subdivision: string = null;
    third_floor_area: number = 0;
    three_quarter_bath: number = null;
    total_living_sqft: number = 0;
    total_sqft: number = 0;
    year_built: number = 0;
    zip: number = null;
    zpid: number = null;
    county_url: string = null;

    property_descriptions: PropertyDescriptionsModel;
    local_real_estate_details: LocalRealStateDetaillModel;
    document_property: PropertyDocumentModel;
    schools_and_neighborhood: SchoolModel;
    price_history: PriceHistoryModel = new PriceHistoryModel();
    assessment: AssessmentModel = new AssessmentModel();
    tax_owed: TaxOwedModel = new TaxOwedModel();
    managerNotes:NotesModel;
    constructor() {
       this.local_real_estate_details = new LocalRealStateDetaillModel();
       this.property_descriptions = new PropertyDescriptionsModel();
       this.document_property = new PropertyDocumentModel();
       this.schools_and_neighborhood = new SchoolModel();
    }
}

export class TaxOwedModel{
    property_taxes_owed: number = null;
    taxes_assessed: number = null;
    taxes_year: number = null;
    house_id: number = null;
    id: number = null;
}


export class PriceHistoryModel{
    price_date: string = null;
    price: string = null;
    cost_per_sqft: string = null;
    source: string = null;
    description: string = "";
}

export class AssessmentModel{
   id: number = null;
   taxes_assessed_1:string = null;
   year_1:string = null;
   taxes_assessed_2:string = null;
   year_2:string = null;
   taxes_assessed_3:string = null;
   year_3:string = null;
   taxes_assessed_4:string = null;
   year_4:string = null;
   taxes_assessed_5:string = null;
   year_5:string = null;
}