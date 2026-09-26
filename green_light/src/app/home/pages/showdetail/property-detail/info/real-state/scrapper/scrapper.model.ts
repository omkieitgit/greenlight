export class PropertyDetailModel{
  PropertyInfoModel:any[];
  PriceHistory:any[];
}

export class PropertyInfoModel {
    address : String;
    state:string;
    zip:string;
    ac:String;
    heating:String;
    specific_property_type : string;
    year_built:number=0;
    bed:number=0;
    bath:number=0;
    total_living_sqft:string;
    lot_acreage_sf:string;
    stories:number=0;
    bonus_room:number=0;
    county:string;
    cost_per_sqft:string;
    garage:number=0;
    pool:string;
    city:string;
    property_type:string;
    ext_wall_type:number=0;
    garage_types:number=0;
    property_description:string;
    legal_description:string;
    price_history:PriceHistory[];
    elementary_school:string;
    middle_school:string;
    high_school:string;

    elementary_ranking:number;
    middle_ranking:number;
    high_ranking:number;

    elementary_distance:number;
    middle_distance:number;
    high_distance:number;

    half_baths:number=0;
    full_bath:number=0;
}


export class PriceHistory{
  price_date:any[];
  date:any[];
  price:any[];
  source:any[];
}

