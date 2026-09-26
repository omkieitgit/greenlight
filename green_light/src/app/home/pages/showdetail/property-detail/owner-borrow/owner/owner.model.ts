
export class OwnerModel{
    Owner:any[];
    Borrow:any[];
}


export class Owner {
    full_name: string =null;
    full_address: string = null;
    email: number = null;
    phone: number = null;
    phone1: number = null;
    deed_bp_instrument: string = null;
    deed_recorded_date: string = null;
    beenverified_url: string = null;
    id:string=null;
    house_id:number=null;
}

export class Borrow{
    full_name: string = null;
    full_address: string = null;
    email: string = null;
    phone: number = null;
    phone2: number = null;
    id:string=null;
    house_id:number=null;
}

export class PropDoc{
    property_document_type: number = null;
    property_type: number = null;
    doc_other_name: string;
    prop_document_date: string;
    case_no: string;
    property_file: number = null;
}