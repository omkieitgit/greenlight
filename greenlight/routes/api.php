<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$router->get('config/property', ['uses' => 'ConfigController@propertyConfig']);
Route::get('token',['uses' => 'AuthController@getToken']);

$router->get('health', function () use ($router) {
    return 'UP';
});
## JWT auth Controller GROUP request or GET and POST
$router->get('logout', ['uses' => 'AuthController@logout']);

$router->group(
    ['middleware' => ['jwt.auth']], function () use ($router) {
    ## Top Search API
    $router->get('tops_search', ['uses' => 'TopSearchController@index']);
    $router->get('config/county_url', ['uses' => 'ConfigController@countyUrl']);
    # User API START
    $router->get('user', ['uses' => 'UserController@profile']);
    $router->post('user', ['uses' => 'UserController@update']);
    $router->put('user', ['uses' => 'UserController@update']);
    $router->put('user/accept_agree', ['uses' => 'UserController@acceptAgree']);
    $router->post('user/change_role', ['uses' => 'UserController@changeRole']);
    $router->post('change_password', ['uses' => 'UserController@changePassword']);
    $router->put('change_password', ['uses' => 'UserController@changePassword']);

    $router->get('user/invite_settings', ['uses' => 'UserInviteSettingsController@index']);
    $router->post('user/invite_settings', ['uses' => 'UserInviteSettingsController@store']);
    $router->delete('user/invite_settings/{id}', ['uses' => 'UserInviteSettingsController@destroy']);

    $router->get('home/user/wholesaleBuyer', ['uses' => 'UserController@wholesaleBuyer']);
    $router->get('home/user/lender', ['uses' => 'UserController@lender']);

    $router->get('autopopulateuser', ['uses' => 'UserController@autopopulateuser']);

    $router->get('user/work_profile_list', ['uses' => 'UserController@getWorkProfileList']);
    $router->post('user/{uid}/work_profile', ['uses' => 'UserController@updateWorkProfileTeam']);
    $router->get('user/dtcDCANOSIMList', ['uses' => 'UserController@profileUserList']);

    # User API END

    # Showdetails: 1 Home Show Details property information
    $router->get('home/common/{house_id}', ['uses' => 'PropertyController@propertyCommonInfo']);
    $router->get('home/property/{house_id}', ['uses' => 'PropertyController@index']);
    $router->get('home/property/{house_id}/all', ['uses' => 'PropertyController@indexAll']);
    $router->put('home/property/{house_id}', ['uses' => 'PropertyController@update']);
    $router->put('home/property_single_record/{house_id}', ['uses' => 'PropertyController@updateSingleRecord']);
    $router->post('home/property', ['uses' => 'PropertyController@create']);
    $router->get('home/sub_to/{house_id}', ['uses' => 'PropertyController@getSubToInfo']);
    $router->post('home/propert_delete', ['uses' => 'PropertyController@propertySoftDeleted']);
    $router->get('home/lenderInfo/{house_id}', ['uses' => 'PropertyController@getClientPayersInfo']);
    $router->put('home/subto_property/{house_id}', ['uses' => 'CmaArvController@updateSubTo']);
    $router->post('home/subToDocument',['uses' => 'CmaArvController@uploadSubToDocument']);
    $router->put('home/update_subto/{house_id}',['uses' => 'CmaArvController@updateSubToInfo']);
    $router->get('home/getSubtoProperty/{house_id}',['uses' => 'CmaArvController@getSubToPropertyInfo']);
    $router->delete('home/subToDocument/{document_id}',['uses' => 'CmaArvController@removeSubtoDocument']);

    #Vehicle Input
     # Showdetails: 1 Home Show Details property information
    $router->get('home/common/{house_id}', ['uses' => 'PropertyController@propertyCommonInfo']);
    
    $router->get('home/vehicle', ['uses' => 'VehicleInputController@index']);
    $router->get('home/vehicleDetail/{vehicleId}', ['uses' => 'VehicleInputController@getVehicleInfo']);
    $router->put('home/vehicle/{vehicle_id}', ['uses' => 'PropertyController@update']);
    $router->post('home/vehicleSale', ['uses' => 'VehicleInputController@VehicleInputSingleUpdate']);
    $router->post('home/vehicleInfo', ['uses' => 'VehicleInputController@createUpdateVehicleInfo']);
    $router->post('home/vehicleCmaArv', ['uses' => 'VehicleInputController@createUpdateVehicleCmaArvInfo']);
    $router->post('home/vehicleNos', ['uses' => 'VehicleInputController@createUpdatevehicleNosInfo']);

    

    # Assessment API
    $router->get('home/assessment/{house_id}', ['uses' => 'PropertyController@assessment']);
    $router->post('home/assessment/', ['uses' => 'PropertyController@assessmentCreate']);
    $router->post('home/assessment_batch/', ['uses' => 'PropertyController@assessmentUpdateOrCreateAll']);
    $router->put('home/assessment/{id}', ['uses' => 'PropertyController@assessmentUpdate']);
    $router->delete('home/assessment/{id}', ['uses' => 'PropertyController@assessmentDelete']);
    $router->put('home/auto_save_assessment/{house_id}', ['uses' => 'PropertyController@updateSingleAssessmentRecord']);

    
    #
    $router->get('home/local_real/{house_id}', ['uses' => 'PropertyController@localReal']);
    $router->put('home/local_real/{house_id}', ['uses' => 'PropertyController@localRealUpdate']);
    $router->get('home/price_history/{house_id}', ['uses' => 'PropertyController@priceHistory']);
    $router->post('home/price_history/', ['uses' => 'PropertyController@priceHistoryCreate']);
    $router->put('home/price_history/{id}', ['uses' => 'PropertyController@priceHistoryUpdate']);
    $router->delete('home/price_history/{id}', ['uses' => 'PropertyController@priceHistoryDelete']);
    $router->put('home/price_history_record/{house_id}', ['uses' => 'PropertyController@updatePriceHistoryRecord']);

    
    $router->get('home/school/{house_id}', ['uses' => 'PropertyController@school']);
    $router->put('home/school/{house_id}', ['uses' => 'PropertyController@schoolUpdate']);
    # property information document upload API
    $router->post('home/property/document/', ['uses' => 'PropertyController@propertyDocumentUpload']);
    $router->delete('home/property/document/{document_id}', ['uses' => 'PropertyController@propertyDocumentDelete']);
    #$router->get('home/property/document/{house_id}', [ 'uses' => 'PropertyController@getPropertyDocuments']);
    # Showdetails:2 Owner Borrower section API
    # Get All information owner and borrower
    $router->get('home/owner_borrower/{house_id}/all', ['uses' => 'OwnerBorrowerController@all']);
    $router->get('home/owner_borrower/{house_id}', ['uses' => 'OwnerBorrowerController@all']);
    $router->put('home/owner/{house_id}/all', ['uses' => 'OwnerBorrowerController@ownerUpdateAll']);
    $router->put('home/single_record_owner/{house_id}', ['uses' => 'OwnerBorrowerController@updateOwnerSingleRecord']);
    $router->put('home/single_record_borrower/{house_id}', ['uses' => 'OwnerBorrowerController@updateBorrowSingleRecord']);
    $router->put('home/store_owner_social_media/{house_id}', ['uses' => 'OwnerBorrowerController@storeUpdateSocailSingleRecord']);
    $router->get('home/owner_social_media/{house_id}/{owner_id}', ['uses' => 'OwnerBorrowerController@ownerSocialMedia']);

    
    # Owner section API
    $router->get('home/owner/{house_id}', ['uses' => 'OwnerBorrowerController@ownerIndex']);
    $router->post('home/owner/', ['uses' => 'OwnerBorrowerController@ownerCreate']);
    $router->put('home/owner/{id}', ['uses' => 'OwnerBorrowerController@ownerUpdate']);
    $router->delete('home/owner/{owner_id}', ['uses' => 'OwnerBorrowerController@ownerDelete']);

    $router->post('home/owner_borrow_info', ['uses' => 'OwnerBorrowerController@ownerBorrowInfoCreate']);


    # Borrower section API
    $router->get('home/borrower/{house_id}', ['uses' => 'OwnerBorrowerController@borrowerIndex']);
    $router->post('home/borrower/', ['uses' => 'OwnerBorrowerController@borrowerCreate']);
    $router->put('home/borrower/{id}', ['uses' => 'OwnerBorrowerController@borrowerUpdate']);
    $router->delete('home/borrower/{borrower_id}', ['uses' => 'OwnerBorrowerController@borrowerDelete']);
    # owner information document upload API
    $router->post('home/owner/document/', ['uses' => 'OwnerBorrowerController@ownerDocumentUpload']);
    $router->delete('home/owner/document/{document_id}', ['uses' => 'OwnerBorrowerController@ownerDocumentDelete']);
    # Borrower information document upload API
    $router->post('home/borrower/document/', ['uses' => 'OwnerBorrowerController@borrowerDocumentUpload']);
    $router->delete('home/borrower/document/{document_id}', ['uses' => 'OwnerBorrowerController@borrowerDocumentDelete']);
    # Home Owner Notes API.
    $router->post('home/owner_notes/{house_id}', ['uses' => 'OwnerNotesController@create']);
    $router->get('home/owner_notes/{house_id}', ['uses' => 'OwnerNotesController@index']);
    $router->delete('home/owner_notes/{id}', ['uses' => 'OwnerNotesController@destroy']);
    $router->put('home/owner_notes/{id}', ['uses' => 'OwnerNotesController@update']);

    # Home Borrower Notes API.
    $router->post('home/borrower_notes/{house_id}', ['uses' => 'BorrowerNotesController@create']);
    $router->get('home/borrower_notes/{house_id}', ['uses' => 'BorrowerNotesController@index']);
    $router->delete('home/borrower_notes/{id}', ['uses' => 'BorrowerNotesController@destroy']);
    $router->put('home/borrower_notes/{id}', ['uses' => 'BorrowerNotesController@update']);

    # CMA ARV Recommendtion section API
    $router->get('home/cma_arv/{house_id}', ['uses' => 'CmaArvController@cmaArvIndex']);
    # Disable not needed in ShowDetails: $router->post('home/cma_arv/', [ 'uses' => 'CmaArvController@cmaArvCreate']);
    $router->put('home/cma_arv/{house_id}', ['uses' => 'CmaArvController@cmaArvUpdate']);
    $router->put('home/single_cma_arv/{house_id}', ['uses' => 'CmaArvController@cmaArvSingleUpdate']);

    $router->get('home/cma_arv/adom/{house_id}', ['uses' => 'CmaArvController@adomIndex']);
    $router->post('home/cma_arv/adom/{house_id}', ['uses' => 'CmaArvController@adomUpdateOrCreate']);
    $router->get('home/cma_arv/sqft/{house_id}', ['uses' => 'CmaArvController@sqftIndex']);
    $router->post('home/cma_arv/sqft/{house_id}', ['uses' => 'CmaArvController@sqftUpdateOrCreate']);



    # CMA/ARV Notes API.
    $router->post('home/cma_arv_notes/{house_id}', ['uses' => 'CmaArvNotesController@create']);
    $router->get('home/cma_arv_notes/{house_id}', ['uses' => 'CmaArvNotesController@index']);
    $router->delete('home/cma_arv_notes/{id}', ['uses' => 'CmaArvNotesController@destroy']);
    $router->put('home/cma_arv_notes/{id}', ['uses' => 'CmaArvNotesController@update']);

    # Map Videp Picture API
    $router->get('home/map_video/{house_id}', ['uses' => 'MapPicturesVideoController@index']);
    $router->put('home/map_video/{house_id}', ['uses' => 'MapPicturesVideoController@mapVideoUpdate']);
    #picture API
    $router->get('home/picture/{house_id}', ['uses' => 'MapPicturesVideoController@pictureIndex']);
    $router->post('home/picture/', ['uses' => 'MapPicturesVideoController@pictureAdd']);
    $router->delete('home/picture/{picture_id}', ['uses' => 'MapPicturesVideoController@pictureRemove']);
    $router->post('home/removeAllPicture/{house_id}', ['uses' => 'MapPicturesVideoController@removeSelectedImages']);
    $router->put('home/updateOrder/{picture_id}', ['uses' => 'MapPicturesVideoController@updateSingleOrderRecord']);
    
    # Sale Details
    $router->post('home/sale/', ['uses' => 'SaleDetailsController@create']);
    $router->get('home/sale/{house_id}', ['uses' => 'SaleDetailsController@indexAll']);
    $router->put('home/sale/{sale_id}', ['uses' => 'SaleDetailsController@update']);
    $router->post('home/sale/document/', ['uses' => 'SaleDetailsController@documentUpload']);
    $router->delete('home/sale/document/{document_id}', ['uses' => 'SaleDetailsController@documentDelete']);
    $router->put('home/single_sale_record/{house_id}', ['uses' => 'SaleDetailsController@createUpdateSingleSaleRecord']);
    
    #$router->get('home/bidder/{house_id}', [ 'uses' => 'SaleDetailsController@indexAll']);
    $router->post('home/bidder/', ['uses' => 'SaleDetailsController@createBidder']);
    $router->put('home/bidder/{bidder_id}', ['uses' => 'SaleDetailsController@updateBidder']);
    $router->post('home/bidder/document', ['uses' => 'SaleDetailsController@documentUploadBidder']);
    $router->delete('home/bidder/document/{document_id}', ['uses' => 'SaleDetailsController@documentDeleteBidder']);
    $router->put('home/single_bidder_record/{house_id}', ['uses' => 'SaleDetailsController@createUpdateBidderSingleRecord']);
    $router->delete('home/bidder/delete/{bidder_id}', ['uses' => 'SaleDetailsController@bidderSoftDeleted']);

    #Mortgage Other Liens & Taxes
    $router->get('home/mortgage_other/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@indexAll']);
    $router->get('home/mortgage_other/{house_id}/all', ['uses' => 'MortgageOtherLiensPropertyTaxesController@indexAll']);
    $router->put('home/mortgage_other/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@update']);
    $router->put('home/mortgage_other_info/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateSingleOtherInfo']);
    
    
    # First Lien, Second Lien , Third Lien
    $router->post('home/mortgage_other/liens', ['uses' => 'MortgageOtherLiensPropertyTaxesController@createLien']);
    $router->put('home/mortgage_other/liens/{mortgage_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateLien']);
    $router->put('home/update_mortgage/liens/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateSingleRecord']);

    
    #liens Document Upload , delete
    $router->post('home/mortgage_other/liens/document/', ['uses' => 'MortgageOtherLiensPropertyTaxesController@liensDocumentUpload']);
    $router->delete('home/mortgage_other/liens/document/{document_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@liensDocumentDelete']);
    # Other Liens information save
    $router->put('home/mortgage_other/other/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateOtherLien']);
    $router->put('home/auto_save_mortgage/other/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateOtherRecord']);
   
    #Other lien Document Upload , delete
    $router->post('home/mortgage_other/other/document/', ['uses' => 'MortgageOtherLiensPropertyTaxesController@otherDocumentUpload']);
    $router->delete('home/mortgage_other/other/document/{document_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@otherDocumentDelete']);
    # HOA Liens information save
    $router->put('home/mortgage_other/hoa/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateHoaLien']);
    $router->put('home/auto_save_mortgage/hoa/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateHoaRecord']);
    #Tax Liens
    $router->put('home/mortgage_other/tax/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateTaxLien']);
    $router->put('home/auto_save_mortgage/tax/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updateTaxRecord']);
    #HOA lien Document Upload , delete
    $router->post('home/mortgage_other/hoa/document/', ['uses' => 'MortgageOtherLiensPropertyTaxesController@hoaDocumentUpload']);
    $router->delete('home/mortgage_other/hoa/document/{document_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@hoaDocumentDelete']);
    #TAX lien Document Upload , delete
    $router->post('home/mortgage_other/tax/document/', ['uses' => 'MortgageOtherLiensPropertyTaxesController@taxDocumentUpload']);
    $router->delete('home/mortgage_other/tax/document/{document_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@taxDocumentDelete']);
    # Property Taxes  information save
    $router->put('home/mortgage_other/taxes/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@updatePropertyTaxes']);
    # Property Taxes Owed  information save
    //$router->post('home/mortgage_other/taxes/owed', [ 'uses' => 'MortgageOtherLiensPropertyTaxesController@createTaxesOwed']);
    //$router->put('home/mortgage_other/taxes/owed/{tax_id}', [ 'uses' => 'MortgageOtherLiensPropertyTaxesController@updateTaxesOwed']);
    #Property Taxes Upload , delete
    $router->post('home/mortgage_other/taxes/document/', ['uses' => 'MortgageOtherLiensPropertyTaxesController@propertyTaxesDocumentUpload']);
    $router->delete('home/mortgage_other/taxes/document/{document_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@propertyTaxesDocumentDelete']);
    #Mortage Liens Notes 1,2,3,4
    $router->post('home/mortgage_notes/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@createNotes']);
    $router->get('home/mortgage_notes/{house_id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@getAllNotes']);
    $router->get('home/mortgage_notes/{house_id}/{lien_type}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@getAllNotes']);
    $router->delete('home/mortgage_notes/{id}', ['uses' => 'MortgageOtherLiensPropertyTaxesController@destroyNotes']);

    # Wholesaler Buyer
    # Strategy
    $router->get('home/wholesaler_buyer/strategy/{house_id}', ['uses' => 'WholesaleBuyerController@index']);
    $router->get('home/wholesaler_buyer/strategy/{house_id}/all', ['uses' => 'WholesaleBuyerController@index']);
    $router->put('home/wholesaler_buyer/strategy/{house_id}', ['uses' => 'WholesaleBuyerController@strategyUpdate']);
    $router->get('home/accounting/strategy/{house_id}', ['uses' => 'WholesaleBuyerController@strategy']);
    # Emails AM
    $router->get('home/wholesaler_buyer/emails_am/{id}', ['uses' => 'WholesaleBuyerController@emailsAm']);
    $router->get('home/wholesaler_buyer/emails_am/{house_id}/all', ['uses' => 'WholesaleBuyerController@emailsAmAll']);
    $router->post('home/wholesaler_buyer/emails_am/', ['uses' => 'WholesaleBuyerController@emailsAmCreate']);
    $router->put('home/wholesaler_buyer/emails_am/{id}', ['uses' => 'WholesaleBuyerController@emailsAmUpdate']);
    $router->delete('home/wholesaler_buyer/emails_am/{id}', ['uses' => 'WholesaleBuyerController@emailsAmDelete']);
    # Emails Company Team Member
    $router->get('home/wholesaler_buyer/emails_company_team_members/{id}', ['uses' => 'WholesaleBuyerController@emailsCompanyTeamMember']);
    $router->get('home/wholesaler_buyer/emails_company_team_members/{house_id}/all', ['uses' => 'WholesaleBuyerController@emailsCompanyTeamMemberAll']);
    $router->post('home/wholesaler_buyer/emails_company_team_members/', ['uses' => 'WholesaleBuyerController@emailsCompanyTeamMemberCreate']);
    $router->put('home/wholesaler_buyer/emails_company_team_members/{id}', ['uses' => 'WholesaleBuyerController@emailsCompanyTeamMemberUpdate']);
    $router->delete('home/wholesaler_buyer/emails_company_team_members/{id}', ['uses' => 'WholesaleBuyerController@emailsCompanyTeamMemberDelete']);
    # Emails Funder & Lender
    $router->get('home/wholesaler_buyer/emails_funder_lender/{id}', ['uses' => 'WholesaleBuyerController@emailsFunderLender']);
    $router->get('home/wholesaler_buyer/emails_funder_lender/{house_id}/all', ['uses' => 'WholesaleBuyerController@emailsFunderLenderAll']);
    $router->post('home/wholesaler_buyer/emails_funder_lender/', ['uses' => 'WholesaleBuyerController@emailsFunderLenderCreate']);
    $router->put('home/wholesaler_buyer/emails_funder_lender/{id}', ['uses' => 'WholesaleBuyerController@emailsFunderLenderUpdate']);
    $router->delete('home/wholesaler_buyer/emails_funder_lender/{id}', ['uses' => 'WholesaleBuyerController@emailsFunderLenderDelete']);
    # Emails Funder & Lender
    $router->get('home/wholesaler_buyer/emails_time_left_notice/{id}', ['uses' => 'WholesaleBuyerController@emailsTimeLeftNotice']);
    $router->get('home/wholesaler_buyer/emails_time_left_notice/{house_id}/all', ['uses' => 'WholesaleBuyerController@emailsTimeLeftNoticeAll']);
    $router->post('home/wholesaler_buyer/emails_time_left_notice/', ['uses' => 'WholesaleBuyerController@emailsTimeLeftNoticeCreate']);
    $router->put('home/wholesaler_buyer/emails_time_left_notice/{id}', ['uses' => 'WholesaleBuyerController@emailsTimeLeftNoticeUpdate']);
    $router->delete('home/wholesaler_buyer/emails_time_left_notice/{id}', ['uses' => 'WholesaleBuyerController@emailsTimeLeftNoticeDelete']);
    # Wholesale STHB N entry
    $router->get('home/wholesaler_buyer/wholesale_buyer_n/{id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerN']);
    $router->get('home/wholesaler_buyer/wholesale_buyer_n/{house_id}/all', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNAll']);
    $router->post('home/wholesaler_buyer/wholesale_buyer_n/', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNCreate']);
    $router->put('home/wholesaler_buyer/wholesale_buyer_n/{id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNUpdate']);
    $router->delete('home/wholesaler_buyer/wholesale_buyer_n/{id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNDelete']);
    # or use this URL
    $router->get('home/sthb/{house_id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerN']);
    $router->post('home/sthb/', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNCreate']);
    $router->put('home/sthb/{id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNUpdate']);
    $router->delete('home/sthb/{id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNDelete']);
    #Wholesale  N entry or STHB total here
    $router->get('home/wholesaler_buyer/wholesale_buyer_n_total/{house_id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNTotal']);
    $router->put('home/wholesaler_buyer/wholesale_buyer_n_total/{house_id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNTotalUpdate']);
    # OR
    $router->get('home/sthb_total/{house_id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNTotal']);
    $router->put('home/sthb_total/{house_id}', ['uses' => 'WholesaleBuyerController@wholesaleBuyerNTotalUpdate']);
    # Trustee
    $router->get('home/trustee/{house_id}', ['uses' => 'TrusteeController@index']);
    $router->put('home/trustee/{house_id}', ['uses' => 'TrusteeController@updateOrCreate']);
    # Deposit wired
    $router->get('home/deposit_wired/{house_id}/all', ['uses' => 'DepositWiredController@all']);
    $router->get('home/deposit_wired/{id}', ['uses' => 'DepositWiredController@index']);
    $router->post('home/deposit_wired/', ['uses' => 'DepositWiredControlle    r@create']);
    $router->put('home/deposit_wired/{id}', ['uses' => 'DepositWiredController@update']);
    $router->delete('home/deposit_wired/{id}', ['uses' => 'DepositWiredController@delete']);
    # Additional Cost Wired
    $router->get('home/additional_cost_wired/{house_id}/all', ['uses' => 'AdditionalCostWiredController@all']);
    $router->get('home/additional_cost_wired/{id}', ['uses' => 'AdditionalCostWiredController@index']);
    $router->post('home/additional_cost_wired/', ['uses' => 'AdditionalCostWiredController@create']);
    $router->put('home/additional_cost_wired/{id}', ['uses' => 'AdditionalCostWiredController@update']);
    $router->delete('home/additional_cost_wired/{id}', ['uses' => 'AdditionalCostWiredController@delete']);
    # Amount Wired to Close
    $router->get('home/amount_wire_to_close/{house_id}/all', ['uses' => 'AmountWiredToCloseController@all']);
    $router->get('home/amount_wire_to_close/{id}', ['uses' => 'AmountWiredToCloseController@index']);
    $router->post('home/amount_wire_to_close/', ['uses' => 'AmountWiredToCloseController@create']);
    $router->put('home/amount_wire_to_close/{id}', ['uses' => 'AmountWiredToCloseController@update']);
    $router->delete('home/amount_wire_to_close/{id}', ['uses' => 'AmountWiredToCloseController@delete']);
    # Property Acquisition A to B section API
    $router->get('home/property_acquisition_ab/{house_id}', ['uses' => 'PropertyAcquisitionAtoBController@index']);
    $router->put('home/property_acquisition_ab/{house_id}', ['uses' => 'PropertyAcquisitionAtoBController@updateOrCreate']);
    # Property Acquisition B to C section API
    $router->get('home/property_acquisition_bc/{house_id}', ['uses' => 'PropertyAcquisitionBtoCController@index']);
    $router->put('home/property_acquisition_bc/{house_id}', ['uses' => 'PropertyAcquisitionBtoCController@updateOrCreate']);
    $router->post('home/manager_notes/{house_id}', ['uses' => 'ManagerNotesController@create']);
    $router->get('home/manager_notes/{type}/{house_id}', ['uses' => 'ManagerNotesController@index']);
    $router->delete('home/manager_notes/{id}', ['uses' => 'ManagerNotesController@destroy']);
    $router->put('home/manager_notes/{id}', ['uses' => 'ManagerNotesController@update']);

    #Verify below API's are show details or not
    $router->get('home/accounting/{house_id}/all', ['uses' => 'ClientController@index']);
    $router->post('home/client/document', ['uses' => 'ClientController@documentUpload']);
    $router->delete('home/client/document/{document_id}', ['uses' => 'ClientController@documentDelete']);
    $router->get('home/client/document/{house_id}', ['uses' => 'ClientController@clienDocumentGet']);
    $router->post('home/accounting/document/', ['uses' => 'AccountingDocumentController@documentUpload']);
    $router->delete('home/accounting/document/{document_id}', ['uses' => 'AccountingDocumentController@documentDelete']);
    $router->get('home/accounting/document/{house_id}', ['uses' => 'AccountingDocumentController@index']);
    $router->post('home/client/master_closing_document', ['uses' => 'ClientController@masterClosingDoc']);
    $router->delete('home/client/master_closing_document/{document_id}', ['uses' => 'ClientController@masterClosingDocDelete']);
    $router->get('home/client/master_closing_document/{house_id}', ['uses' => 'ClientController@clientMasterClosingdocGet']);
    
    #Client Renovation
    $router->post('home/client/renovation', ['uses' => 'ClientController@clientRenovationCreate']);
    $router->delete('home/client/renovation/{id}', ['uses' => 'ClientController@clientRenovationDelete']);
    $router->get('home/client/renovation/{house_id}/{section_type}/{orderBy}', ['uses' => 'ClientController@clientRenovation']);
    $router->post('home/client/renovationUpdate', ['uses' => 'ClientController@clientRenovationUpdate']);
    $router->get('home/client/renovationCategory', ['uses' => 'ClientController@getRenovationCategory']);
    $router->post('home/client/renovationCategory', ['uses' => 'ClientController@saveRenovationCategory']);
    $router->delete('home/client/renovationCategory/{id}', ['uses' => 'ClientController@removeRenovationCategory']);
    $router->get('home/client/renovation_lender/{house_id}/{section_type}', ['uses' => 'ClientController@clientRenovationLender']);
    $router->post('home/client/invoiceHistory', ['uses' => 'ClientController@updateInvoiceHistory']);
    $router->post('/home/client/exportClientRenovation', ['uses' => 'ClientController@exportClientRenovation']);

    #Non hub
    $router->get('home/client/non_hub/{house_id}', ['uses' => 'ClientController@getClientNonHubExpenditures']);
    $router->get('home/accounting/rci_costs/{house_id}', ['uses' => 'RICCostsController@index']);

    # Client Master
    $router->post('home/client/master', ['uses' => 'ClientController@clientMasterCreate']);
    $router->put('home/client/master/{house_id}', ['uses' => 'ClientController@clientMasterUpdate']);
    $router->get('home/client/master/{house_id}', ['uses' => 'ClientController@getClientMaster']);
    
    #1099
    $router->post('home/form1099Misc', ['uses' => 'Form1099MiscController@form1099MiscCreate']);
    $router->get('home/form1099Misc', ['uses' => 'Form1099MiscController@index']);
    $router->put('home/form1099Misc/{house_id}', ['uses' => 'Form1099MiscController@form1099MiscUpdate']);
    $router->get('home/recipient', ['uses' => 'Form1099MiscController@getRecipient']);
    $router->get('home/payers', ['uses' => 'Form1099MiscController@getPayers']);
    $router->get('home/1099MscForm/{house_id}', ['uses' => 'Form1099MiscController@getMscForm']);
    $router->post('home/msc1099FormPdf', ['uses' => 'Form1099MiscController@msc1099FormPdf']);

    #w9
    $router->post('home/w9', ['uses' => 'W9Controller@w9Create']);
    $router->get('home/w9/{user_id}', ['uses' => 'W9Controller@w9Index']);
    $router->put('home/w9/{house_id}', ['uses' => 'W9Controller@W9InfoUpdate']);
    $router->get('home/tradesmanW9/{user_id}', ['uses' => 'W9Controller@tradesmanW9']);
    $router->post('home/tradesmanW9', ['uses' => 'W9Controller@postTradesmanW9']);
    $router->post('home/sendW9Email/{house_id}', ['uses' => 'W9Controller@postsendW9Email']);
    $router->get('home/viewSsn/{user_id}', ['uses' => 'W9Controller@viewSsNumber']);
    # Non Hud
    $router->post('home/non_hud_expenditures', ['uses' => 'NonHudExpenditureController@nonHudExpendituresCreate']);
    $router->put('home/non_hud_expenditures/{document_id}', ['uses' => 'NonHudExpenditureController@nonHudExpendituresUpdate']);
    $router->get('home/non_hud_expenditures/{house_id}', ['uses' => 'NonHudExpenditureController@index']);
    $router->delete('home/non_hud_expenditures/{document_id}', ['uses' => 'NonHudExpenditureController@nonHudExpendituresDelete']);
    $router->put('home/update_non_hud_expenditures/{house_id}', ['uses' => 'NonHudExpenditureController@updateNonHudExpenditures']);
    $router->post('home/client/information', ['uses' => 'ClientController@clientInformationCreate']);
    $router->get('home/client/information/{house_id}', ['uses' => 'ClientController@clientInformationGet']);
    $router->delete('home/client/member/{id}', ['uses' => 'ClientController@removeClientMember']);
    $router->delete('home/client/manager/{id}', ['uses' => 'ClientController@removeClientManager']);
    $router->delete('home/client/manager/{id}', ['uses' => 'ClientController@removeClientManager']);
    

    /*House Payout */
    $router->put('home/payout/{payout_id}', ['uses' => 'PayoutController@payoutUpdate']);
    $router->get('home/payout/{house_id}', ['uses' => 'PayoutController@payOutGet']);
    $router->delete('home/payout/{id}', ['uses' => 'PayoutController@removePayout']);
    $router->delete('home/payout/the_estates_title/{id}', ['uses' => 'PayoutController@removeTheestatesTitle']);
    
    $router->put('home/payout', ['uses' => 'PayoutController@updatePayoutRecord']);
    $router->get('home/payout/{house_id}', ['uses' => 'PayoutController@index']);
    $router->get('home/payout_detail/{house_id}', ['uses' => 'PayoutController@getPayoutDetail']);
    $router->post('home/payout_detail', ['uses' => 'PayoutController@payoutCreate']);
    $router->put('home/payoutCategory', ['uses' => 'PayoutController@updatePayoutCategory']);
    $router->get('home/payoutCat', ['uses' => 'PayoutController@getCategory']);
    $router->put('home/updatePayoutDetail', ['uses' => 'PayoutController@updateHomeBuyerRecord']);

    #Reschedule
    $router->get('home/reSchedule', ['uses' => 'McdController@getReschedule']);


    $router->post('home/client/wholesaleBuyer', ['uses' => 'ClientController@saveWholesaleBuyer']);
    $router->post('home/client/lender', ['uses' => 'ClientController@saveLender']);
    $router->get('home/client/wholesaleBuyer/{house_id}', ['uses' => 'ClientController@wholesaleBuyer']);
    $router->get('home/client/lender/{house_id}', ['uses' => 'ClientController@lender']);
    $router->post('home/client/renovation_budget', ['uses' => 'ClientRenovationBudgetController@createClientRenovationBudget']);
    $router->get('home/client/renovation_budget/{house_id}', ['uses' => 'ClientRenovationBudgetController@index']);
    #Client Bidding Funds
    # Static route should be first then dynamic one
    $router->post('home/client_bidding_funds/document', ['uses' => 'ClientBiddingFundsController@documentUpload']);
    $router->delete('home/client_bidding_funds/document/{document_id}', ['uses' => 'ClientBiddingFundsController@documentDelete']);
    $router->get('home/client_bidding_funds/document/{house_id}', ['uses' => 'ClientBiddingFundsController@document']);
    $router->post('home/client_bidding_funds/{house_id}', ['uses' => 'ClientBiddingFundsController@updateOrCreate']);
    $router->get('home/client_bidding_funds/{house_id}', ['uses' => 'ClientBiddingFundsController@index']);
    #Renovation Cost, Incidental costs, Carry costs
    $router->post('home/accounting/incidental_costs', ['uses' => 'RICCostsController@updateOrCreateIncidental']);
    $router->delete('home/accounting/incidental_costs/{id}', ['uses' => 'RICCostsController@deleteIncidental']);
    $router->post('home/accounting/carry_costs', ['uses' => 'RICCostsController@updateOrCreateCarry']);
    $router->delete('home/accounting/carry_costs/{id}', ['uses' => 'RICCostsController@deleteCarry']);
    $router->post('home/accounting/renovation_costs', ['uses' => 'RICCostsController@updateOrCreateRenovation']);
    $router->delete('home/accounting/renovation_costs/{id}', ['uses' => 'RICCostsController@deleteRenovation']);

    ## Scraper Call
    $router->post('home/scraper', ['uses' => 'ScraperAWSController@index']);

    # Show Details API end.

    # Search API
    $router->get('search', ['uses' => 'SearchController@index']);
    $router->get('context', ['uses' => 'SearchController@indexHouseId']);
    # Search API
    $router->get('search', ['uses' => 'SearchController@index']);
    $router->get('advance', ['uses' => 'SearchController@advance']);
    # buy it and pass it
    $router->get('buyit', ['uses' => 'HouseBuyItController@buyitList']);
    $router->post('buyit/{house_id}', ['uses' => 'HouseBuyItController@updateOrCreate']);
    $router->put('buyit/{house_id}', ['uses' => 'HouseBuyItController@updateOrCreate']);
    $router->get('passit', ['uses' => 'HouseBuyItController@passitList']);
    $router->post('passit/{house_id}', ['uses' => 'HouseBuyItController@updateOrCreatePassIt']);
    $router->post('contactRequst', ['uses' => 'HouseBuyItController@contactRequest']);

    # Invite API
    $router->post('invite/{house_id}', ['uses' => 'InviteController@updateOrCreate']);
    $router->delete('invite/{invite_id}', ['uses' => 'InviteController@deleteInvite']);
    $router->delete('removeInviteAll/{invite_id}', ['uses' => 'InviteController@deleteAllInvite']);
    $router->get('invite/list/{house_id}', ['uses' => 'InviteController@list']);
    $router->get('invite/all/{house_id}', ['uses' => 'InviteController@all']);
    #Check is property invite or not
    $router->post('is_invited/{house_id}', ['uses' => 'InviteController@isInvited']);

    #HOA Invite
    $router->post('hoa_invite/{house_id}', ['uses' => 'InviteController@hoaInvite']);
    $router->get('hoa_invite_list/{house_id}', ['uses' => 'InviteController@hoaInviteList']);


    # Home Buyer dashboard
    $router->get('home_buyer_properties/count', ['uses' => 'HomeBuyerDashboardController@dashboardCount2']);
    $router->get('home_buyer_properties/count2', ['uses' => 'HomeBuyerDashboardController@dashboardCount']);
    $router->get('home_buyer_properties/list', ['uses' => 'HomeBuyerDashboardController@list2']);
    $router->get('home_buyer_properties/list2', ['uses' => 'HomeBuyerDashboardController@list']);
    # User Favourite API
    $router->post('favourite/add/{house_id}', ['uses' => 'UserFavoritesController@updateOrCreate']);
    $router->post('favourite/remove/{house_id}', ['uses' => 'UserFavoritesController@delete']);
    $router->get('favourite/all', ['uses' => 'UserFavoritesController@all']);
    # Property Queue API
    $router->get('property_queue', ['uses' => 'PropertyQueueListController@myList']);
    $router->post('property_queue', ['uses' => 'PropertyQueueListController@create']);
    $router->put('property_queue/{list_id}', ['uses' => 'PropertyQueueListController@update']);
    $router->delete('property_queue/{list_id}', ['uses' => 'PropertyQueueListController@delete']);
    # Property Queue Houeses API
    $router->get('property_queue_houses', ['uses' => 'PropertyQueueListHousesController@index']);
    $router->get('property_queue_houses/{list_id}', ['uses' => 'PropertyQueueListHousesController@list']);
    $router->post('property_queue_houses/add/0', ['uses' => 'PropertyQueueListHousesController@updateOrCreate']);
    $router->put('property_queue_houses/move_to_list', ['uses' => 'PropertyQueueListHousesController@moveToList']);
    $router->post('property_queue_houses/add/{list_id}', ['uses' => 'PropertyQueueListHousesController@updateOrCreate']);
    $router->delete('property_queue_houses/remove/{property_queue_list_houses_id}', ['uses' => 'PropertyQueueListHousesController@delete']);

    # Private QuickView URL
    $router->get('home/quickview/{token_or_house_id}', ['uses' => 'QuickViewController@private']);

    $router->post('common/lender_it/{house_id}', ['uses' => 'CommonController@lenderIt']);
    $router->get('common/deposit_executive_lender/{house_id}', ['uses' => 'CommonController@GetdepositExecutiveLender']);
    $router->post('common/deposit_executive_lender/{house_id}', ['uses' => 'CommonController@depositExecutiveLender']);
    $router->post('common/wholesale_notes/{house_id}', ['uses' => 'WholesaleNotesController@create']);
    $router->get('common/wholesale_notes/{house_id}', ['uses' => 'WholesaleNotesController@index']);
    $router->get('common/renovation/{house_id}', ['uses' => 'CommonController@renovationPaymentRequest']);
    $router->post('common/renovation/{house_id}', ['uses' => 'CommonController@renovationPaymentProcess']);
    $router->post('common/title_search/{house_id}', ['uses' => 'CommonController@titleSearchProcess']);
    $router->post('common/insurance_quote/{house_id}', ['uses' => 'CommonController@insuranceQuoteProcess']);
    $router->get('common/picture_payment_token/', ['uses' => 'CommonController@picturePaymentRequest']);
    $router->post('common/picture/', ['uses' => 'CommonController@picturePaymentProcess']);
    $router->get('common/area_invite_data/', ['uses' => 'CommonController@areaInviteData']);
    $router->post('common/area_invite/{house_id}', ['uses' => 'CommonController@areaInvite']);

    # Email, Export ..
    $router->post('common/email/', ['uses' => 'CommonController@email']);
    $router->post('common/email', ['uses' => 'CommonController@email']);
    $router->post('common/print/', ['uses' => 'CommonController@printD']);
    $router->post('common/print', ['uses' => 'CommonController@printD']);
    $router->post('common/export/', ['uses' => 'CommonController@export']);
    $router->post('common/export', ['uses' => 'CommonController@export']);
    $router->post('common/wholesale_retail', ['uses' => 'CommonController@wholesaleRetail']);
    $router->get('common/wholesale_retail', ['uses' => 'CommonController@wholesaleRetail']);
    $router->post('buyitExport', ['uses' => 'HouseBuyItController@exportBuyIt']);
    # Below endpoints for alarm Me API .
    $router->get('common/alarm_me/', ['uses' => 'CommonController@alarmMeList']);
    $router->post('common/alarm_me/{house_id}', ['uses' => 'CommonController@alarmMe']);
    $router->delete('common/alarm_me/{alarm_id}', ['uses' => 'CommonController@alarmMeDelete']);
    $router->get('common/alarmMe', ['uses' => 'CommonController@alarmMeListDetail']);
    $router->put('common/updateAlarmMe', ['uses' => 'CommonController@updateAlarmMe']);

    # Common notes for property :  only for user to add on property
    $router->get('common/note/{house_id}', ['uses' => 'CommonController@noteList']);
    $router->get('common/notes/{house_id}', ['uses' => 'CommonController@noteList']);
    $router->post('common/note/{house_id}', ['uses' => 'CommonController@noteCreate']);
    $router->post('common/notes/{house_id}', ['uses' => 'CommonController@noteCreate']);
    $router->delete('common/note/{id}', ['uses' => 'CommonController@noteDelete']);
    $router->delete('common/notes/{id}', ['uses' => 'CommonController@noteDelete']);

    # Common notes for property :  WIP
    $router->get('notes/{house_id}/{note_type}', ['uses' => 'CommonNotesController@index']);
    $router->post('notes/{house_id}', ['uses' => 'CommonNotesController@store']);
    $router->delete('notes/{id}', ['uses' => 'CommonNotesController@destroy']);
    $router->put('notes/{id}', ['uses' => 'CommonNotesController@update']);

    # Or use below end points both are same
    $router->get('alarm_me/', ['uses' => 'CommonController@alarmMeList']);
    $router->post('alarm_me/{house_id}', ['uses' => 'CommonController@alarmMe']);
    $router->delete('alarm_me/{alarm_id}', ['uses' => 'CommonController@alarmMeDelete']);
    $router->post('geo/{house_id}', ['uses' => 'GeoController@updateOrCreate']);
    $router->get('geo/{house_id}', ['uses' => 'GeoController@index']);

    # Report Section , No buyer for this report.
    $router->get('report', ['uses' => 'ReportController@index']);
    $router->get('report/exportMe', ['uses' => 'ReportController@exportMe']);
    $router->get('report/view_more', ['uses' => 'ReportController@viewMore']);
    $router->get('report/aa-report', ['uses' => 'ReportController@getAaReport']);
    $router->get('report/aa_county_report', ['uses' => 'ReportController@getCountyReport']);
    $router->get('report/aa_buyer_report', ['uses' => 'ReportController@getTotalBuyItbyBuyer']);
    $router->get('report/aa_sale_report', ['uses' => 'ReportController@getTotalBuyItbySaleType']);
    $router->get('report/aa_buyer_home_report', ['uses' => 'ReportController@getBuyItbyBuyerId']);
    $router->get('report/sale_type_aa_report', ['uses' => 'ReportController@getSaleTypeDetailAaReport']);
    $router->post('report/export_sale_type_aa_report', ['uses' => 'ReportController@exportSaleTypeDetailAaReport']);
    $router->post('report/export_buyit_aa_report', ['uses' => 'ReportController@exportBuyitAaReport']);
    $router->get('report/countyReport', ['uses' => 'ReportController@getCountyByReport']);
    $router->post('report/exportyCountyReport', ['uses' => 'ReportController@exportyCountyReport']);
    $router->get('report/monthly-report', ['uses' => 'ReportController@monthlyReport']);
    $router->get('report/export-monthly-report', ['uses' => 'ReportController@exportMonthlyReport']);

    //Es Guide
    $router->get('es_guide', ['uses' => 'CommonController@getEsGuide']);

    //MCD 
    $router->get('mcd/{house_id}', ['uses' => 'McdController@index']);
    $router->post('mcd/{house_id}', ['uses' => 'McdController@updateOrCreate']);
    $router->delete('mcd/{id}', ['uses' => 'McdController@destroy']);

    $router->get('mcd_lender/{house_id}', ['uses' => 'McdController@getMcdLender']);
    $router->post('mcd_lender/{house_id}', ['uses' => 'McdController@updateOrCreateMcdLender']);
    $router->delete('mcd_lender/{id}', ['uses' => 'McdController@destroyMcdLender']);
    $router->get('mcdLenderDetail/{house_id}', ['uses' => 'McdController@getMcdLenderDetails']);
    $router->post('mcd_single_lender/{house_id}', ['uses' => 'McdController@updateMcdLenderSingleRecord']);
    $router->post('mcd_other_record/{house_id}', ['uses' => 'McdController@updateMcdOtherRecord']);
    $router->post('mcd_user', ['uses' => 'McdController@addMcdUser']);
    $router->get('mcd_user/{house_id}', ['uses' => 'McdController@getMcdUser']);
    $router->delete('mcd_user/{id}', ['uses' => 'McdController@destroyMcdUser']);
    $router->get('is_mcd_access/{house_id}', ['uses' => 'McdController@isMcdAccess']);


    $router->get('tradesmanTracking/{house_id}', ['uses' => 'McdController@getTradesmanTracking']);
    $router->post('tradesmanTracking/{house_id}', ['uses' => 'McdController@updateOrCreateTradesmanTracking']);
    $router->delete('tradesmanTracking/{id}', ['uses' => 'McdController@destroyTradesmanTracking']);
    $router->post('tradesmanUser', ['uses' => 'McdController@updateOrCreateTradesmanUser']);

    $router->get('shortTermRental/{house_id}', ['uses' => 'McdController@getShortTermRentalInfo']);
    $router->post('shortTermRental/{house_id}', ['uses' => 'McdController@updateCreateShortTermRental']);
    $router->delete('shortTermRental/{id}', ['uses' => 'McdController@destroyShortTermRental']);
  
    
    

    $router->post('lenderStatement/{house_id}', ['uses' => 'McdController@updateOrCreateLenderStatement']);
    $router->get('lenderStatement/{house_id}', ['uses' => 'McdController@getLenderStatement']);
    $router->delete('lenderStatement/{id}', ['uses' => 'McdController@destroyLenderStatement']);

    $router->post('home/additionalField/{house_id}', ['uses' => 'PayoutController@createUpdateAdditionalField']);
    $router->delete('home/additionalField/{id}', ['uses' => 'PayoutController@destroyAdditionalField']);


    $router->post('bankStatement/{house_id}', ['uses' => 'McdController@updateCreateBankStatement']);
    $router->get('bankStatement/{house_id}', ['uses' => 'McdController@getBankStatement']);
    $router->delete('bankStatement/{id}', ['uses' => 'McdController@destroyBankStatement']);
    
    //Deposit Spread sheet API
    $router->post('depositSpreadSheet/{house_id}', ['uses' => 'McdController@updateCreateDepositSpreadSheet']);
    $router->get('depositSpreadSheet/{house_id}', ['uses' => 'McdController@getDepositSpreadSheet']);
    $router->delete('depositSpreadSheet/{id}', ['uses' => 'McdController@destroyDepositSpreadSheet']);
    $router->delete('removeDepositAccount/{id}', ['uses' => 'McdController@removeDepositAccount']);
    $router->delete('removeDepositLink/{id}', ['uses' => 'McdController@removeDepositLink']);
    $router->get('getAllDepositSheet', ['uses' => 'McdController@getAllDepositSheets']);
    $router->get('getLenderDepositSheet', ['uses' => 'McdController@getLenderDepositSheet']);

    $router->post('acTimeTracking/{house_id}', ['uses' => 'TimeTrackingController@updateOrCreate']);
    $router->get('acTimeTracking/{house_id}', ['uses' => 'TimeTrackingController@index']);
    $router->delete('acTimeTracking/{id}', ['uses' => 'TimeTrackingController@destroy']);
    
    $router->post('burnRate/{house_id}', ['uses' => 'ClientController@updateCreateBurnRate']);
    $router->get('burnRate/{house_id}', ['uses' => 'ClientController@getBurnRate']);
    $router->delete('burnRate/{id}', ['uses' => 'ClientController@destroyBurnRate']);
    $router->put('burnRateRecord/{house_id}', ['uses' => 'ClientController@burnRateRecord']);
    $router->put('updateActualBurnRate/{house_id}', ['uses' => 'ClientController@updateActualBurnRate']);

    $router->post('depositLender', ['uses' => 'McdController@updateCreateDepositLender']);
    $router->put('payoutLender/{house_id}', ['uses' => 'PayoutController@createUpdatePayoutLender']);
    
    $router->get('homeBuyerRenoCategory/{house_id}', ['uses' => 'ClientController@getRenoCategory']);
    
    $router->post('home/mergeProperty', ['uses' => 'PropertyController@mergeProperty']);
    $router->post('home/mergeRecords', ['uses' => 'PropertyController@mergeRecords']);

    #mailing
    $router->get('mailing/{house_id}', ['uses' => 'ClientController@getMailing']);
    $router->post('mailing', ['uses' => 'ClientController@postMailing']);
    $router->delete('mailing/{id}', ['uses' => 'ClientController@destroyMailing']);
    $router->get('sendEmail/{house_id}', ['uses' => 'ClientController@sendMailToUser']);

    #listing Tab
    $router->post('home/listing/document', ['uses' => 'ListingController@postDocument']);
    $router->delete('home/listing/document/{document_id}', ['uses' => 'ListingController@deleteDocument']);
    $router->get('home/listing/document/{house_id}', ['uses' => 'ListingController@getDocument']);
  
    //    $router->post('passit/{house_id}',[ 'uses' => 'HouseBuyItController@updateOrCreatePassIt']);
    //    $router->put('passit/{house_id}',[ 'uses' => 'HouseBuyItController@updateOrCreatePassIt']);
    
    $router->post('home/drive-list/{house_id}', ['uses' => 'DriveListController@storeDriveList']);
    $router->post('home/drive-list-detail', ['uses' => 'DriveListController@storeDriveListDetail']);
    $router->get('home/drive-list/{house_id}', ['uses' => 'DriveListController@getDriveList']);
    $router->delete('home/remove-drive-list-detail/{drive_list_detail_id}', ['uses' => 'DriveListController@removeDriveListDetail']);
    
    $router->post('home/add/drive-list', ['uses' => 'DriveListController@updateOrCreateUserDriveList']);
    $router->delete('home/remove/drive-list/{house_id}', ['uses' => 'DriveListController@delete']);
    $router->get("home/all/drive-list", ['uses' => 'DriveListController@getAllUserDriveList']);
    $router->get('generate-docx', 'DriveListController@exportDriveList');
    $router->get('home/drive-list-info/{house_id}', ['uses' => 'DriveListController@getPropertyDriveList']);

    
}
);

