export class ShortTermRentalModel{
        id:number;
        property_number:string;
        guest_name:string;
        check_in_date:string|number;
        check_out_date:string|number;
        property_description:string;
        amount_received:any=0;
        amount_deposit:number=0;
        account_deposited:number;
        link:string;
        remark:string;
        rental_type:string;
        deposite_link:string;

}


export class bookingModel{
        id:number;
        guest_name:string;
        check_in_date:string|number;
        check_out_date:string|number;
        amount_deposit:number;
}
export class bankStatementModel{
        id:number;
        trans_desc:string;
        trans_date:string|number;
        deposit_in:string|number;
        link:string|number;
        listing:string;
        trans_type:string;  
        renter_tr:string;

}
