'use strict';
import {environment} from '../../environments/environment'

export const base_url = environment.api_url; 
export const scrapper_base_url = environment.scraper_url; 

export const apiUrl = {
		
		/** Property Detail Module */
		"token":base_url+"token",
		/** Property Tab API */
		"property_info":base_url+"home/property",
		"property_document":base_url+"home/property/document",
		'property_assessment': base_url+"home/assessment",
		'price_history': base_url+"home/price_history",
		'save_prop_real_state': base_url+"home/local_real/",
		'update_school': base_url+"home/school",
		'home_common':base_url+'home/common',
		"auto_save_property":base_url+"home/property_single_record",
		"auto_price_history":base_url+"home/price_history_record",
		'auto_save_assessment': base_url+"home/auto_save_assessment",
		'sub_to':base_url+"home/sub_to",
		'delete_property':base_url+'home/propert_delete',
		'lender_info':base_url+'home/lenderInfo',
		'subto_document': base_url+'home/subToDocument',
		'update_subto': base_url+'home/update_subto',
		'get_sub_to': base_url+'home/getSubtoProperty',


		'county_url':base_url+'config/county_url',
		'favourite':base_url+'favourite/',
		'subto_property':base_url+'home/subto_property',

		/** scraper Tab API */
		'update_scrapper_data':	base_url+'home/scrapper/',
		'scraper':base_url+'home/scraper',

		/** Picture Tab API */
		"map_video":base_url+"home/map_video",
		"picture":base_url+"home/picture",
		"removeAllPicture":base_url+"home/removeAllPicture",
		'geo':base_url+'geo',

		/** Owner and Borrower Tab API */
		'owner':base_url+"home/owner",
		'owner_document':base_url+"home/owner/document",
		"borrower":base_url+"home/borrower",
		"borrower_document":base_url+"home/borrower/document",
		'owner_borrow_info':base_url+"home/owner_borrow_info",
		'owner_borrow_all_info':base_url+'home/owner_borrower',
		'single_record_owner':base_url+'home/single_record_owner',
		'single_record_borrower':base_url+'home/single_record_borrower',

		/** Sale Tab API */
		"sale_detail":base_url+"home/sale",
		"bidder_detail":base_url+"home/bidder",
		"bidder_document":base_url+"home/bidder/document",
		"sale_document":base_url+"home/sale/document",
		"save_single_sale_record":base_url+"home/single_sale_record",
		"save_single_bidder_record":base_url+"home/single_bidder_record",
		"remove_bidder":base_url+"home/bidder/delete",
		
		/** Mortgage Tab API */
		'mortgage':base_url+"home/mortgage_other",
		'mortgage_lien':base_url+'home/mortgage_other/liens',
		'mortgate_tax': base_url+'home/mortgage_other/taxes',
		'mortgage_lien_document': base_url+'home/mortgage_other/liens/document',
		'mortgage_hoa_document': base_url+'home/mortgage_other/hoa/document',
		'mortgage_other_document': base_url+'home/mortgage_other/other/document',
		'mortgage_tax_document': base_url+'home/mortgage_other/tax/document',
		'mortgage_notes':base_url+'home/mortgage_notes/',
		'auto_save_mortgage_lien_record':base_url+'home/update_mortgage/liens',
		'auto_save_mortgage_record':base_url+'home/auto_save_mortgage',

		/** CMA-ARV Module APIs */
		"cma_arv":base_url+"home/cma_arv/",
		'emails_am':base_url+'home/wholesaler_buyer/emails_am',
		'cma_arv_adom':base_url+'home/cma_arv/adom',
		'cma_arv_single':base_url+'home/single_cma_arv',
		'cma_arv_sqft':base_url+'home/cma_arv/sqft',

		/* Wholesale Buyer tab API*/
		'wholesale_buyer_n':base_url+'home/wholesaler_buyer/wholesale_buyer_n',
		'sthb_total':base_url+'home/sthb_total',

		/** Invite API */
		'send_invite':base_url+'invite',
		'delete_invite':base_url+'invite',
		'delete_all_invite':base_url+'removeInviteAll',
		'invite_list':base_url+'invite/list/',
		'invite_list_all':base_url+'invite/all/',
		'send_hoa_invite':base_url+'hoa_invite/',
		'hoa_invite_list':base_url+'hoa_invite_list/',
		/** bottom button API */
		'buyit':base_url+'buyit',
		'save_buy_it':base_url+'buyit',
		'save_pass_on':base_url+'passit',
		'lender_it':base_url+'common/lender_it',
		'deposit_executive_lender':base_url+'common/deposit_executive_lender',
		'insurance_quote':base_url+'common/insurance_quote',
		'title_search':base_url+'common/title_search',
		'save_area_invite':base_url+'common/area_invite',
		'get_area_invite':base_url+'common/area_invite_data',
		'get_picture':base_url+'common/picture',
		'get_picture_payment':base_url+'common/picture_payment_token',
		'contactRequst':base_url+'contactRequst',
		/** Property Queue APIs*/
		'property_queue':base_url+'property_queue',
		'add_property_queue':base_url+'property_queue_houses/add/',
		'queue_list':base_url+'property_queue_houses',
		'remove_property_queue_house':base_url+'property_queue_houses/remove/',
		'remove_property_queue':base_url+'property_queue/',

		/** Search Page API*/
		'search':base_url+'search',
		'advance':base_url+'advance',
		'export':base_url+'common/',
		'buyitexport':base_url+'buyitExport',
		/** logged in user API */
		"auth_login":base_url+"auth/login",  
		"user_detail":base_url+"user",
		"forgot_password":base_url+"forgot_password",
		"contact_us":base_url+"contact_us",
		"register":base_url+"register",
		"register_wholesale_buyer":base_url+"register/buyer",
		'profile':base_url+'user',
		'change_password':base_url+'change_password',
		'change_role':base_url+'user/change_role',
		"register_buyer":base_url+"register/buyer",
		"register_const":base_url+"config/member_register",
		"property_config":base_url+"config/property",
		'reset_password':base_url+'reset_password',
		'user_list':base_url+'autopopulateuser',
		'accept_agree':base_url+'user/accept_agree',
		'work_profile':base_url+'user/work_profile_list',
		'work_profile_user_list':base_url+'user/dtcDCANOSIMList',
		

		/** Alarm Tab API */
		'alarm_me':base_url+'common/alarm_me/',
		'alarm':base_url+'alarm_me',
		'alarmMe':base_url+'common/alarmMe',
		'updateAlarmMe':base_url+'common/updateAlarmMe',
		
		
		/** Client Info */
		'client_sthb_total':base_url+'home/sthb_total',

		/** Client Master tab*/
		'client_master_doc':base_url+'home/client/master_closing_document',
		'accounting_strategy':base_url+'home/accounting/strategy',
		'client_mcd':base_url+'home/client/master/',

		/* Client Document */
		"client_document":base_url+"home/client/document",

		/* Accounting Document */
		"accounting_document":base_url+"home/accounting/document",
		
		/* W9 */
		'client_w9':base_url+"home/w9",
		'tradesmanW9':base_url+"home/tradesmanW9",
		'sendW9Email':base_url+'home/sendW9Email',
		'viewSsn':base_url+"home/viewSsn",
		/* client Renovation Budget */
		'renovation_budget':base_url+'home/client/renovation_budget',

		/* client Renovation */
		"client_renovation":base_url+"home/client/renovation",
		"client_renovation_lender":base_url+"home/client/renovation_lender",
		"client_renovation_update":base_url+"home/client/renovationUpdate",
		"client_renovation_document_delete":base_url+"home/client/renovation/",
		"client_renovation_detail":base_url+'home/client/renovation/detail',
		'renovationCategory':base_url+'home/client/renovationCategory',
		'invoiceHistory':base_url+'home/client/invoiceHistory',
		'export_renovation':base_url+'home/client/exportClientRenovation',
		
		/* misc 1099 */
		'misc_1099':base_url+'home/form1099Misc',
		'recipient':base_url+'home/recipient',
		'payers':base_url+'home/payers',
		'mscForm':base_url+'home/1099MscForm',
		'msc1099FormPdf':base_url+'home/msc1099FormPdf',
		
		/*Re-schedule*/
		'reSchedule':base_url+'home/reSchedule',
		
		/** property acquisition */
		'property_acquisition_ab':base_url+'home/property_acquisition_ab', //remove
		'property_acquisition_bc':base_url+'home/property_acquisition_bc', //remove

		/** Client Bidding Funds */
		'client_bidding_funds':base_url+'home/client_bidding_funds', //remove
		
		'homebuyer_strategy_all':base_url+'home/wholesaler_buyer/strategy',
		'homebuyer_strategy_save':base_url+'home/wholesaler_buyer/strategy',

		'homebuyer_email_time_left_save':base_url+'home/wholesaler_buyer/emails_time_left_notice',
		'homebuyer_email_time_left_update':base_url+'home/wholesaler_buyer/emails_time_left_notice',
		'homebuyer_email_time_left_delete':base_url+'home/wholesaler_buyer/emails_time_left_notice/',
		'homebuyer_company_team_save':base_url+'home/wholesaler_buyer/emails_company_team_members',
		'homebuyer_company_team_update':base_url+'home/wholesaler_buyer/emails_company_team_members',
		'homebuyer_company_team_delete':base_url+'home/wholesaler_buyer/emails_company_team_members/',
		'homebuyer_funder_lender_save':base_url+'home/wholesaler_buyer/emails_funder_lender',
		'homebuyer_funder_lender_update':base_url+'home/wholesaler_buyer/emails_funder_lender',
		'homebuyer_funder_lender_delete':base_url+'home/wholesaler_buyer/emails_funder_lender/',
	


		'homebuyer_trustee_update':base_url+'home/trustee/',
		'homebuyer_deposit_wired_save':base_url+'home/deposit_wired',
		'homebuyer_deposit_wired_update':base_url+'home/deposit_wired/',
		'homebuyer_deposit_wired_delete':base_url+'home/deposit_wired/',
		'homebuyer_additional_cost_wired_save':base_url+'home/additional_cost_wired',
		'homebuyer_additional_cost_wired_update':base_url+'home/additional_cost_wired/',
		'homebuyer_additional_cost_wired_delete':base_url+'home/additional_cost_wired/',
		'homebuyer_amount_wire_to_close_save':base_url+'home/amount_wire_to_close',
		'homebuyer_amount_wire_to_close_update':base_url+'home/amount_wire_to_close/',
		'homebuyer_amount_wire_to_close_delete':base_url+'home/amount_wire_to_close/',
		


		"client_closing_doc_upload":base_url+"home/client/master_closing_document",
		"client_closing_doc_delete":base_url+"home/client/master_closing_document/",
		//"get_accounting_details":base_url+"home/accounting",
	
		/**Non Hub */
		'inovice_non_hud':base_url+'home/client/non_hub',
		
		
		//Not Needed
		'non_hud':base_url+'home/non_hud_expenditures',
		'update_non_hud':base_url+'home/update_non_hud_expenditures/',


		'client_info':base_url+'home/client/information',
		'client_member_delete':base_url+'home/client/member/',
		'client_manager_delete':base_url+'home/client/manager/',

		//payout
		'payout':base_url+'home/payout',
		'payout_detail':base_url+'home/payout_detail',
		'updatePayoutDetail':base_url+'home/updatePayoutDetail',
		'get_payout_details':base_url+'home/payout/',
		'payoutCat':base_url+'home/payoutCat',
		'additionalField':base_url+'home/additionalField',
		'payoutCategory':base_url+'home/payoutCategory',
		
		'wholesale_buyer':base_url+'home/user/wholesaleBuyer', //Remove
		'lender':base_url+'home/user/lender',//Remove
		'account_wholesale':base_url+'home/client/wholesaleBuyer', //Remove
		'account_lender':base_url+'home/client/lender',//Remove

		//Home buyer Dashboard
		'wholesale_buyer_list':base_url+'home_buyer_properties/list',
		'wholesale_buyer_count':base_url+'home_buyer_properties/count',
		
	
		'quick_view':base_url+'quickview/',
		'private_quick_view':base_url+'home/quickview/',
		'braintree_client_token':base_url+'braintree',


		//Renovation Cost, Incidental costs, Carry costs
		"rci_costs":base_url+"home/accounting/rci_costs/",
		'carry_cost':base_url+"home/accounting/carry_costs",
		"incidental_cost" :base_url+"home/accounting/incidental_costs",
		"renovation_costs":base_url+"home/accounting/renovation_costs",
		
		//property Search API
		'propertyAutoComplete':base_url+'tops_search',

		//invite property API
		'invite_setting':base_url+'user/invite_settings',

		//static Page API
		"privacy_policy":base_url+'info/privacy_policy_html',
		"return_policy":base_url+'info/return_policy_html',
		"toc":base_url+'info/terms_and_condition_html',
		'is_agree':base_url+'info/is_agree_html',
		'is_popup':base_url+'info/is_popup_html',

		//Report 
		'report':base_url+'report',
		'exportMe':base_url+'report/exportMe',
		'viewMore':base_url+'report/view_more',
		'aa-report':base_url+'report/aa-report',
		'aa_county_report':base_url+'report/aa_county_report',
		'aa_buyer_report':base_url+'report/aa_buyer_report',
		'aa_sale_report':base_url+'report/aa_sale_report',
		'aa_report_by_buyer_id':base_url+'report/aa_buyer_home_report',
		'aa_report_by_sale_type':base_url+'report/sale_type_aa_report',
		'export_sale_type_aa_report':base_url+'report/export_sale_type_aa_report',
		'export_buyit_aa_report':base_url+'report/export_buyit_aa_report',


		'county_by_report':base_url+'report/countyReport',
		'export_county_by_report':base_url+'report/exportyCountyReport',
		//guide 
		'es_guide':base_url+'es_guide',

		// is intevited 
		'is_invited':base_url+'is_invited',

		//mcd
		'mcd':base_url+'mcd',
		'mcd_lender':base_url+'mcd_lender',
		'mcd_single_lender':base_url+'mcd_single_lender',
		'mcd_lender_detail':base_url+'mcdLenderDetail',
		'lender_statement':base_url+'lenderStatement',
		'tradesmanTracking':base_url+'tradesmanTracking',
		'tradesmanUser':base_url+'tradesmanUser',
		'shortTermRental':base_url+'shortTermRental',
		'bankStatement':base_url+'bankStatement',
		'depositSpreadSheet':base_url+'depositSpreadSheet',
		'acTimeTracking':base_url+'acTimeTracking',
		'burnRate':base_url+'burnRate',
		'depositLender':base_url+'depositLender',
		'removeDepositAccount':base_url+'removeDepositAccount',
		'removeDepositLink':base_url+'removeDepositLink',
		'burnRateRecord':base_url+'burnRateRecord',
		'updateActualBurnRate':base_url+'updateActualBurnRate',
		'payoutLender':base_url+'payoutLender',
		'mcd_other_info':base_url+'mcd_other_record',
		'mcd_user':base_url+"mcd_user",
		'isMcdAccess':base_url+"is_mcd_access",
		//Home Buyer
		'homeBuyerRenoCategory':base_url+'homeBuyerRenoCategory',

		//deposit all list
		'allDepositSpreadSheet':base_url+'getAllDepositSheet',
		'getLenderDepositSheet':base_url+'getLenderDepositSheet',
		'mergeProperty':base_url+'home/mergeProperty',
		'mergeRecords':base_url+'home/mergeRecords',

		//Mailing
		'mailing':base_url+'mailing',
		'sendEmail':base_url+'sendEmail',
		/* Listing Tab */
		"listing_document":base_url+"home/listing/document",

		/*Vehicle*/
		'vehicle':base_url+'home/vehicle',
		'vehicleDetail':base_url+'home/vehicleDetail',
		'vehicleSale':base_url+'home/vehicleSale',
		'saveUpdateVehicleInfo':base_url+'home/vehicleInfo',
		'vehicleCmaArv':base_url+'home/vehicleCmaArv',
		'vehicleNos':base_url+'home/vehicleNos'
}	

export const scraperApiUrl = {
	'trulia':'scraper/truliaScript',
	'zillow':'scraper/zillowScript',
	'har':'scraper/harScript',
	'redfin':'scraper/redfinScript',
	'realtor':'scraper/realtorScript',
	'gui':'scraper/EstatelyScript',
	'movoto':'scraper/MovotoScript'
};

export const fixedUrl={
	'beenverified_url':'https://www.beenverified.com/reverse-address-lookup/',
	'beenverified_owner_url':'https://www.beenverified.com/people/',
	'pacer_url':'https://pacer.login.uscourts.gov/csologin/login.jsf'
}

export const map_api={
	'api_id':'gAnmNM7t13dEKgp2y30x',
	'api_code':'6TP6iRTxsGN-bp6v9Vpp4A',
	'map_api_url':'https://autocomplete.search.hereapi.com/v1/autocomplete',
	'get_geocode':'https://geocode.search.hereapi.com/v1/geocode',
	'api_key' : "YtIQXdLz9Zb_st2YD0iGcUoa6O51kc91S_ZGnXfoQB4",
	//'site_key' : '6LePAk8UAAAAADfk-4qnOrzKmPTaEyRZL5DlPzDh',
	
	'herewego_common_key':[
		{"app_id":"Iw54ixdke01QyO0vACQE","app_code":"yPH2ycpV_L0RHjBs6_ekSw"},
		{"app_id":"THf7ha5KVQW7D82hacCK","app_code":"ybAZIuT1upGYvyKr9iFiZQ"},
		{"app_id":"j8yTZCruuuqgwyBskoKL","app_code":"7Zcob17LmNfmCgKOoMxneQ"}, 
	]
}


export const commonNotes ={
	"manager":{'title': 'Acquisition Manager','url': base_url+'home/manager_notes','note_type': '1','btn_title':'Add Notes','is_seprate':false},
	"buyer":{'title': 'Buyer/AM/SubTo','url': base_url+'home/manager_notes','note_type': '2','btn_title':'Add Notes','is_seprate':false},
	"owner":{'title': 'Owner','url': base_url+'home/owner_notes','note_type': '','btn_title':'Add Notes','is_seprate':false},
	"borrower":{'title': 'Borrower','url': base_url+'home/borrower_notes','note_type': '','btn_title':'Add Notes','is_seprate':false},
	"cma_arv":{'title': 'CMA/ARV','url': base_url+'home/cma_arv_notes','note_type': '','btn_title':'Add Notes','is_seprate':false},
	"notes":{'title': '','url': base_url+'common/wholesale_notes','note_type': '','btn_title':'Add Notes','is_seprate':false},
	"hoa_lien_priorty":{'title': 'HOA Lien Priority','url': base_url+'notes','note_type': '','btn_title':'HOA Lien Priority','is_seprate':true},
	"hoa_rental_policy":{'title': 'POA/HOA Rental Policy','url': base_url+'notes','note_type': '','btn_title':'POA/HOA Rental Policy','is_seprate':true},
	"mortgage_notes":{'title': '','url': base_url+'notes','note_type': '','btn_title':'Add Notes','is_seprate':true},

};