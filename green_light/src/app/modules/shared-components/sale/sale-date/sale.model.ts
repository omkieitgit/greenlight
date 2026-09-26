// export class SaleModel{
//     sale_date:SaleDateModel[];
// }

export class SaleModel {
        sale_id: string = null;
        sale_date: string =null;
        case_number: string = "";
        opening_bid:  string = "";
        sale_type:  string = "";
        sale_status:  string = "";
        sale_place:  string = "";
        sale_time:  string = "";
        trustee: string = "";
        trustee_file_no:  string = "";
        trustee_scraped: string = "";
        trustee_url:  string = "";
        trustee_address: string = "";
        trustee_phone: string = "";
        trustee_hours: string = "";
        notice_of_foreclosure: string = "";
        legal_notice_url: string = "";
        legal_date_pulled: string = null;
        nos_name: string = "";
        nos_date: string = null;
        auction_com_url: string = "";
        auction_date_pulled: string = null;
        newspapaer_url: string = "";
        newspapaer_date_pulled: string = null;
        news_paper_nos: string = "";
        priceint: string = "";
        before_sale_trustee_notes: string = "";
        after_sale_trustee_notes : string = "";
        document_sale: any = [];
        sale_bidder: SaleBidderModel[];
}

export class SaleBidderModel{
    bidder_id: number = null;
    name_upset_bidder: string ="";
    amount_of_bid: string = "";
    bid_date: string = null;
    last_date_to_upset_bid: string = null;
    min_amt_nxt_ub: string = "";
    email: string = "";
    address: string = "";
    phone: string = "";
    date_of_sale_report: string = null;
    deposit_upset: string = "";
    name_of_mortage: string = "";
    name_of_cryer: string = "";
    bid_confirmed: number = null;
    bid_upset: number = null;
}


export class bidderInfoModel{
    attorney_name: string = "";
    attorney_address: string = "";
    attorney_city: string = "";
    attorney_zipcode: string = "";
    attorney_phone: string = "";
}