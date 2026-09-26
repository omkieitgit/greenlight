import { Injectable } from '@angular/core';
import { Observable, Subject } from 'rxjs';

@Injectable()

export class CommunicationService {

    private expand = new Subject<boolean>();
    private userInfo = new Subject<any>();
    private dataInfo = new Subject<any>(); 
    private checkedManualSearch = new Subject<any>(); 
    private checkedNoActiveLien= new Subject<any>(); 
    private scrapperInfo= new Subject<any>(); 
    private ownerInfo = new Subject<any>(); 
    private mortgage_notes = new Subject<any>(); 
    private publishRole = new Subject<any>(); 
    private publishFinalCheckDca = new Subject<any>(); 
    private renovation_detail=new Subject<any>();
    private homeBuyerPropertyType=new Subject<any>();
    private updateRenovation=new Subject<boolean>();

    private set_rod_url = new Subject<any>();
    private updateSaleId = new Subject<any>();
    private googleMapUrl = new Subject<string>();

    private netProfit = new Subject<number>();
    private payoutBuydate = new Subject<any>();
    private acTimeTracking = new Subject<any>();
    private payoutTotal = new Subject<any>();
    private fundsPriorClosing = new Subject<number>();
    private add1099User = new Subject<number>();

    expandAll(flag: boolean) {
        this.expand.next(flag);
    }

    clearExpand() {
        this.expand.next();
    }

    getExpand(): Observable<any> {
        return this.expand.asObservable();
    }

    user_info(user: any) {
        this.userInfo.next(user);
    }

    getUser(): Observable<any> {
        return this.userInfo.asObservable();
    }

    sendData(data:any){
        this.dataInfo.next(data);
    }
    getData():Observable<any>{
        return this.dataInfo.asObservable();
    }

    setManualSearch(data:any){
        this.checkedManualSearch.next(data);
    }

    getManualSearch():Observable<any>{
        return this.checkedManualSearch.asObservable();
    }

    setNoActiveLien(data:any){
        this.checkedNoActiveLien.next(data);
    }

    getNoActiveLien():Observable<any>{
        return this.checkedNoActiveLien.asObservable();
    }

    setScrapperData(data:any){
        this.scrapperInfo.next(data);
    }

    getScrapperData():Observable<any>{
        return this.scrapperInfo.asObservable();
    }

    sendOwnerInfo(data:any){
        this.ownerInfo.next(data);
    }
    getOwnerInfo():Observable<any>{
        return this.ownerInfo.asObservable();
    }

    sendMortgageNotes(data:any){
        this.mortgage_notes.next(data);
    }
    getMortgageNotes():Observable<any>{
        return this.mortgage_notes.asObservable();
    }

    publishRoleChange(data:any){
        this.publishRole.next(data);
    }

    getChangedRole(){
        return this.publishRole.asObservable();
    }

    publishFinalCheck(data:any){
        this.publishFinalCheckDca.next(data);
    }

    getFinalCheckDCA(){
        return this.publishFinalCheckDca.asObservable();
    }

    publishRenovationDetail(data:any){
        this.renovation_detail.next(data);
    }

    getRenovationDetail(){
        return this.renovation_detail.asObservable();
    }

    setHomeBuyerPropetyType(data:any){
        this.homeBuyerPropertyType.next(data);
    }

    getHomeBuyerPropetyType(){
        return this.homeBuyerPropertyType.asObservable();
    }

    setUpdateRenovation(data:any){
        this.updateRenovation.next(data);
    }

    getUpdateRenovation(){
        return this.updateRenovation.asObservable();
    }

    setRodUrl(data){
        this.set_rod_url.next(data);
    }
    getRodUrl(): Observable<any> {
        return this.set_rod_url.asObservable();
    }

    setSaleId(data){
        this.updateSaleId.next(data);
    }

    getSaleId():Observable<any>{
        return this.updateSaleId.asObservable();
    }

    setGoogleMapUrlUpdate(data){
        this.googleMapUrl.next(data);
    }
    getGoogleMapUrl(){
        return this.googleMapUrl.asObservable();
    }

    setNetProfit(data){
        this.netProfit.next(data);
    }
    getNetProfit(){
        return this.netProfit.asObservable();
    }

    setPayoutInfo(data){
        this.payoutBuydate.next(data);
    }
    getPayoutInfo(){
        return this.payoutBuydate.asObservable();
    }

    setAcTimeTracking(data){
        this.acTimeTracking.next(data);
    }
    getAcTimeTracking(){
        return this.acTimeTracking.asObservable();
    }

    setPayoutTotal(data){
        this.payoutTotal.next(data);
    }
    getPayoutTotal(){
        return this.payoutTotal.asObservable();
    }

    setFundsPriorClosing(data){
        this.fundsPriorClosing.next(data);
    }

    getFundsPriorClosing(){
        return this.fundsPriorClosing.asObservable();
    }
    
    setAdd1099User(data){
        this.add1099User.next(data);
    }

    getAdd1099User(){
        return this.add1099User.asObservable();
    }
}