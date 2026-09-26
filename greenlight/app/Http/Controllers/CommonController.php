<?php
/**
 * Created By Rativardhan Singh Sengar  5/26/19 10:54 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/26/19 9:57 PM
 */

namespace App\Http\Controllers;

use App\Exports\CommonExport;
use App\Helpers\CommonHelper;
use App\Models\AlarmMeModel;
use App\Models\AreaModel;
use App\Models\BuyersPictureModel;
use App\Models\CmaArvModel;
use App\Models\DepositLogHistoryModel;
use App\Models\PropertyNotesModel;
use App\Services\AlarmMeService;
use App\Services\GeoService;
use App\Services\HousePaymentLogService;
use App\Services\MailService;
use App\Services\PropertyService;
use App\Services\UrlPdfCsvExcelService;
use App\Services\UserService;
//use App\Traits\PaymentGatewayTrait;
use FontLib\TrueType\Collection;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Excel;
use App\Models\EsGuideModel;



class CommonController extends Controller {

    #use PaymentGatewayTrait;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $mailService;
    private $userService;
    private $housePaymentLogService;
    private $geoService;
    private $propertyService;
    private $alarmMeService;
    private $urlPdfCsvExcelService;
    private $excel;


    public function __construct(Request $request, MailService $mailService, UserService $userService, HousePaymentLogService $housePaymentLogService, GeoService $geoService
        , PropertyService $propertyService
        , AlarmMeService $alarmMeService
        , UrlPdfCsvExcelService $urlPdfCsvExcelService
        , Excel $excel
    ) {
        Log::info("CommonController: __construct called");
        $this->request                = $request;
        $this->mailService            = $mailService;
        $this->userService            = $userService;
        $this->housePaymentLogService = $housePaymentLogService;
        $this->geoService             = $geoService;
        $this->propertyService        = $propertyService;
        $this->alarmMeService         = $alarmMeService;
        $this->urlPdfCsvExcelService  = $urlPdfCsvExcelService;
        $this->excel                  = $excel;
    }

    private function likeWhere($query, $field, $value) {
        if (empty($value)) return $query;
        return $query->where($field, 'LIKE', "%$value%");
    }

    private function where($query, $field, $value, $condition = "=") {
        if (empty($value)) return $query;
        return $query->where($field, $condition, $value);
    }

    private function whereBetween($query, $field, $from, $to) {
        if (!empty($from)) $query->where($field, '>=', $from);

        if (!empty($to)) $query->where($field, '<=', $to);

        return $query;
    }


    public function lenderIt($house_id) {

        Log::info("CommonController: lenderIt called");

        // Check does he have access of this or not.
        $what_type_lender = $this->request->input('what_type_lender');
        $notes            = $this->request->input('notes');
        $credit_score     = $this->request->input('credit_score');
        $contact_number   = $this->request->input('contact_number');

        if (empty($what_type_lender)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Please select at least one lender type."], 200);
        }
        else if (empty($notes)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Please enter notes."], 200);
        }
        else if (empty($credit_score)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Please select credit score."], 200);
        }
        else if (empty($contact_number)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Please enter contact number."], 200);
        }

        // ToDo: make a history of this records.

        // send lender it  notes to every one
        $request_info             = $this->request->all();
        $info                     = $request_info;
        $info['what_type_lender'] = implode(",", $what_type_lender);
        $info['house_id']         = $house_id;

        $this->mailService->sendLenderItEmailToAmAndInsurance($info);
        return response()->json(['status'  => 'success',
                                 'message' => "We are forwarding your request to the Lenders. Please let us know if you do not hear from them in the next 24 hours. Thank You"], 200);
    }

    public function depositExecutiveLender($house_id) {
        Log::info("CommonController: depositExecutiveLender called");

        $rules = ['house_id'              => 'required|numeric|exists:home_information,house_id',
                  'turn_around_time'      => 'required',
                  'rate_of_return'        => 'required',
                  'renovation_risk'       => 'required',
                  'funding_deposit'       => ['required',
                                              Rule::in(['yes',
                                                        'no']),],
                  'loan_current'          => 'required',
                  'needed_for_deposit'    => 'required',
                  'needed_for_renovation' => 'required',
                  'miscelainous_fees'     => 'required',
                  'estimated_values_ab'   => ['required',
                                              Rule::in(['yes',
                                                        'no']),],
                  'estimated_values_bc'   => ['required',
                                              Rule::in(['yes',
                                                        'no']),],
                  'full_scope_work'       => ['required',
                                              Rule::in(['yes',
                                                        'no']),],
                  'full_material_list'    => ['required',
                                              Rule::in(['yes',
                                                        'no']),],
                  'your_timeline'         => ['required',
                                              Rule::in(['yes',
                                                        'no']),],
                  'purchased_over'        => 'required',
                  'flip_transactions'     => 'required',
                  'rehab_currently'       => 'required',
                  'p1_value'              => 'required',
                  'p1_adom'               => 'required',
                  'p2_value'              => 'required',
                  'p2_adom'               => 'required',
                  'p3_adom'               => 'required',
                  'p3_value'              => 'required',
                  'wholetail_value'       => 'required',
                  'rental_rate'           => 'required',
                  'deposit_notes'         => 'required',
                  'loan_type'             => 'required',

        ];

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $all['user_id']  = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        // We are not using this because , we want to return all error into one array box
        if ($validator->fails()) {
            //$errors = $validator->errors();
            $message = CommonHelper::customValidatorMessageArray($validator);

            return response()->json(['status'  => 'failed',
                                     'message' => $message], 200);
        }

        DepositLogHistoryModel::create($all);
        $this->mailService->sendDepositMail($all);

        return response()->json(['status'  => 'success',
                                 'message' => "Get Deposit/Exclusive Lender Request Submitted"], 200);
    }

    public function getdepositExecutiveLender($house_id) {
        Log::info("CommonController: depositExecutiveLender called");

        $info = CmaArvModel::where('house_id','=',$house_id)
                           ->whereIn('info_added_by',['first_dtc','second_dca','third_dca'])
                           ->orderBy('info_added_by','desc')->first();

        return response()->json(['status'  => 'success',
                                 "data"    => $info,
                                 'message' => ""], 200);
    }


    //
    /**
     * To return client token and amount of renovation bid..
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function renovationPaymentRequest($house_id) {

        // check if property already payed by same user for renovation bid
        $isFound = $this->housePaymentLogService->getByPaymentType($house_id, 'renovation_bid');

        if (empty($isFound)) {
            $token = $this->createBraintreeToken();

            $total_cost = $this->renovationTotalCost($house_id);

            return response()->json(['status'  => 'success',
                                     'message' => "",
                                     'data'    => ["total_cost" => $total_cost,
                                                   "token"      => $token,]], 200);


        }


        return response()->json(['status'  => 'failed',
                                 'message' => "You already paid for a renovation bid."], 200);

    }

    private function renovationTotalCost($house_id) {
        $total_cost = 150;

        $geoInfo = $this->geoService->findOneById($house_id);;
        if (empty($geoInfo) || empty($geoInfo->latitude) || empty($geoInfo->longitude)) {
            $total_cost = 250;
        }
        else {

            ## ToDo: ask craig for this, right now it is default for all location what about other state and other county
            //                $config['greensboro_lat'] = '36.067417';
            //                $config['greensboro_long'] = '-79.791985';

            $lat1 = '36.067417';
            $lon1 = '-79.791985';
            $lat2 = $geoInfo->latitude;
            $lon2 = $geoInfo->longitude;

            $distance = CommonHelper::get_distance($lat1, $lon1, $lat2, $lon2, 'miles');
            if ($distance > 100) {
                $total_cost = 250;
            }
        }

        return $total_cost;
    }

    public function renovationPaymentProcess($house_id) {
        $nounce = $this->request->input('nonce');
        if (empty($nounce)) {
            return response()->json(['status'  => 'failed',
                                     "message" => "Invalid payment process.",
                                     'data'    => []], 200);
        }


        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);

        if (empty($info)) {
            return response()->json(['status'  => 'failed',
                                     'message' => __("error_messages.house_id_exists")], 200);
        }

        $total_cost = $this->renovationTotalCost($house_id);

        $isPaid          = $this->makeBraintreeSale('renovation_', $total_cost, $house_id, $nounce);
        $data            = [];
        $data['is_paid'] = $isPaid;

        if ($isPaid !== true) {
            return response()->json(['status'  => 'failed',
                                     'message' => $isPaid], 200);
        }
        else {

            # Save House Payment Log
            $insertInfo                 = [];
            $insertInfo['house_id']     = $house_id;
            $insertInfo['total_cost']   = $total_cost;
            $insertInfo['payment_type'] = 'renovation_bid';
            $this->housePaymentLogService->create($insertInfo);

            # Send Renovation Bid Email
            $mailInfo             = [];
            $mailInfo['house_id'] = $house_id;
            $this->mailService->sendRenovationMail($mailInfo);

            # ToDo: send an email to User for renovation confirmation.

            return response()->json(['status'  => 'success',
                                     'message' => "Your order has been sent out to the Service Provider, they should be in touch with you soon. Please contact your Acquisition Manager if the Service Provider does not get a hold of you within the next 24 hours.",
                                     "data"    => $data], 200);
        }

    }

    public function titleSearchProcess($house_id) {

        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);

        if (empty($info)) {
            return response()->json(['status'  => 'failed',
                                     'message' => __("error_messages.house_id_exists")], 200);
        }

        # Send Renovation Bid Email
        $mailInfo             = [];
        $mailInfo['house_id'] = $house_id;
        $this->mailService->sendTitleSearchMail($mailInfo);

        # ToDo: send an email to User for Title Search confirmation.
        $data = [];
        return response()->json(['status'  => 'success',
                                 'message' => "Your order has been sent out to the Service Provider, they should be in touch with you soon. Please contact your Acquisition Manager if the Service Provider does not get a hold of you within the next 24 hours.",
                                 "data"    => $data], 200);

    }

    public function insuranceQuoteProcess($house_id) {

        # first check record exists or not
        $info = $this->propertyService->findOneById($house_id);

        if (empty($info)) {
            return response()->json(['status'  => 'failed',
                                     'message' => __("error_messages.house_id_exists")], 200);
        }

        # Send Renovation Bid Email
        $mailInfo             = [];
        $mailInfo['house_id'] = $house_id;
        $this->mailService->sendInsuranceQuoteMail($mailInfo);

        # ToDo: send an email to User for insurance quote confirmation.
        $data = [];
        return response()->json(['status'  => 'success',
                                 'message' => "We are forwarding your request to the insurance companies. Please let us know if you do not hear from them in the next 24 hours. Thank You",
                                 "data"    => $data], 200);

    }

    public function picturePaymentRequest() {

        $token = $this->createBraintreeToken();
        if (empty($token)) {

            return response()->json(['status'  => 'failed',
                                     'message' => "Something went wrong, Please try after some time."], 200);

        }

        return response()->json(['status'  => 'success',
                                 'message' => "",
                                 'data'    => ["token" => $token,]], 200);

    }

    public function picturePaymentProcess() {

        $nounce = $this->request->input('nonce');
        if (empty($nounce)) {
            return response()->json(['status'  => 'failed',
                                     "message" => "Invalid payment process.",
                                     'data'    => []], 200);
        }

        $number_home    = $this->request->input('number_home');
        $number_of_home = $this->request->input('number_of_home');
        $buyer_notes    = $this->request->input('buyer_notes');

        $get_a_picture = config('property_information.get_a_picture');

        if (!in_array($number_home, array_keys($get_a_picture))) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Invalid number of homes option."], 200);
        }

        if ($number_home == "crawl_space" && intval($number_of_home) <= 0) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Invalid crawl space number (number of homes)."], 200);
        }

        if (empty($buyer_notes)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Please enter buyer notes."], 200);
        }

        $crawl_space_calculation = config('property_information.crawl_space_calculation');

        // Calculate total price here.
        if ($number_home == 'crawl_space') {
            $total_cost = $crawl_space_calculation['1'] + $crawl_space_calculation['1+'] * ($number_of_home - 1);

        }
        else {

            if (intval($number_home) <= 0) {
                return response()->json(['status'  => 'failed',
                                         'message' => "Invalid number of homes."], 200);
            }

            $total_cost = $crawl_space_calculation['1'] + $crawl_space_calculation['1+'] * ($number_home - 1);
        }

        // Make one entry into database for this.
        $insert_info                   = [];
        $insert_info['user_id']        = $this->userService->user_id();
        $insert_info['total_cost']     = $total_cost;
        $insert_info['number_home']    = $number_home;
        $insert_info['number_of_home'] = $number_of_home;
        $insert_info['buyer_notes']    = $buyer_notes;
        $insert_info['paid_status']    = 'pending';
        $info                          = BuyersPictureModel::create($insert_info);

        $order_id = $info->buyers_picture_id;

        $isPaid          = $this->makeBraintreeSale('get_picture_', $total_cost, $order_id, $nounce);
        $data            = [];
        $data['is_paid'] = $isPaid;

        if ($isPaid !== true) {
            BuyersPictureModel::where('buyers_picture_id', $order_id)
                              ->update(['paid_status' => 'failed']);

            return response()->json(['status'  => 'failed',
                                     'message' => "Your payment can not be process, Please contact your Acquisition Manager."], 200);
        }
        else {

            # Save House Payment Log
            BuyersPictureModel::where('buyers_picture_id', $order_id)
                              ->update(['paid_status' => 'paid']);

            # Send Renovation Bid Email
            $info = $insert_info;
            $this->mailService->sendPictureMail($info);

            # ToDo: send an email to User for renovation confirmation.
            return response()->json(['status'  => 'success',
                                     'message' => "Your order has been sent out to the Service Provider, they should be in touch with you soon. Please contact your Acquisition Manager if the Service Provider does not get a hold of you within the next 24 hours.",
                                     "data"    => $data], 200);
        }

    }

    public function areaInviteData() {

        $area_list = AreaModel::select('area_id', 'area_name')->orderBy('area_name', 'asc')->get();
        return response()->json(['status'  => 'success',
                                 'message' => "",
                                 "data"    => $area_list], 200);
    }

    public function areaInvite($house_id) {
        # ToDO: Implement code to send area invite emails to all users exists in table.
        # ToDO: No home buyer can call this function
        # ToDo: check old code before implementation.


        $invite_type = $this->request->input('invite_type');
        $area_id     = $this->request->input('area_id');
        $note        = $this->request->input('note');

        if (empty($invite_type) || !in_array($invite_type, array('area_invite',
                                                                 'county_invite'))) {

            return response()->json(['status'  => 'failed',
                                     'message' => "Invalid invite type."], 200);

        }

        if (empty($area_id)) {
            return response()->json(['status'  => 'failed',
                                     'message' => "Invalid area."], 200);
        }

        if ($invite_type == 'area_invite') {
            // Check valid area_id or not
            $isArea = AreaModel::where('area_id', $area_id)->exists();

            if (!$isArea) {
                return response()->json(['status'  => 'failed',
                                         'message' => "Invalid request, Please refresh page and try again."], 200);
            }
        }

        return response()->json(['status'  => 'success',
                                 'message' => "Your invite has been sent."], 200);

    }

    public function email() {
        Log::info("CommonController: email called");

        $rules = ['to'        => 'required|email',
                  'subject'   => 'required',
                  'message'   => 'required',
                  'house_ids' => 'required',
                  'type'      => ["required",
                                  Rule::in(['url',
                                            'pdf',
                                            '40_details',
                                            'email_acquistion_info']),],];


        $all       = $this->request->all();
        $validator = Validator::make($all, $rules);

        // We are not using this because , we want to return all error into one array box
        if ($validator->fails()) {
            // $errors = $validator->errors();
            $message = CommonHelper::customValidatorMessageArray($validator);
            return response()->json(['status'  => 'failed',
                                     'message' => $message], 200);
        }

        if ($all['type'] == "url") {
            $this->mailService->sendHouseLinkEmail($all);
        }
        else if ($all['type'] == "pdf") {
            $this->mailService->sendHouse40DetailsEmail($all);
        }
        else if ($all['type'] == "40_details") {
            $this->mailService->sendHouse40DetailsEmail($all);
        }
        else if ($all['type'] == "email_acquistion_info") {
            $this->mailService->sendHouseAcquistionInfoEmail($all);
        }

        return response()->json(['status'  => 'success',
                                 'message' => "Your request has been completed."], 200);
    }

    public function printD() {

        Log::info("CommonController: printD called");

        $rules = [
            'house_ids' => 'required',
            'type'      => ["required",
                            Rule::in(['url',
                                      'pdf',
                                      '40_details',
                                      'email_acquistion_info']),],];


        $all       = $this->request->all();
        $validator = Validator::make($all, $rules);

        // We are not using this because , we want to return all error into one array box
        if ($validator->fails()) {
            // $errors = $validator->errors();
            $message = CommonHelper::customValidatorMessageArray($validator);
            return response()->json(['status'  => 'failed',
                                     'message' => $message], 200);
        }

        $url = "";
        if ($all['type'] == "url") {
            $url = $this->urlPdfCsvExcelService->houseLinkPdf($all);
        }
        else if ($all['type'] == "pdf") {
            $url = $this->urlPdfCsvExcelService->house40DetailsPdf($all);
        }
        else if ($all['type'] == "40_details") {
            $url = $this->urlPdfCsvExcelService->house40DetailsPdf($all);
        }
        else if ($all['type'] == "email_acquistion_info") {
            $url = $this->urlPdfCsvExcelService->houseAcquistionInfoPdf($all);
        }

        return response()->json(['status'  => 'success',
                                 "data"    => ['url' => $url],
                                 'message' => "Your request has been completed."], 200);
    }

    public function export() {

        Log::info("CommonController: export called");
        $export_name = [
            "export_url",
            "export_40detail",
            "export_acquistion",
            "export_texas",
            "export_hoa",
            "export_quickview",
            "export_redemption",
            "export_bidder", //Rati is working on: Not Done
            "export_winding_bidder", //Rati is working on: Not Done
            'export_subto',
            'export_texas_auction',
            'export_redemption_excess_fund'
        ];

        $export_type = ["pdf",
                        "xls",
                        "csv"];


        $rules = [
            'house_ids'   => 'required',
            'export_name' => ["required",
                              Rule::in($export_name),],
            'export_type' => ["required",
                              Rule::in($export_type),]
            ,];

        $all       = $this->request->all();
        $validator = Validator::make($all, $rules);

        // We are not using this because , we want to return all error into one array box
        if ($validator->fails()) {
            // $errors = $validator->errors();
            $message = CommonHelper::customValidatorMessageArray($validator);
            return response()->json(['status'  => 'failed',
                                     'message' => $message], 400)
                             ->setStatusCode(400, $message[0]);;
        }

        //        $export_name = [
        //            "export_url"
        //            ,"export_40detail"
        //            ,"export_acquistion"
        //            ,"export_bidder"
        //            ,"export_texas"
        //            ,"winning_bidder"
        //            ,"export_quickview"
        //        ];
        //
        //        $export_type = ["pdf","xls","csv"];
        $blobFilePath = '';
        if ($all['export_type'] == "pdf") {
            if ($all['export_name'] == "export_url") {
                $blobFilePath = $this->urlPdfCsvExcelService->houseLinkPdf($all, true);
            }
            else if ($all['export_name'] == "export_40detail") {
                $blobFilePath = $this->urlPdfCsvExcelService->house40DetailsPdf($all, true);
            }
            else if ($all['export_name'] == "export_acquistion") {
                $blobFilePath = $this->urlPdfCsvExcelService->houseAcquistionInfoPdf($all, true);
            }
            else if ($all['export_name'] == "export_hoa") {
                $blobFilePath = $this->urlPdfCsvExcelService->houseAHOAInfoPdf($all, true);
            }
            else if ($all['export_name'] == "export_texas") {
                $blobFilePath = $this->urlPdfCsvExcelService->houseTexasAuctionPdf($all, true);
            }
            else if ($all['export_name'] == "export_quickview") {
                $blobFilePath = $this->urlPdfCsvExcelService->quickViewPdf($all, true);
            }
            else if ($all['export_name'] == "export_redemption") {
                $blobFilePath = $this->urlPdfCsvExcelService->redemptionPdf($all, true);
            }
            else if ($all['export_name'] == "export_bidder") {
                $blobFilePath = $this->urlPdfCsvExcelService->bidderPdf($all, true);
            } 
            else if ($all['export_name'] == "export_winding_bidder") {
                $blobFilePath = $this->urlPdfCsvExcelService->winningBidderPdf($all, true);
            }
            else if ($all['export_name'] == "export_subto") {
                $blobFilePath = $this->urlPdfCsvExcelService->subToPdf($all, true);
            }
            else if ($all['export_name'] == "export_texas_auction") {
                $blobFilePath = $this->urlPdfCsvExcelService->texasAuctionExportToPdf($all, true);
            }
            else if ($all['export_name'] == "export_redemption_excess_fund") {
                $blobFilePath = $this->urlPdfCsvExcelService->redemptionExcessFundPdf($all, true);
            }
            ## TODO: add pdf section here for export_texas from old website.


            return response($blobFilePath, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename=GLPF_15643723071.pdf');
        }
        else if ($all['export_type'] == "csv") {
            $csvArray = [];
            if ($all['export_name'] == "export_url") {
                $csvArray = $this->urlPdfCsvExcelService->houseLinkArray($all, true);
                $temp     = [];
                foreach ($csvArray as $key => $value) {
                    $temp[] = [$value];
                }
                $csvArray = $temp;
            }
            else if ($all['export_name'] == "export_40detail") {
                $csvArray = $this->urlPdfCsvExcelService->house40DetailsArray($all, true);

            }
            else if ($all['export_name'] == "export_acquistion") {
                $csvArray = $this->urlPdfCsvExcelService->houseAcquistionInfoArray($all, true);
            }
            else if ($all['export_name'] == "export_hoa") {
                $csvArray = $this->urlPdfCsvExcelService->houseAHOAInfoArray($all, true);

            }
            else if ($all['export_name'] == "export_texas") {
                $csvArray = $this->urlPdfCsvExcelService->houseTexasAuctionArray($all, true);
            }
            else if ($all['export_name'] == "export_quickview") {
                $csvArray = $this->urlPdfCsvExcelService->quickViewArray($all, true);
            }
            else if ($all['export_name'] == "export_redemption") {
                $csvArray = $this->urlPdfCsvExcelService->redemptionArray($all, true);
            }
            else if ($all['export_name'] == "export_bidder") {
                $csvArray = $this->urlPdfCsvExcelService->bidderArray($all, true);
            }
            else if ($all['export_name'] == "export_winding_bidder") {
                $csvArray = $this->urlPdfCsvExcelService->winningBidderArray($all, true);
            }
            else if ($all['export_name'] == "export_subto") {
                $csvArray = $this->urlPdfCsvExcelService->subToArray($all, true);
            }
            else if ($all['export_name'] == "export_texas_auction") {
                $csvArray = $this->urlPdfCsvExcelService->texasAuctionExportToArray($all, true);
            }
            else if ($all['export_name'] == "export_redemption_excess_fund") {
                $csvArray = $this->urlPdfCsvExcelService->redemptionExcessFundArray($all, true);
            }
            $str = CommonHelper::arrayToCSV($csvArray);
            return response($str, 200)
                ->header('Content-Type', 'application/csv')
                ->header('Content-Disposition', 'attachment; filename=GLPF_15643723071.csv');
        }
        else if ($all['export_type'] == "xls") {
            //$contents = Excel::raw(new CommonExport, Excel::XLSX);
            $csvArray = [];

            if ($all['export_name'] == "export_url") {
                $csvArray = $this->urlPdfCsvExcelService->houseLinkArray($all, true);

                // Change into MultiDimension array
                $temp = [];
                foreach ($csvArray as $key => $value) {
                    $temp[] = [$value];
                }
                $csvArray = $temp;
            }
            else if ($all['export_name'] == "export_40detail") {
                $csvArray = $this->urlPdfCsvExcelService->house40DetailsArray($all, true);

            }
            else if ($all['export_name'] == "export_acquistion") {
                $csvArray = $this->urlPdfCsvExcelService->houseAcquistionInfoArray($all, true);
            }
            else if ($all['export_name'] == "export_hoa") {
                $csvArray = $this->urlPdfCsvExcelService->houseAHOAInfoArray($all, true);
            }
            else if ($all['export_name'] == "export_texas") {
                $csvArray = $this->urlPdfCsvExcelService->houseTexasAuctionArray($all, true);
            }
            else if ($all['export_name'] == "export_quickview") {
                $csvArray = $this->urlPdfCsvExcelService->quickViewArray($all, true);
            }
            else if ($all['export_name'] == "export_redemption") {
                $csvArray = $this->urlPdfCsvExcelService->redemptionArray($all, true);
            }
            else if ($all['export_name'] == "export_bidder") {
                $csvArray = $this->urlPdfCsvExcelService->bidderArray($all, true);
            }
            else if ($all['export_name'] == "export_winding_bidder") {
                $csvArray = $this->urlPdfCsvExcelService->winningBidderArray($all, true);
            }
            else if ($all['export_name'] == "export_subto") {
                $csvArray = $this->urlPdfCsvExcelService->subToArray($all, true);
            }
            else if ($all['export_name'] == "export_texas_auction") {
                $csvArray = $this->urlPdfCsvExcelService->texasAuctionExportToArray($all, true);
            }
            else if ($all['export_name'] == "export_redemption_excess_fund") {
                $csvArray = $this->urlPdfCsvExcelService->redemptionExcessFundArray($all, true);
            }
            $export = new CommonExport($csvArray);

            #ToDo: temporary fix, deleteFileAfterSend: false creating a lot of temp files, Please fix this.
            $info = $this->excel->download($export, 'GLPF_' . time() . '.xlsx')->deleteFileAfterSend(false);
            return $info;
            //$str =  $this->excel->raw($csvArray, \Maatwebsite\Excel\Excel::XLS);
            // return $this->excel->download(new Collection($csvArray), 'invoices.xlsx');
            // return $this->excel->download($csvArray, 'invoices.xlsx');
            // $str = CommonHelper::arrayToCSV($csvArray);
            //            return response($str, 200)
            //                ->header('Content-Type', 'application/vnd.ms-excel')
            //                ->header('Content-Disposition', 'attachment; filename=ES_15643723071.xls')
            //                ;
        }


        return response()->json(['status'  => 'failed',
                                 'message' => __("error_messages.something_wrong")], 400)
                         ->setStatusCode(400, __("error_messages.something_wrong"));

    }

    public function alarmMe($house_id) {

        # ToDo: get alarm list API
        # ToDo: remove alarm list api
        # ToDo: one flag to show alarm popup with particualr properties.
        # ToDo: make alarm settings page and API
        # ToDo: also make option for send email and implement port notification to manage this.

        $isExists = $this->alarmMeService->exists(['user_id'  => $this->userService->user_id(),
                                                   'house_id' => $house_id]);

        if (!$isExists) {
            // Add Into Database
            $all             = [];
            $all['house_id'] = $house_id;
            $this->alarmMeService->create($all);

            return response()->json(['status'  => 'success',
                                     'message' => "Alarm has been added to your alarm list."], 200);
        }


        return response()->json(['status'  => 'failed',
                                 'message' => "Alarm already exists in your alarm list."], 200);

    }

    public function alarmMeList() {

        # ToDo: get alarm list API
        # ToDO: code mimplemented from UserFavouriteController, try to make it common.

        Log::info("CommonController: alarmMeList called");
        $limit = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if ($limit > 50) $limit = 50;

        $offset = $this->request->get('offset') ? $this->request->get('offset') : 0;


        $address        = $this->request->get('address');
        $city           = $this->request->get('city');
        $county         = $this->request->get('county');
        $state          = $this->request->get('state');
        $zip            = $this->request->get('zip');
        $sale_date_from = $this->request->get('sale_date_from');
        $sale_date_to   = $this->request->get('sale_date_to');


        $info = AlarmMeModel::select([
                                         "home_information.house_id"
                                         ## Needed this field, Important this line
                                     ])
                            ->leftJoin('home_information', 'home_information.house_id', '=', 'alarm_me.house_id');

        $info = $this->where($info, 'alarm_me.user_id', $this->userService->user_id());
        $info = $this->likeWhere($info, 'home_information.address', $address);
        $info = $this->likeWhere($info, 'city', $city);
        $info = $this->likeWhere($info, 'county', $county);
        $info = $this->where($info, 'state', $state);
        $info = $this->where($info, 'zip', $zip);
        $info = $info->whereNull('home_information.deleted_at');
        $isManyJoin = false;

        if (!empty($sale_date_from) || !empty($sale_date_to)) {
            $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
            $info       = $this->whereBetween($info, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
            $isManyJoin = true;
        }


        if ($isManyJoin === false) {
            $total = $info->count();
        }
        else {
            # ToDO convert this into above count info ..
            $info->groupBy('home_information.house_id');
            $total = $info->count();
        }

        // $info->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id');
        //$info->leftJoin('trustee', 'home_information.house_id', '=', 'trustee.house_id');
        $info->select([
                          "alarm_me.*",
                          "home_information.address",
                          "home_information.city",
                          "home_information.county",
                          "home_information.state",
                          "home_information.zip",
                          "home_information.total_living_sqft",
                          "home_information.year_built",
                          "home_information.bed",
                          "home_information.bath",
                          'home_information.lot_acreage_sf',
                          'home_information.parcel_id1',
                          'home_information.parcel_id2',
                          'home_information.subdivision',
                          'home_information.total_living_sqft',
                          'home_information.county_value',
                          //"property_descriptions.*",
                          //"local_real_estate.zestimate",
                          "home_information.house_id"
                          ## Needed this field, Important this line
                      ]);
       
        $info->with([
                        "house" => function ($query) {
                            $query->select([
                                               "home_information.house_id"
                                               ## Needed this field, Important this line
                                           ]);
                        },
                        "house.schools_and_neighborhood",
                        "house.property_descriptions",
                        "house.local_real_estate_details",
                        "house.geo",
                        "house.last_sale_details"     => function ($query) {
                            $query->select(['house_id',
                                            'sale_id',
                                            'trustee',
                                            'sale_date',
                                            'sale_time'
                                            ,
                                            'case_number',
                                            'priceint',
                                            'opening_bid',
                                            'sale_type'
                                            ,
                                            'nos_by',
                                            'nos_date'
                                           ]);
                        },
                        "house.last_sale_details.nos" => function ($query) {
                            $query->select(["users.id",
                                            "users.email",
                                            "users.first_name",
                                            "users.last_name"]);
                        },
                        "house.last_cma_arv_recommendations"  => function ($query) {
                            $query->select(['house_id',
                                            'recommended_cma_arv',
                                            'general_demand',
                                            'specific_demand']);
                        },
                        "house.cma_arv_recommendations"       => function ($query) {
                            $query->select(['house_id',
                                            'info_added_by',
                                            'user_id',
                                            'date']);
                        },
                        "house.cma_arv_recommendations.user"  => function ($query) {
                            $query->select(["users.id",
                                            "users.email",
                                            "users.first_name",
                                            "users.last_name"]);
                        },
                        "house.first_liens"                   => function ($query) {
                            $query->select(['house_id',
                                            'lien_amount',
                                            'date_recorded',
                                            'lien_foreclosing']);
                        },
                        'house.last_sale_details.last_bidder' => function ($query) {
                            $query->select(['house_id',
                                            'sale_id',
                                            'min_amt_nxt_ub',
                                            'last_date_to_upset_bid'
                                            ,
                                            'name_upset_bidder as wining_bidder',
                                            'amount_of_bid as winning_bid'
                                            ,
                                            'im_by',
                                            'im_date'
                                           ]);
                        },

                        'house.last_sale_details.last_bidder.im' => function ($query) {
                            $query->select(["users.id",
                                            "users.email",
                                            "users.first_name",
                                            "users.last_name"]);
                        },
                        "house.wholesale_buyer_strategy"         => function ($query) {
                            $query->select(['house_id',
                                            'est_close_date_a_to_b']);
                        },
                        "house.front_picture"                    => function ($query) {
                            $query->select(['house_id',
                                            'org_name',
                                            'store_name']);
                        }]);
        $info = $info->skip(intval($offset))->take(intval($limit))->get();

        if (empty($info)) {
            return response()->json(['status'  => 'success',
                                     'total'   => $total,
                                     'data'    => [],
                                     'message' => __("error_messages.record_not_exists")], 200);
        }


        $info = $info->map(function ($children) {
            $children->trustee                      = $children->house->trustee;
            $children->schools_and_neighborhood     = $children->house->schools_and_neighborhood;
            $children->property_descriptions        = $children->house->property_descriptions;
            $children->local_real_estate_details    = $children->house->local_real_estate_details;
            $children->geo                          = $children->house->geo;
            $children->last_sale_details            = $children->house->last_sale_details;
            $children->last_cma_arv_recommendations = $children->house->last_cma_arv_recommendations;
            $children->first_liens                  = $children->house->first_liens;
            $children->wholesale_buyer_strategy     = $children->house->wholesale_buyer_strategy;
            $children->front_picture                = $children->house->front_picture;
            $children->makeHidden('house');
            return $children;
        });

        return response()->json(['status' => 'success',
                                 'total'  => $total,
                                 'data'   => $info], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function alarmMeDelete($alarm_id)
    {
        Log::info("CommonController: alarmMeDelete called");

        try
        {
            $info = AlarmMeModel::where(['alarm_id'=>$alarm_id,"user_id"=>$this->userService->user_id()]);
            if ($info->count() > 0)
            {
                $info->delete();
                return response()->json(['status' => 'success','message' => __("messages.record_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['status' => 'failed','message' => __("messages.record_not_exists"), 'data' => []], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status' => 'failed','message' => __("error_messages.something_wrong")], 400);
        }

    }

    public function wholesaleRetail()
    {
        $rules = [
            'house_ids'       => 'required_without:house_id',
            'house_id'        => 'required_without:house_ids',
            'arv'             => 'required',
            'cma_arv'         => 'required',
            'renovation_cost' => 'required',
        ];
        $messages = [
            'house_ids.required_without' => 'Please select at least one property.',
            'house_id.required_without' => 'Please select at least one property.',
        ];

        $all       = $this->request->all();
        $validator = Validator::make($all, $rules,$messages);

        // We are not using this because , we want to return all error into one array box
        if ($validator->fails()) {
            // $errors = $validator->errors();
            $message = CommonHelper::customValidatorMessageArray($validator);
            return response()->json(['status'  => 'failed',
                                     'message' => $message], 200);
        }

        $url = $this->urlPdfCsvExcelService->wholesaleRetailPdf($all);

        return response()->json(['status'  => 'success',
                                 "data"    => ['url' => $url],
                                 'message' => "Your request has been completed."], 200);
    }



    public function noteList($house_id) {

        Log::info("CommonController: index called");
        $info = PropertyNotesModel::where('house_id','=',$house_id)
        ->where("user_id",$this->userService->user_id())
        ;

        $info = $info ->get();
        return response()->json(['status'  => 'success',
            'message' => ".",
            'data'    => $info,
        ], 200);
    }


    public function noteCreate($house_id) {

        Log::info("CommonController: create called");
        $rules = ['house_id' => 'required|numeric|exists:home_information,house_id',
            'notes'     => 'required',];
        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $all['user_id'] = $this->userService->user_id();

        $validator = Validator::make($all, $rules);
        if ($validator->fails()) {
            $message = CommonHelper::customValidatorMessageArray($validator);
            return response()->json(['status'  => 'failed',
                'message' => $message], 200);
        }

        $info = PropertyNotesModel::create($all);
        return response()->json(['status'  => 'success',
            'data'=>$info,
            'message' => "Notes added successfully."], 200);
    }

    public function noteDelete($id)
    {
        Log::info("CommonController: noteDelete called");
        try
        {
            $info = PropertyNotesModel::find($id);
            if ($info == true)
            {
                // check user id of notes
                if($info->user_id != $this->userService->user_id())
                {
                    return response()->json(['message' => __("messages.not_delete")], 200);
                }
                $info->delete();
                return response()->json(['message' => __("messages.record_delete")
                    ], 200);
            }
            else
            {
                return response()->json(['message' => __("error_messages.record_not_exists")], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['message' => __("error_messages.something_wrong")], 400);
        }
    }

    public function getEsGuide(){
        $info=EsGuideModel::orderBy('id', 'DESC')->get();
        return response()->json(['status'  => 'success',
            'message' => ".",
            'data'    => $info,
        ], 200);
    }

    function alarmMeListDetail(){
        $userId=$this->userService->user_id();
        $info=$this->alarmMeService->getAlarmMe($userId);
        return response()->json(['status'  => 'success',
            'message' => ".",
            'data'    => $info,
        ], 200);
    }

    function updateAlarmMe(){
        $userId=$this->userService->user_id();
        $info=$this->alarmMeService->updateAlarmMe($userId);
        return response()->json(['status'  => 'success',
            'message' => ".",
            'data'    => $info,
        ], 200);
    }
}
