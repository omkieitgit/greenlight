export class TrustLedgerModel{
        id:number;
        statement_date:string;
        payee:string;
        transaction:string|number;
        description:string;
        deposit:number;
        payment:number;
        balance:number;
        doc_org_name:string;
        doc_store_name:string;
        created_at:string;
        updated_at:string;
        url:Object;
        remaningBalace:number;
}