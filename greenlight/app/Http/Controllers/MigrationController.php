<?php
/**
 * Created By Rativardhan Singh Sengar 7/29/19 11:41 PM
 * Copyright (c) 2019. All rights Reserved
 * Last Modified 7/29/19 11:34 PM
 */

namespace App\Http\Controllers;

use App\ContactUs;
use App\Helpers\CommonHelper;
use App\Models\AverageDaysMarketModel;
use App\Models\DocumentAccountingModel;
use App\Models\AlarmMeModel;
use App\Models\AreaContyModel;
use App\Models\AreaEmailsModel;
use App\Models\AreaModel;
use App\Models\AssessmentModel;
use App\Models\BorrowerModel;
use App\Models\CmaArvModel;
use App\Models\DocumentBidderModel;
use App\Models\DocumentMortgage;
use App\Models\DocumentMortgageHoa;
use App\Models\DocumentMortgageOther;
use App\Models\DocumentOwnerModel;
use App\Models\DocumentPictureModel;
use App\Models\DocumentPropertyModel;
use App\Models\DocumentSaleModel;
use App\Models\EmailsAmModel;
use App\Models\GeoModel;
use App\Models\HoaEmailsModel;
use App\Models\HomeBuyersAlias2DarrenModel;
use App\Models\HouseBuyItHistoryModel;
use App\Models\HouseBuyItModel;
use App\Models\HouseTokenModel;
use App\Models\InviteHistoryModel;
use App\Models\InviteModel;
use App\Models\LocalRealEstateModel;
use App\Models\ManagerNotesModel;
use App\Models\MapVideoModel;
use App\Models\MortgageHoaModel;
use App\Models\MortgageLiensModel;
use App\Models\MortgageOtherModel;
use App\Models\OwnerModel;
use App\Models\PriceHistoryModel;
use App\Models\PropertyAcquisitionAtoBFirstModel;
use App\Models\PropertyAcquisitionAtoBSecondModel;
use App\Models\PropertyAcquisitionBtoCFirstModel;
use App\Models\PropertyAcquisitionBtoCSecondModel;
use App\Models\PropertyDescriptionsModel;
use App\Models\PropertyModel;
use App\Models\PropertyQueueListHousesModel;
use App\Models\PropertyQueueListModel;
use App\Models\RolesModel;
use App\Models\SaleBidderModel;
use App\Models\SaleBidderNotesModel;
use App\Models\SaleDetailsDescriptionsModel;
use App\Models\SaleDetailsModel;
use App\Models\SchoolNeighborhoodModel;
use App\Models\User;
use App\Models\UserFavoritesModel;
use App\Models\UserModel;
use App\Models\UserPaymentLog;
use App\Models\UserRolesModel;
use App\Models\WholesaleBuyerNModel;
use App\Models\WholesaleBuyerNTotalModel;
use App\Models\WholesaleBuyerStrategyModel;
use App\Services\InviteHistoryService;
use App\Services\InviteService;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
Use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;

class MigrationController extends Controller
{

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userService;
    private $keyStoreForRecall = [];
    private $isExistsUser      = [];
    private $inviteService;
    private $inviteHistoryService;


    public function __construct(Request $request
        , UserService $userService
    , InviteService $inviteService
    , InviteHistoryService $inviteHistoryService
    )
    {
        Log::info("CommonController: __construct called");
        $this->request = $request;
        $this->userService = $userService;
        $this->inviteService = $inviteService;
        $this->inviteHistoryService = $inviteHistoryService;
    }

    public function migrationFirst()
    {

        // Check key first before migration
        $key = $this->request->input('key');
        if ($key != "rati12") {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        AreaModel::where('area_id', '>', 0)->forceDelete();
        AreaContyModel::where('area_county_id','>',0)->forceDelete();
        AreaEmailsModel::where('area_email_id','>',0)->forceDelete();

        // Migrate User Info
        // $this->migrateUserInfo();
        #ToDO create default user with bank username, firstname, lastname after users data migration
        $this->print_mem();

        // Migrate area invite list here,

        //        $last = AreaModel::orderBy('area_id', 'DESC')->first();
        //        $area_id = 0;
        //        if (!empty($last)) $area_id = $last->area_id;

        // Fetch all Area info from old database
        $areaBy = DB::connection('olddb')
            ->table('area')
            ->select('*')
            ->where('area_id','>',0)
            ->orderBy('area.area_id', 'asc')->get();

        Log::emergency("DB Area By - " . ($areaBy->count()));

        if ($areaBy != null) {
            foreach ($areaBy as $tkey => $tvalue) {

                $transactionResult = DB::transaction(function () use ( $tvalue) {

                    $insert = [];
                    $insert['area_id'] = $tvalue->area_id;
                    $insert['area_name'] = $tvalue->area_name;
                    $insert['state'] = $tvalue->state;
                    $insert['area_status'] = $tvalue->area_status;
                    $insert['created_at'] = strtotime($tvalue->created_date);
                    // $insert['updated_at'] = $tvalue->area_id;
                    AreaModel::insert($insert);
                });
            }
        }


        // Fetch all Area info from old database
        $areaBy = DB::connection('olddb')
            ->table('area_county')
            ->select('*')
            ->where('area_county_id','>',0)
            ->orderBy('area_county.area_county_id', 'asc')->get();

        Log::emergency("DB Area By - " . ($areaBy->count()));

        if ($areaBy != null) {
            foreach ($areaBy as $tkey => $tvalue) {

                $transactionResult = DB::transaction(function () use ( $tvalue) {

                    $insert = [];
                    $insert['area_county_id'] = $tvalue->area_county_id;
                    $insert['area_id'] = $tvalue->area_id;
                    $insert['state'] = $tvalue->state;
                    $insert['area_county'] = $tvalue->area_county;
                    //$insert['created_at'] = strtotime($tvalue->created_date);
                    // $insert['updated_at'] = $tvalue->area_id;
                    AreaContyModel::insert($insert);
                });
            }
        }

        // Fetch all Area info from old database
        $areaBy = DB::connection('olddb')
            ->table('area_emails')
            ->select('*')
            ->where('area_email_id','>',0)
            ->orderBy('area_emails.area_email_id', 'asc')->get();

        Log::emergency("DB Area By - " . ($areaBy->count()));

        if ($areaBy != null) {
            foreach ($areaBy as $tkey => $tvalue) {

                $transactionResult = DB::transaction(function () use ( $tvalue) {
                    $insert = [];
                    $insert['area_email_id'] = $tvalue->area_email_id;
                    $insert['area_id'] = $tvalue->area_id;
                    $insert['email_id'] = $tvalue->email_id;
                    //$insert['created_at'] = strtotime($tvalue->created_date);
                    // $insert['updated_at'] = $tvalue->area_id;
                    AreaEmailsModel::insert($insert);
                });
            }
        }
        Log::emergency("Final End of Records Completed - ");

    }

    public function migrationPrimary()
    {

        // Check key first before migration
        $key = $this->request->input('key');
        $chunk = $this->request->input('chunk');
        $chunk = $chunk ? $chunk : 1000;

        $limit = $this->request->input('limit');
        $limit = $limit ? $limit : 3000;

        if ($key != "rati12") {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        if (env("APP_ENV") == "local111") {
            # ToDO: remove this code later.. very important
            Log::emergency(" Remove record from database - ");
            PropertyModel::where('house_id', '>', 0)->delete();
            User::where('id', '>', 0)->delete();
            ContactUs::where('id', '>', 0)->delete();
            DB::table('login_history')->where('id', '>', 0)->delete();
        }

        // Migrate User Info
        $this->migrateUserInfo();

        $last = PropertyModel::orderBy('house_id', 'DESC')->first();

        #ToDO create default user with bank username, firstname, lastname after users data migration

        # TODO: check logic here
        $counter = 0;
        $chunkLimit = $limit;
        $primaryHouseId = 0;
        if (!empty($last)) $primaryHouseId = $last->house_id;

        // Last Sale Id
        $saleInfo = SaleDetailsModel::orderBy('sale_id', 'desc')->first();
        $sale_id = intval(@$saleInfo->sale_id) + 1;

        $mortgageInfo = MortgageLiensModel::orderBy('mortgage_id', 'desc')->first();
        $mortgage_id = intval(@$mortgageInfo->mortgage_id) + 1;

        Log::emergency("----------------House: " . $primaryHouseId . "-------------- ");
        $this->print_mem();

        DB::connection('olddb')
            ->table('home information')
            ->leftJoin('home_info2', 'house_id', '=', 'home information.house id')
            ->select('*')
            ->where('home information.house id', ">", $primaryHouseId)
            //->whereIn('home information.house id', [701])//[1800932,1817096,1805896,212939])  // [18165]) //
            ->where('is_deleted', '!=', 'yes')
            ->orderBy('home information.house id', 'asc')
            ->chunk($chunk, function ($houses) use (
                &$counter, $chunkLimit, &$sale_id, &$mortgage_id

            ) {
                Log::emergency("-------------------------Counter: " . $counter . "-------------------------- ");
                $this->print_mem();


                $temp = [];
                $tempD = [];
                $house_ids = [];
                $geoData = [];
                $localRealStateData = [];
                $priceHistoryData = [];
                $schoolAndNeighbourHood = [];
                $assessmentTaxes = [];
                $managerNotes = [];
                $property_document = [];
                $recorded_document = [];
                $pictureAndVideo = [];
                $sale_details = [];
                $sale_details_descriptions = [];
                $house_id_sale_id = [];
                $ownerByInfo = [];
                $owner_document = [];
                $borrowerByInfo = [];
                $mortgageLiens = [];
                $house_id_mortgage_id = [];
                $mortgageLienDocuments = [];

                $mortgageOtherLiens = [];
                $mortgageOtherDocument = [];
                $mortgageHoaLiens = [];
                $mortgageHoaDocument = [];

                $counter = $counter + count($houses);
                if ($counter > $chunkLimit) return false;

                foreach ($houses as $house) {
                    $house_id = $house->{'house id'};
                    $house_ids[] = $house_id;


                    $pool = @explode('/', $house->{'pool/spa'});

                    $poolSpa = 9;

                    $yes = ['yes',
                        'Yes',
                        'Y',
                        'y'];
                    $no = ['no',
                        'NO',
                        'No',
                        'N',
                        'n'];
                    if (in_array(@$pool[0], $yes) && in_array(@$pool[1], $yes)) $poolSpa = 3;
                    else if (in_array(@$pool[0], $yes)) $poolSpa = 1;
                    else if (in_array(@$pool[1], $yes)) $poolSpa = 2;

                    $property_type = CommonHelper::searchSimilarMatch($house->{'property type'}, config('property_information.property_types'), 0);

                    $sptValue = 0;
                    if ($property_type != 0) {
                        $spt = config('property_information.specific_property_types');
                        $spt = $spt[$property_type][key($spt[$property_type])];
                        $sptValue = CommonHelper::searchSimilarMatch($house->{'specific pt'}, $spt, 0);
                    }

                    $year_built = CommonHelper::decimalRemovalCharacter($house->{'year built'});

                    $insert = ['house_id' => $house_id,
                        'zpid' => $house->zpid ? $house->zpid : 0,
                        'address' => $house->address,
                        'city' => $house->city,
                        'county' => $house->cCounty,
                        'state' => strlen($house->state) > 2 ? CommonHelper::searchIndexByValue($house->state, config('constants.states'), null) : $house->state,
                        'zip' => $house->zip,
                        'total_living_sqft' => CommonHelper::decimalRemovalCharacter($house->sq),
                        'cost_per_sqft' => CommonHelper::decimalRemovalCharacter($house->pricesq),
                        'total_sqft' => CommonHelper::decimalRemovalCharacter($house->total_sq),
                        'cost_sqft' => null,
                        //round($house->comp/$house->total_sq, 2),
                        'main_floor_area' => CommonHelper::decimalRemovalCharacter($house->{'main fl area'}),
                        'second_floor_area' => CommonHelper::decimalRemovalCharacter($house->second_floor),
                        'third_floor_area' => CommonHelper::decimalRemovalCharacter($house->{'upper fl area'}),
                        'basement_area' => CommonHelper::decimalRemovalCharacter($house->{'basement area'}),
                        'finished_basement_area' => CommonHelper::decimalRemovalCharacter($house->{'finished bsmt area'}),
                        'finished_attic' => CommonHelper::decimalRemovalCharacter($house->{'finished attc area'}),
                        'enclosed_porch' => CommonHelper::decimalRemovalCharacter($house->enclosed_porch),
                        'bonus_room' => null,
                        'year_built' => strlen($year_built) > 4 ? null : $year_built,
                        'bed' => is_numeric($house->bed) ? ($house->bed > 99 ? 99 : $house->bed) : 0,
                        'bath' => is_numeric($house->bath) ? ($house->bath > 99 ? 99 : $house->bath) : 0,
                        'full_bath' => is_numeric($house->{'full bath'}) ? ($house->{'full bath'} > 99 ? 99 : $house->{'full bath'}) : 0,
                        'half_bath' => is_numeric($house->{'1_2 bath'}) ? ($house->{'1_2 bath'} > 99 ? 99 : $house->{'1_2 bath'}) : 0,
                        'three_quarter_bath' => is_numeric($house->{'3_4 bath'}) ? ($house->{'3_4 bath'} > 99 ? 99 : $house->{'3_4 bath'}) : 0,
                        'of_families' => is_numeric($house->{'# of families'}) ? $house->{'# of families'} : 0,
                        'of_kitchen' => CommonHelper::dbIsNumericValue255($house->{'# of kitchens'}),
                        'fireplaces' => strtolower($house->fireplaces) == 'yes' ? 1 : 0,
                        'subdivision' => $house->subdivision,

                        'ext_wall_type' => CommonHelper::searchSimilarMatch($house->{'ext wall type'}, config('property_information.ext_wall_type'), 0),
                        'roofing' => CommonHelper::searchSimilarMatch($house->roofing, config('property_information.roofing'), 0),
                        'ac' => CommonHelper::searchSimilarMatch($house->ac, config('property_information.ac_heating'), 9),
                        'heating' => CommonHelper::searchSimilarMatch($house->heating, config('property_information.ac_heating'), 9),

                        'pool' => $poolSpa,
                        'spa' => in_array(@$pool[1], $yes) ? 1 : 0,
                        'garages' => CommonHelper::dbIntValValue255($house->garage),
                        'garage_types' => 0,
                        'garage_sf' => CommonHelper::decimalRemovalCharacter($house->{'attached garage sf'}),
                        'lot_acreage_sf' => CommonHelper::decimalRemovalCharacter($house->{'lot size'}),
                        'stories' => 0,
                        // drive by

                        'property_type' => $property_type,
                        'specific_property_type' => $sptValue,
                        'building_style' => CommonHelper::searchSimilarMatch($house->{'building style'}, config('property_information.building_style'), 0),

                        'parcel_id1' => null,
                        //owner information mParcel1
                        'parcel_id2' => null,
                        //owner information mParcel1
                        'prc_url' => $house->{'county assessor link'},
                        'country_assessor_url' => null,
                        //forclosure information county assesor1 url
                        'gis_url' => null,
                        'treasurer_url' => '',
                        // forclosure information county teasurer1 url
                        'tax_bill_url' => '',
                        // forclosure information // county site url 2
                        'record_number' => null,
                        'choose_prc' => null,
                        'county_value' => null,
                        // forclosure information counvalue ,];

                    ];

                    $tempD[] = ['house_id' => $house_id,
                        'property_description' => $house->notesofCondition,
                        'legal_description' => $house->legalDescription,];

                    // property description and legal description
                    $temp[$house_id] = $insert;


                    $localRealStateData[$house_id]['house_id'] = $house_id;
                    $localRealStateData[$house_id]['zillow_url'] = $house->{'zillow site url'};
                    $localRealStateData[$house_id]['truila_url'] = $house->{'trulia site url'};
                    $localRealStateData[$house_id]['realtor_url'] = $house->{'realtor site url'};
                    $localRealStateData[$house_id]['redfin_url'] = $house->{'homes site url'};
                    $localRealStateData[$house_id]['beenverified_url'] = $house->{'movoto site url'};
                    $localRealStateData[$house_id]['har_url'] = $house->{'har_url'}; // home_info2
                    $localRealStateData[$house_id]['zestimate'] = null;
                    $localRealStateData[$house_id]['truila_est'] = null;
                    $localRealStateData[$house_id]['realtor_est'] = null;
                    $localRealStateData[$house_id]['redfin_est'] = null;

                    // Price History
                    $this->priceHistoryData($house_id, $house, $priceHistoryData);
                    //Schools & Neighborhood
                    $this->schoolAndNeighbourHood($house_id, $house, $schoolAndNeighbourHood);

                    if (!empty($house->{'property images url'})) {
                        $house_id = $house_id;
                        $pictureAndVideo[$house_id]['house_id'] = $house_id;
                        $pictureAndVideo[$house_id]['google_map_url'] = '';
                        $pictureAndVideo[$house_id]['image_url'] = $house->{'property images url'};
                        $pictureAndVideo[$house_id]['video_url'] = '';
                        $pictureAndVideo[$house_id]['video_type'] = ''; // Youtube,Vimeo,Dailymotion
                    }

                }

                // Foreclosure Information
                $this->foreclosureInfo($house_ids, $temp, $localRealStateData, $sale_details, $sale_details_descriptions, $sale_id, $house_id_sale_id);

                $this->bankInformation($house_ids, $assessmentTaxes, $mortgageLiens, $mortgage_id, $house_id_mortgage_id,

                    $mortgageOtherLiens, $mortgageHoaLiens);
                $this->driveByInformation($house_ids, $temp, $managerNotes);
                $this->ownerByInfo($house_ids, $temp, $ownerByInfo, $borrowerByInfo);
                $this->commonDocuments($house_ids, $property_document, $recorded_document, $house_id_sale_id, $owner_document

                    , $house_id_mortgage_id, $mortgageLienDocuments

                    , $mortgageOtherDocument, $mortgageHoaDocument);
                $geoData = $this->getGeo($house_ids, $pictureAndVideo);
                $this->pictureAndVideo($house_ids, $pictureAndVideo);

                //                echo '<pre>';
                //                print_r($sale_details);
                //                die;
                $transactionResult = DB::transaction(function () use (
                    $temp, $tempD, $geoData, $localRealStateData, $schoolAndNeighbourHood, $assessmentTaxes, $property_document, $recorded_document, $pictureAndVideo, $priceHistoryData, $managerNotes, $sale_details, $sale_details_descriptions, $ownerByInfo, $owner_document, $borrowerByInfo, $mortgageLiens, $mortgageLienDocuments, $mortgageOtherLiens, $mortgageHoaLiens, $mortgageOtherDocument, $mortgageHoaDocument

                ) {
                    Log::emergency("Total Property Information- " . count($temp));
                    PropertyModel::insert($temp);

                    Log::emergency("Total GeoData- " . count($geoData));
                    GeoModel::insert($geoData);

                    Log::emergency("Total Local Real State- " . count($localRealStateData));
                    LocalRealEstateModel::insert($localRealStateData);

                    Log::emergency("Total Price History - " . count($priceHistoryData));
                    PriceHistoryModel::insert($priceHistoryData);

                    Log::emergency("Total Schools & Neighborhood - " . count($schoolAndNeighbourHood));
                    SchoolNeighborhoodModel::insert($schoolAndNeighbourHood);

                    Log::emergency("Total Assessment & Taxes - " . count($assessmentTaxes));
                    AssessmentModel::insert($assessmentTaxes);

                    Log::emergency("Total Manager Notes - " . count($managerNotes));
                    ManagerNotesModel::insert($managerNotes);

                    #Recorded Document/Picture
                    Log::emergency("Total Document/Picture - " . count($property_document));
                    DocumentPropertyModel::insert($property_document);

                    # Picture and Video
                    Log::emergency("Total Picture And Video - " . count($pictureAndVideo));
                    MapVideoModel::insert($pictureAndVideo);


                    # Owner Info
                    Log::emergency("Total Owner - " . count($ownerByInfo));
                    OwnerModel::insert($ownerByInfo);

                    # Owner Document
                    Log::emergency("Total Document/Owner - " . count($owner_document));
                    DocumentOwnerModel::insert($owner_document);

                    # Borrower Info
                    Log::emergency("Total Borrower - " . count($borrowerByInfo));
                    BorrowerModel::insert($borrowerByInfo);

                    # Mortage Info,, Bank lien 1
                    Log::emergency("Total Mortage Liens - " . count($mortgageLiens));
                    $array_chunk = array_chunk($mortgageLiens, 1000, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total chunk - " . count($value));
                        MortgageLiensModel::insert($value);
                    }


                    #Mortgage Documents
                    Log::emergency("Total Mortgage Document - " . count($mortgageLienDocuments));
                    $array_chunk = array_chunk($mortgageLienDocuments, 1000, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total chunk - " . count($value));
                        DocumentMortgage::insert($value);
                    }

                    # Mortage Other
                    Log::emergency("Total Mortgage Other - " . count($mortgageOtherLiens));
                    MortgageOtherModel::insert($mortgageOtherLiens);

                    # Mortage Other Document
                    Log::emergency("Total Mortgage Other Document - " . count($mortgageOtherDocument));
                    DocumentMortgageOther::insert($mortgageOtherDocument);

                    # Mortage Hoa
                    Log::emergency("Total Mortgage Hoa- " . count($mortgageHoaLiens));
                    MortgageHoaModel::insert($mortgageHoaLiens);

                    # Mortage Other Document
                    Log::emergency("Total Mortgage HOA Document - " . count($mortgageHoaDocument));
                    DocumentMortgageHoa::insert($mortgageHoaDocument);


                });

                echo($transactionResult);
                Log::emergency("Total Records Completed - " . $counter);
            });


        Log::emergency("Final End of Records Completed - " . $chunkLimit);


    }

    public function singleInfo($houseId)
    {

        // Check key first before migration
        $key = $this->request->input('key');

        if ($key != "rati12" || empty($houseId)) {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        PropertyModel::where('house_id', '=', $houseId)->delete();

        $counter = 0;
        $chunkLimit = 1;
        $chunk = 2;

        // Last Sale Id
        $saleInfo = SaleDetailsModel::orderBy('sale_id', 'desc')->first();
        $sale_id = intval(@$saleInfo->sale_id);

        $mortgageInfo = MortgageLiensModel::orderBy('mortgage_id', 'desc')->first();
        $mortgage_id = intval(@$mortgageInfo->mortgage_id) + 1;

        Log::emergency("----------------House Id " . $houseId . "-------------- ");
        $this->print_mem();

        // check is deleted record
        $isDeletedRecord = true;

        DB::connection('olddb')
            ->table('home information')
            ->leftJoin('home_info2', 'house_id', '=', 'home information.house id')
            ->select('*')
            ->whereIn('home information.house id', [$houseId])
            ->where('is_deleted', '!=', 'yes')
            ->orderBy('home information.house id', 'asc')->chunk($chunk, function ($houses) use (
                &$counter, $chunkLimit, &$sale_id, &$mortgage_id, &$isDeletedRecord

            ) {
                $isDeletedRecord = false;

                Log::emergency("-----------------------Counter--" . $counter . "-------------------------- ");
                $this->print_mem();

                $temp = [];
                $tempD = [];
                $house_ids = [];
                $localRealStateData = [];
                $priceHistoryData = [];
                $schoolAndNeighbourHood = [];
                $assessmentTaxes = [];
                $managerNotes = [];
                $property_document = [];
                $recorded_document = [];
                $pictureAndVideo = [];
                $sale_details = [];
                $sale_details_descriptions = [];
                $house_id_sale_id = [];
                $ownerByInfo = [];
                $owner_document = [];
                $borrowerByInfo = [];
                $mortgageLiens = [];
                $house_id_mortgage_id = [];
                $mortgageLienDocuments = [];

                $mortgageOtherLiens = [];
                $mortgageOtherDocument = [];
                $mortgageHoaLiens = [];
                $mortgageHoaDocument = [];

                $counter = $counter + count($houses);

                if ($counter > $chunkLimit) return false;

                foreach ($houses as $house) {
                    $house_id = $house->{'house id'};
                    $house_ids[] = $house_id;


                    $pool = @explode('/', $house->{'pool/spa'});

                    $poolSpa = 9;

                    $yes = ['yes',
                        'Yes',
                        'Y',
                        'y'];
                    $no = ['no',
                        'NO',
                        'No',
                        'N',
                        'n'];
                    if (in_array(@$pool[0], $yes) && in_array(@$pool[1], $yes)) $poolSpa = 3;
                    else if (in_array(@$pool[0], $yes)) $poolSpa = 1;
                    else if (in_array(@$pool[1], $yes)) $poolSpa = 2;

                    $property_type = CommonHelper::searchSimilarMatch($house->{'property type'}, config('property_information.property_types'), 0);


                    $sptValue = 0;
                    if ($property_type != 0) {
                        $spt = config('property_information.specific_property_types');
                        $spt = $spt[$property_type][key($spt[$property_type])];
                        $sptValue = CommonHelper::searchSimilarMatch($house->{'specific pt'}, $spt, 0);
                    }


                    $year_built = CommonHelper::decimalRemovalCharacter($house->{'year built'});

                    $insert = ['house_id' => $house_id,
                        'zpid' => $house->zpid ? $house->zpid : 0,
                        'address' => $house->address,
                        'city' => $house->city,
                        'county' => $house->cCounty,
                        'state' => strlen($house->state) > 2 ? CommonHelper::searchIndexByValue($house->state, config('constants.states'), null) : $house->state,
                        'zip' => $house->zip,
                        'total_living_sqft' => CommonHelper::decimalRemovalCharacter($house->sq),
                        'cost_per_sqft' => CommonHelper::decimalRemovalCharacter($house->pricesq),
                        'total_sqft' => CommonHelper::decimalRemovalCharacter($house->total_sq),
                        'cost_sqft' => null,
                        //round($house->comp/$house->total_sq, 2),
                        'main_floor_area' => CommonHelper::decimalRemovalCharacter($house->{'main fl area'}),
                        'second_floor_area' => CommonHelper::decimalRemovalCharacter($house->second_floor),
                        'third_floor_area' => CommonHelper::decimalRemovalCharacter($house->{'upper fl area'}),
                        'basement_area' => CommonHelper::decimalRemovalCharacter($house->{'basement area'}),
                        'finished_basement_area' => CommonHelper::decimalRemovalCharacter($house->{'finished bsmt area'}),
                        'finished_attic' => CommonHelper::decimalRemovalCharacter($house->{'finished attc area'}),
                        'enclosed_porch' => CommonHelper::decimalRemovalCharacter($house->enclosed_porch),
                        'bonus_room' => null,
                        'year_built' => strlen($year_built) > 4 ? null : $year_built,
                        'bed' => is_numeric($house->bed) ? ($house->bed > 255 ? 255 : $house->bed) : 0,
                        'bath' => is_numeric($house->bath) ? ($house->bath > 99 ? 99 : $house->bath) : 0,
                        'full_bath' => is_numeric($house->{'full bath'}) ? ($house->{'full bath'} > 255 ? 255 : $house->{'full bath'}) : 0,
                        'half_bath' => is_numeric($house->{'1_2 bath'}) ? ($house->{'1_2 bath'} > 255 ? 255 : $house->{'1_2 bath'}) : 0,
                        'three_quarter_bath' => is_numeric($house->{'3_4 bath'}) ? ($house->{'3_4 bath'} > 255 ? 255 : $house->{'3_4 bath'}) : 0,
                        'of_families' => is_numeric($house->{'# of families'}) ? $house->{'# of families'} : 0,
                        'of_kitchen' => CommonHelper::dbIsNumericValue255($house->{'# of kitchens'}),
                        'fireplaces' => strtolower($house->fireplaces) == 'yes' ? 1 : 0,
                        'subdivision' => $house->subdivision,

                        'ext_wall_type' => CommonHelper::searchSimilarMatch($house->{'ext wall type'}, config('property_information.ext_wall_type'), 0),
                        'roofing' => CommonHelper::searchSimilarMatch($house->roofing, config('property_information.roofing'), 0),
                        'ac' => CommonHelper::searchSimilarMatch($house->ac, config('property_information.ac_heating'), 9),
                        'heating' => CommonHelper::searchSimilarMatch($house->heating, config('property_information.ac_heating'), 9),

                        'pool' => $poolSpa,
                        'spa' => in_array(@$pool[1], $yes) ? 1 : 0,
                        'garages' => CommonHelper::dbIntValValue255($house->garage),
                        'garage_types' => 0,
                        'garage_sf' => CommonHelper::decimalRemovalCharacter($house->{'attached garage sf'}),
                        'lot_acreage_sf' => CommonHelper::decimalRemovalCharacter($house->{'lot size'}),
                        'stories' => 0,
                        // drive by

                        'property_type' => $property_type,
                        'specific_property_type' => $sptValue,
                        'building_style' => CommonHelper::searchSimilarMatch($house->{'building style'}, config('property_information.building_style'), 0),

                        'parcel_id1' => null,
                        //owner information mParcel1
                        'parcel_id2' => null,
                        //owner information mParcel1
                        'prc_url' => $house->{'county assessor link'},
                        'country_assessor_url' => null,
                        //forclosure information county assesor1 url
                        'gis_url' => null,
                        'treasurer_url' => '',
                        // forclosure information county teasurer1 url
                        'tax_bill_url' => '',
                        // forclosure information // county site url 2
                        'record_number' => null,
                        'choose_prc' => null,
                        'county_value' => null,
                        // forclosure information counvalue ,];

                    ];


                    $tempD[] = ['house_id' => $house_id,
                        'property_description' => $house->notesofCondition,
                        'legal_description' => $house->legalDescription,];


                    // property description and legal description
                    $temp[$house_id] = $insert;


                    $localRealStateData[$house_id]['house_id'] = $house_id;
                    $localRealStateData[$house_id]['zillow_url'] = $house->{'zillow site url'};
                    $localRealStateData[$house_id]['truila_url'] = $house->{'trulia site url'};
                    $localRealStateData[$house_id]['realtor_url'] = $house->{'realtor site url'};
                    $localRealStateData[$house_id]['redfin_url'] = $house->{'homes site url'};
                    $localRealStateData[$house_id]['beenverified_url'] = $house->{'movoto site url'};
                    $localRealStateData[$house_id]['har_url'] = $house->{'har_url'}; // home_info2
                    $localRealStateData[$house_id]['zestimate'] = null;
                    $localRealStateData[$house_id]['truila_est'] = null;
                    $localRealStateData[$house_id]['realtor_est'] = null;
                    $localRealStateData[$house_id]['redfin_est'] = null;

                    // Price History
                    $this->priceHistoryData($house_id, $house, $priceHistoryData);
                    //Schools & Neighborhood
                    $this->schoolAndNeighbourHood($house_id, $house, $schoolAndNeighbourHood);

                    if (!empty($house->{'property images url'})) {
                        $house_id = $house_id;
                        $pictureAndVideo[$house_id]['house_id'] = $house_id;
                        $pictureAndVideo[$house_id]['google_map_url'] = '';
                        $pictureAndVideo[$house_id]['image_url'] = $house->{'property images url'};
                        $pictureAndVideo[$house_id]['video_url'] = '';
                        $pictureAndVideo[$house_id]['video_type'] = ''; // Youtube,Vimeo,Dailymotion
                    }

                }

                // Foreclosure Information
                $this->foreclosureInfo($house_ids, $temp, $localRealStateData, $sale_details, $sale_details_descriptions, $sale_id, $house_id_sale_id);

                $this->bankInformation($house_ids, $assessmentTaxes, $mortgageLiens, $mortgage_id, $house_id_mortgage_id,

                    $mortgageOtherLiens, $mortgageHoaLiens);
                $this->driveByInformation($house_ids, $temp, $managerNotes);
                $this->ownerByInfo($house_ids, $temp, $ownerByInfo, $borrowerByInfo);
                $this->commonDocuments($house_ids, $property_document, $recorded_document, $house_id_sale_id, $owner_document

                    , $house_id_mortgage_id, $mortgageLienDocuments

                    , $mortgageOtherDocument, $mortgageHoaDocument);

                $geoData = $this->getGeo($house_ids, $pictureAndVideo);
                $this->pictureAndVideo($house_ids, $pictureAndVideo);


                $transactionResult = DB::transaction(function () use (
                    $temp, $tempD, $geoData, $localRealStateData, $schoolAndNeighbourHood, $assessmentTaxes, $property_document, $recorded_document, $pictureAndVideo, $priceHistoryData, $managerNotes, $sale_details, $sale_details_descriptions, $ownerByInfo, $owner_document, $borrowerByInfo, $mortgageLiens, $mortgageLienDocuments, $mortgageOtherLiens, $mortgageHoaLiens, $mortgageOtherDocument, $mortgageHoaDocument

                ) {
                    Log::emergency("Total Property Information- " . count($temp));
                    PropertyModel::insert($temp);


                    Log::emergency("Total GeoData- " . count($geoData));
                    GeoModel::insert($geoData);

                    Log::emergency("Total Local Real State- " . count($localRealStateData));
                    LocalRealEstateModel::insert($localRealStateData);

                    Log::emergency("Total Price History - " . count($priceHistoryData));
                    PriceHistoryModel::insert($priceHistoryData);

                    Log::emergency("Total Schools & Neighborhood - " . count($schoolAndNeighbourHood));
                    SchoolNeighborhoodModel::insert($schoolAndNeighbourHood);

                    Log::emergency("Total Assessment & Taxes - " . count($assessmentTaxes));
                    AssessmentModel::insert($assessmentTaxes);

                    Log::emergency("Total Manager Notes - " . count($managerNotes));
                    ManagerNotesModel::insert($managerNotes);

                    #Recorded Document/Picture
                    Log::emergency("Total Document/Picture - " . count($property_document));
                    DocumentPropertyModel::insert($property_document);

                    # Picture and Video
                    Log::emergency("Total Picture And Video - " . count($pictureAndVideo));
                    MapVideoModel::insert($pictureAndVideo);


                    # Owner Info
                    Log::emergency("Total Owner - " . count($ownerByInfo));
                    OwnerModel::insert($ownerByInfo);

                    # Owner Document
                    Log::emergency("Total Document/Owner - " . count($owner_document));
                    DocumentOwnerModel::insert($owner_document);

                    # Borrower Info
                    Log::emergency("Total Borrower - " . count($borrowerByInfo));
                    BorrowerModel::insert($borrowerByInfo);

                    # Mortage Info,, Bank lien 1
                    Log::emergency("Total Mortage Liens - " . count($mortgageLiens));
                    $array_chunk = array_chunk($mortgageLiens, 1000, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total chunk - " . count($value));
                        MortgageLiensModel::insert($value);
                    }


                    #Mortgage Documents
                    Log::emergency("Total Mortgage Document - " . count($mortgageLienDocuments));
                    $array_chunk = array_chunk($mortgageLienDocuments, 1000, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total chunk - " . count($value));
                        DocumentMortgage::insert($value);
                    }

                    # Mortage Other
                    Log::emergency("Total Mortgage Other - " . count($mortgageOtherLiens));
                    MortgageOtherModel::insert($mortgageOtherLiens);

                    # Mortage Other Document
                    Log::emergency("Total Mortgage Other Document - " . count($mortgageOtherDocument));
                    DocumentMortgageOther::insert($mortgageOtherDocument);

                    # Mortage Hoa
                    Log::emergency("Total Mortgage Hoa- " . count($mortgageHoaLiens));
                    MortgageHoaModel::insert($mortgageHoaLiens);

                    # Mortage Other Document
                    Log::emergency("Total Mortgage HOA Document - " . count($mortgageHoaDocument));
                    DocumentMortgageHoa::insert($mortgageHoaDocument);


                });

                echo($transactionResult);
                Log::emergency("Total Records Completed - " . $counter);
            });

        if($isDeletedRecord)
        {
            Log::emergency("********Record has been deleted*********",["houseid"=>$houseId]);
            echo "<br/>********Record has been deleted*********".$houseId;
            return false;
        }

        Log::emergency("Final End of Records Completed - " . $chunkLimit);

        Log::emergency("---------------------picture-------------------");
        $this->picture($chunk, 500, $houseId);
        Log::emergency("---------------------cmaARVHomebuyer1-------------------");
        $this->cmaARVHomebuyer1($chunk, 500, $houseId); //
        // $this->propertyQueueList($chunk, $limit); // not on house id
        Log::emergency("---------------------propertyQueueListHouses-------------------");
        $this->propertyQueueListHouses($chunk, 500, $houseId); // not on house id
        Log::emergency("---------------------propertyDescription-------------------");
        $this->propertyDescription($chunk, 500, $houseId); // already added above
        Log::emergency("---------------------propertyUserFavourite-------------------");
        $this->propertyUserFavourite($chunk, 1000, $houseId); // not on house id
        Log::emergency("---------------------alarmMeProcess-------------------");
        $this->alarmMeProcess($chunk, 1000, $houseId); //
        Log::emergency("---------------------emailsAM-------------------");
        $this->emailsAM($chunk, 500, $houseId); //
        Log::emergency("---------------------sthbWholesaleBuyerN-------------------");
        $this->sthbWholesaleBuyerN($chunk, 500, $houseId); //
        Log::emergency("---------------------sthbWholesaleBuyerNTotal-------------------");
        $this->sthbWholesaleBuyerNTotal($chunk, 500, $houseId); //
        Log::emergency("---------------------migrateSaleDetailsBidder-------------------");
        $this->migrateSaleDetailsBidder($chunk, 500, $houseId);
        Log::emergency("---------------------sthbWholesaleBuyerStrategy-------------------");
        $this->sthbWholesaleBuyerStrategy($chunk, 500, $houseId);
        Log::emergency("---------------------propertyAcquisitionAtoB-------------------");
        $this->propertyAcquisitionAtoB($chunk, 500, $houseId); //
        Log::emergency("---------------------homeBuyersAlias2Darren-------------------");
        $this->homeBuyersAlias2Darren($chunk, 500, $houseId);
        Log::emergency("---------------------propertyAcquisitionBtoC-------------------");
        $this->propertyAcquisitionBtoC($chunk, 500, $houseId); //
        Log::emergency("---------------------invitations_info-------------------");
        $this->invitations_info($chunk, 500, $houseId);
        Log::emergency("---------------------house_buyit-------------------");
        $this->house_buyit($chunk, 500, $houseId);
        Log::emergency("---------------------lender_it_invite-------------------");
        $this->lender_it_invite($chunk, 500, $houseId);
        Log::emergency("---------------------accounting_document-------------------");
        $this->accounting_document($chunk, 500, $houseId);
        Log::emergency("---------------------average_days_market-------------------");
        $this->average_days_market($chunk, 500,$houseId);

    }

    public function migrationStepByStep()
    {
        // Check key first before migration
        $key = $this->request->input('key');
        $chunk = $this->request->input('chunk');
        $chunk = $chunk ? $chunk : 1000;

        $limit = $this->request->input('limit');
        $limit = $limit ? $limit : 10000;

        if ($key != "rati12") {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        if (env("APP_ENV") == "local11") {

        }

        $this->print_mem();
        $request_type = $this->request->input('request_type');

        if ($request_type == 'uuser') {
            $this->updateMigrateUserInfo();
        } else if ($request_type == '1') {
            $this->picture($chunk, $limit);
        }
        else if ($request_type == '2one') {
            $this->propertyQueueList($chunk, $limit); // One time run only
        }
        else if ($request_type == '3one') {
            $this->propertyQueueListHouses($chunk, $limit); // One time run only
        }
        else if ($request_type == '412') {
            $this->propertyDescription($chunk, $limit); //
        }
        else if ($request_type == '4') {
            $this->propertyUserFavourite($chunk, $limit); // delete all entries and make new entries
        }
        else if ($request_type == '5') {
            $this->alarmMeProcess($chunk, $limit); //
        }
        else if ($request_type == '6') {
            $this->cmaARVHomebuyer1($chunk, $limit); //
        }
        else if ($request_type == '7') {
            $this->emailsAM($chunk, $limit); //
        }
        else if ($request_type == '8') {
            $this->sthbWholesaleBuyerN($chunk, $limit); //
        }
        else if ($request_type == '9') {
            $this->sthbWholesaleBuyerNTotal($chunk, $limit); //
        }

        else if ($request_type == '10') {
            $this->migrateSaleDetailsBidder($chunk, $limit); //

        }
        else if ($request_type == '11') {
            $this->sthbWholesaleBuyerStrategy($chunk, $limit); //
        }
        else if ($request_type == '12') {
            $this->propertyAcquisitionAtoB($chunk, $limit);
            $this->homeBuyersAlias2Darren($chunk, $limit);
        }
        else if ($request_type == '13') {
            $this->propertyAcquisitionBtoC($chunk, $limit);
        }
        else if ($request_type == '14') {
            $this->invitations_info($chunk, $limit);
        }
        else if ($request_type == '15') {
            $this->house_buyit($chunk, $limit);
        }
        else if ($request_type == '16') {
            $this->contact_us($chunk, $limit);
        }
        else if ($request_type == '17') {
            $this->lender_it_invite($chunk, $limit); // One time run only after invitation info
        }
        else if ($request_type == '18') {
            $this->accounting_document($chunk, $limit); // One time run only after invitation info
        }
        else if ($request_type == '19') {
            $this->house_token($chunk, $limit); // One time run only after invitation info
        }
        else if ($request_type == '20') {
            $this->transferBraintreePaymentLog($chunk, $limit); // One time run only after User table fpr payment log
        }
        else if ($request_type == '21') {
            $this->average_days_market($chunk, $limit);
        }


        else {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

    }


    private function average_days_market($chunk, $limit, $houseId = "")
    {
        Log::emergency("----average_days_market--");

        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = AverageDaysMarketModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        $isDone = [];
        DB::connection('olddb')
            ->table('average_days_market')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('average_days_market.houseid', "=", $houseId);
                else
                    $q->where('average_days_market.houseid', ">", $parimaryId);
            })
            ->orderBy('average_days_market.houseid', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValueHistory = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {
                    //'wholesaleBuyer','1FinalCheckUrl','2FinalCheckUrl','3FinalCheckUrl'
                    $type = NULL;
                    if($tValue->{'type'} == 'wholesaleBuyer')
                        $type = 'wholesale_buyer';
                    else if($tValue->{'type'} == '1FinalCheckUrl')
                        $type = 'first_dtc';
                    else if($tValue->{'type'} == '2FinalCheckUrl')
                        $type = 'second_dca';
                    else if($tValue->{'type'} == '3FinalCheckUrl')
                        $type = 'third_dca';

                    $json = base64_decode($tValue->{'json'});
                    if (!$this->isuserExists($tValue->{'added_by'})) $tValue->{'added_by'} = null;
                    $tempInfo = [
                        //'id' => $id,
                        'house_id' => $tValue->{'houseid'},
                        'added_by' => $tValue->{'added_by'},
                        'json' => $json,
                        'type' => $type,
                        'created_at' => ($tValue->{'date_added'} != null ? strtotime($tValue->{'date_added'}):null),
                        'updated_at' => ($tValue->{'date_added'} != null ? strtotime($tValue->{'date_added'}):null),
                    ];
                    $tempValueHistory[] = $tempInfo;
                }

                DB::transaction(function () use (
                    $tempValueHistory
                ) {
                    Log::emergency("average_days_market  N Total List - " . count($tempValueHistory));
                    echo "<br/>average_days_market N total - " . count($tempValueHistory);
                    AverageDaysMarketModel::insert($tempValueHistory);

                });

                Log::emergency("AverageDaysMarketModel N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function house_token($chunk, $limit)
    {
        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = HouseTokenModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");

        $isDone = [];
        DB::connection('olddb')
            ->table('house_token')
            ->select([
                '*',
            ])->where('house_token.house_id', ">", $parimaryId)
            ->orderBy('house_token.house_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$isDone
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValueHistory = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {
                    $tempInfo = [
                        //'id' => $id,
                        'house_id' => $tValue->{'house_id'},
                        'token' => $tValue->{'public_token'},
                    ];
                    $tempValueHistory[] = $tempInfo;
                }


                DB::transaction(function () use (
                    $tempValueHistory
                ) {
                    Log::emergency("HouseTokenModel  N Total List - " . count($tempValueHistory));
                    echo "<br/>HouseTokenModel N total - " . count($tempValueHistory);
                    HouseTokenModel::insert($tempValueHistory);

                });

                Log::emergency("DocumentAccountingModel N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }


    public function migrationLast()
    {
        $config = [];
        $config['foreclosure_type_hoa']=array(
            'lomoteyventures@gmail.com',
            'barkoofz@gmail.com',
            'cgmcclure3@gmail.com',
            'dreamchasersrealty@gmail.com',
            'julius@yahoo.com',
            'southerestates@gmail.com',
            'mairajmohiuddin@yahoo.com',
            'hrjcustomdesign@gmail.com',
            'craig@theestates.com',
            'miket.estates@gmail.com',
            'velentahill@yahoo.com',
            'samr@theestates.com',
            'jamesjlovept@gmail.com',
            'estates@endoge.com',
            'realestatebalance101@gmail.com',
            'vjhill@ncsu.edu',
            'jfmorenom@yahoo.com',
            'rdbreithaupt@gmail.com'


        );
        $config['arizona_foreclosure_type_hoa']=array(
            "franklinjones1966@gmail.com"
        );
        $config['texas_foreclosure_type_hoa']=array(
            "stephanie.malek@icloud.com",
            "csw5252@gmail.com",
            "lwest707@gmail.com",
            "agaperealtysolutions@gmail.com",
            "taxlienbuyer@gmail.com",
            "cgmcclure3@gmail.com",
            "southerestates@gmail.com",
            "craig@theestates.com",
            "estates@endoge.com",
            "samr@theestates.com",
            "support@texasshowplaceproperties.com",
            "energyinspections@gmail.com",
            'realestatebalance101@gmail.com',
            'vjhill@ncsu.edu',
            'sjrealestate2019@yahoo.com',
            'geridelaney@delaneypropertyinvestments.com',
            'kingsa1@hotmail.com'
        );

        // Check key first before migration
        $key = $this->request->input('key');
        if ($key != "rati12") {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        HoaEmailsModel::where('id', '>', 0)->forceDelete();
        $this->print_mem();

        $transactionResult = DB::transaction(function () use ( $config) {

            // TX
            $where = [];
            $users = User::whereIn('email',$config['texas_foreclosure_type_hoa'])->get();

            $insert_batch = [];
            foreach ($users as $user)
            {
                $insert =[];
                $insert['user_id'] = $user->id;
                $insert['state'] = 'TX';
                //$insert['county'] = 'd0';
                $insert['created_at'] = strtotime('now');
                $insert['updated_at'] = strtotime('now');
                $insert_batch[]= $insert;
            }
            HoaEmailsModel::insert($insert_batch);
        });

        $transactionResult = DB::transaction(function () use ( $config) {
            // TX
            $where = [];
            $users = User::whereIn('email',$config['arizona_foreclosure_type_hoa'])->get();

            $insert_batch = [];
            foreach ($users as $user)
            {
                $insert =[];
                $insert['user_id'] = $user->id;
                $insert['state'] = 'AZ';
                //$insert['county'] = '';
                $insert['created_at'] = strtotime('now');
                $insert['updated_at'] = strtotime('now');
                $insert_batch[]= $insert;
            }
            HoaEmailsModel::insert($insert_batch);
        });

        $transactionResult = DB::transaction(function () use ( $config) {

            // TX
            $where = [];
            $users = User::whereIn('email',$config['foreclosure_type_hoa'])->get();

            $insert_batch = [];
            foreach ($users as $user)
            {
                $insert =[];
                $insert['user_id'] = $user->id;
                $insert['state'] = 'd0';
                $insert['county'] = 'd0';
                $insert['created_at'] = strtotime('now');
                $insert['updated_at'] = strtotime('now');
                $insert_batch[]= $insert;
            }
            HoaEmailsModel::insert($insert_batch);
        });


        Log::emergency("Final End of Records Completed - ");
        echo 'completed';



    }

    public function dailyUpdate($days)
    {
        // Check key first before migration
        $key = $this->request->input('key');
        $log_id = $this->request->input('log_id');

        if ($key != "rati12" || $days > 21) {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        $minlogid = '';


        if(!empty($log_id))
        {
             $minlogid = $log_id;
        }
        else
        {
            echo $sql = "SELECT min(log_id) as minlogid FROM `log_history` WHERE `log_by_date` > '".date('Y-m-d', strtotime('-'.$days.' days'))." 00:00:00' limit 0,1";

            $result = DB::connection('olddb')->select($sql);
            $minlogid = $result[0]->minlogid;
        }

        echo 'Current min log id '.$minlogid;

        if(intval($minlogid) < 1)
        {
            echo 'No records exists';die;
        }

        Log::info("----------------------minlogid---" . $minlogid . "-------------------------- ");

       // SELECT * FROM `` WHERE `` >= 14350649 and  != 0 and  not like  group by houseid ORDER BY `log_by_date` asc

       $result  = DB::connection('olddb')
            ->table('log_history')
            ->select([
                '*',
            ])
            ->where('log_id', ">=", $minlogid)
            ->where('houseid', "!=", 0)
            ->where('log_message', 'not like', 'Record open request%')
            ->orderBy('log_history.log_id', 'asc')

           ->get();


        Log::emergency("Queue: Working on  total records - " . $result->count());
        $in_array = [];
       foreach($result as $value)
       {
           DB::transaction(function () use (
               $value, &$in_array
           ) {
               if(in_array($value->houseid, $in_array))
               {
                    echo 'Skipping, this is already done.';
               }
               else{
                   $in_array[] = $value->houseid;
                   echo '<br/>log id ----------------------'.$value->log_id;
                   echo '<br/>House Id ----------------------'.$value->houseid;
                   $this->singleInfo($value->houseid);
               }

                //               if(count($in_array) > 5)
                //               {
                //                   var_dump($in_array); die;
                //               }

           });
       }


        Log::emergency("Queue: Completed   total records - " . $result->count());
        echo "Queue: Completed   total records - " . $result->count();
    }


    private function accounting_document($chunk, $limit, $houseId = "")
    {
        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = DocumentAccountingModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        $isDone = [];
        DB::connection('olddb')
            ->table('accounting documents')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('accounting documents.house id', "=", $houseId);
                else
                    $q->where('accounting documents.house id', ">", $parimaryId);
            })
           // ->where('accounting document.status','invite')
            ->orderBy('accounting documents.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$isDone
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValueHistory = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $id = $tValue->{'accounting doc id'};
                    if (!$this->isuserExists($tValue->{'added_by'})) $tValue->{'added_by'} = 0;

                    // Date parsing
                    $document_date = null;
                    $t    = substr($tValue->{'actual doc name'}, 0, 10);
                    $t    = str_replace(" ", "-", trim($t));
                    $temp = ((bool)strtotime($t));
                    if ($temp) {
                        $document_date = date('Y-m-d', strtotime($t));
                    }
                    else {
                        $t    = explode(".", $tValue->{'actual doc name'});
                        $t    = $t[0];
                        $t    = (strlen($t) > 10) ? substr($t, -10) : $t;
                        $t    = str_replace(" ", "-", trim($t));
                        $temp = ((bool)strtotime($t));
                        if ($temp) {
                            $document_date = date('Y-m-d', strtotime($t));
                        }
                    }

                    $tempInfo = [
                        //'id' => $id,
                        'house_id' => $tValue->{'house id'},
                        'added_by' => $tValue->{'added_by'},
                        'document_type' => 99,
                        'document_date' => CommonHelper::dbDateFormat($document_date),
                        'org_name' => $tValue->{'actual doc name'},
                        'store_name' => $tValue->{'store doc name'},
                        'created_at' => ($tValue->{'date_added'}),
                        'updated_at' => ($tValue->{'date_added'}),
                        'deleted_at' => ($tValue->{'deleted_on'} != null ? strtotime($tValue->{'deleted_on'}):null),
                    ];
                    $tempValueHistory[] = $tempInfo;
                }


                DB::transaction(function () use (
                    $tempValueHistory
                ) {
                    Log::emergency("DocumentAccountingModel  N Total List - " . count($tempValueHistory));
                    echo "<br/>DocumentAccountingModel N total - " . count($tempValueHistory);
                    DocumentAccountingModel::insert($tempValueHistory);

                });

                Log::emergency("DocumentAccountingModel N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    // one time event
    private function lender_it_invite($chunk, $limit, $houseId = "")
    {
        $counter = 0;
        $chunk = 100;
        $chunkLimit = 3000;
        $firsLast = InviteModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        $isDone = [];
        DB::connection('olddb')
            ->table('funder_lender_invite')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('funder_lender_invite.house_id', "=", $houseId);
                else
                {
                }
            })
            ->where('funder_lender_invite.status','invite')
            ->orderBy('funder_lender_invite.house_id', 'asc')
            ->orderBy('funder_lender_invite.funder_lender_invite_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$isDone, $houseId
            )
            {
                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {
                    $invitee_id = $tValue->{'funder_lender_invite_id'};
                    if (!$this->isuserExists($tValue->{'invitee_id'})) $tValue->{'invitee_id'} = null;
                    if (!$this->isuserExists($tValue->{'user_id'})) $tValue->{'user_id'} = null;


                    $invitees_info = array(
                        'house_id'        => $tValue->{'house_id'},
                        'address'         => ($tValue->{'address'}),
                        'invitee_to'      => $tValue->{'invitee_id'},
                        'invitee_from'    => $tValue->{'user_id'},
                        'invitee_email'   => $tValue->{'invitee_email'},
                        'invitee_subject' => $tValue->{'invitee_subject'},
                        'invitee_message' => $tValue->{'invitee_message'},
                        'created_at' => ($tValue->{'invite_date'}),
                        'updated_at' => ($tValue->{'invite_date'}),
                    );

                    if(!isset($isDone[$tValue->{'house_id'}.'_'.$invitee_id])){
                        $isDone[$tValue->{'house_id'}.'_'.$invitee_id] = 1;
                        $this->inviteService->updateOrCreate(['house_id'=>$tValue->{'house_id'}, 'invitee_to'=>$tValue->{'invitee_id'}], $invitees_info);
                    }
                    $this->inviteHistoryService->create($invitees_info);
                }

                Log::emergency("inviteService N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;




    }



    private function contact_us($chunk, $limit, $houseId = "")
    {
        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = ContactUs::orderBy('id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");

        DB::connection('olddb')
            ->table('contact_us')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('contact_us.id', "=", $houseId);
                else
                    $q->where('contact_us.id', ">", $parimaryId);
            })
            ->orderBy('contact_us.id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$isDone
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $tempValueHistory = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {


                    $tempInfo = [
                        'id' => $tValue->{'id'},
                        'your_name' => $tValue->{'first_name'},
                        'company' => $tValue->{'company_name'},
                        'email' => $tValue->{'email'},
                        'phone' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'phone'},18),
                        'message' => $tValue->{'message'},
                        'ip' => ($tValue->{'ip'}),
                        'user_id' => ($tValue->{'login_user'}),
                        'created_at' => strtotime($tValue->{'date_added'}),
                    ];

                    $tempValueHistory[] = $tempInfo;
                }

                DB::transaction(function () use (
                    $tempValue, $tempValueHistory
                ) {
                    Log::emergency("ContactUs  N Total List - " . count($tempValue));
                    echo "<br/>ContactUs N total - " . count($tempValue);
                    ContactUs::insert($tempValue);

                    Log::emergency("HouseBuyItHistoryModel  N Total List - " . count($tempValueHistory));
                    echo "<br/>ContactUs N total - " . count($tempValueHistory);
                    ContactUs::insert($tempValueHistory);
                });

                Log::emergency("ContactUs N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function house_buyit($chunk, $limit, $houseId = "")
    {
        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = HouseBuyItModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        $isDone = [];
        DB::connection('olddb')
            ->table('house_buyit')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('house_buyit.houseid', "=", $houseId);
                else
                    $q->where('house_buyit.houseid', ">", $parimaryId);
            })
            ->orderBy('house_buyit.houseid', 'asc')
            ->orderBy('house_buyit.id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$isDone
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $tempValueHistory = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $added_by = $tValue->{'added_by'};
                    if (!$this->isuserExists($tValue->{'added_by'})) $tValue->{'added_by'} = null;


                    $tempInfo = [
                        'house_id' => $tValue->{'houseid'},
                        'user_id' => $tValue->{'added_by'},
                        'position' => $tValue->{'position'},
                        'notes' => $tValue->{'notes'},
                        'question' => $tValue->{'question'},
                        'request_type' => $tValue->{'request_type'},
                        'created_at' => strtotime($tValue->{'date_added'}),
                        'updated_at' => strtotime($tValue->{'date_added'}),
                    ];

                    if(!isset($isDone[$tValue->{'houseid'}.'_'.$added_by])){
                        $tempValue[$tValue->{'houseid'}.'_'.$added_by] = $tempInfo;
                        $isDone[$tValue->{'houseid'}.'_'.$added_by] = 1;
                    }
                    $tempValueHistory[] = $tempInfo;
                }

            //                echo '<pre>';
            //                print_r($isDone);
            //                print_r($tempValue);
            //                print_r($tempValueHistory);

                DB::transaction(function () use (
                    $tempValue, $tempValueHistory
                ) {
                    Log::emergency("HouseBuyItModel  N Total List - " . count($tempValue));
                    echo "<br/>HouseBuyItModel N total - " . count($tempValue);
                    HouseBuyItModel::insert($tempValue);

                    Log::emergency("HouseBuyItHistoryModel  N Total List - " . count($tempValueHistory));
                    echo "<br/>HouseBuyItHistoryModel N total - " . count($tempValueHistory);
                    HouseBuyItHistoryModel::insert($tempValueHistory);
                });

                Log::emergency("HouseBuyItModel N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function invitations_info($chunk, $limit, $houseId = "")
    {
        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = InviteModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        $isDone = [];
        DB::connection('olddb')
            ->table('invitations_info')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('invitations_info.house_id', "=", $houseId);
                else
                    $q->where('invitations_info.house_id', ">", $parimaryId);
            })
            ->where('invitations_info.status','invite')
            ->orderBy('invitations_info.house_id', 'asc')
            ->orderBy('invitations_info.invite_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$isDone
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $tempValueHistory = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $invitee_id = $tValue->{'invitee_id'};
                    if (!$this->isuserExists($tValue->{'invitee_id'})) $tValue->{'invitee_id'} = null;
                    if (!$this->isuserExists($tValue->{'uid'})) $tValue->{'uid'} = null;

                    $tempInfo = [
                        'house_id' => $tValue->{'house_id'},
                        'address' => $tValue->{'address'},
                        'invitee_email' => $tValue->{'invitee_email'},
                        'invitee_subject' => $tValue->{'invitee_subject'},
                        'invitee_message' => $tValue->{'invitee_message'},
                        'invitee_to' => $tValue->{'invitee_id'},
                        'invitee_from' => $tValue->{'uid'},
                        'created_at' => strtotime($tValue->{'invited_on'}),
                        'updated_at' => strtotime($tValue->{'invited_on'}),
                    ];

                    if(!isset($isDone[$tValue->{'house_id'}.'_'.$invitee_id])){
                        $tempValue[$tValue->{'house_id'}.'_'.$invitee_id] = $tempInfo;
                        $isDone[$tValue->{'house_id'}.'_'.$invitee_id] = 1;
                    }
                    $tempValueHistory[] = $tempInfo;
                }


                DB::transaction(function () use (
                    $tempValue, $tempValueHistory
                ) {
                    Log::emergency("InviteModel  N Total List - " . count($tempValue));
                    echo "<br/>InviteModel N total - " . count($tempValue);
                    InviteModel::insert($tempValue);

                    Log::emergency("InviteHistoryModel  N Total List - " . count($tempValueHistory));
                    echo "<br/>InviteHistoryModel N total - " . count($tempValueHistory);
                    InviteHistoryModel::insert($tempValueHistory);
                });

                Log::emergency("HouseBuyItModel N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function sthbWholesaleBuyerNTotal($chunk, $limit, $houseId = "") {
        // No Data Loss in it ..

        // DELETE FROM  `hb project profits` where `house id` in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter    = 0;
        $chunkLimit = $limit;
        $last       = WholesaleBuyerNTotalModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('hb project profits')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('hb project profits.house id', "=", $houseId);
                else
                    $q->where('hb project profits.house id', ">", $parimaryId);
            })
            ->orderBy('hb project profits.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $counter   = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    //9999999999999999.99
                    $tempValue[] = [
                        'house_id'          => $tValue->{'house id'},
                        'est_amount'        => CommonHelper::decimalNumberLimit($tValue->{'est sthb total amount'}, 999999999999999.99, -999999999999999.99),
                        'est_percent'       => CommonHelper::decimalNumberLimit($tValue->{'est sthb total percent'}, 999999999999999.99, -999999999999999.99),
                        'est_profit'        => CommonHelper::decimalNumberLimit($tValue->{'est sthb total profit'}, 999999999999999.99, -999999999999999.99),
                        'est_payoff_amount' => CommonHelper::decimalNumberLimit($tValue->{'est sthb total payoff amount'}, 999999999999999.99, -999999999999999.99),
                        'act_amount'        => CommonHelper::decimalNumberLimit($tValue->{'act sthb total amount'}, 999999999999999.99, -999999999999999.99),
                        'act_percent'       => CommonHelper::decimalNumberLimit($tValue->{'act sthb total percent'}, 999999999999999.99, -999999999999999.99),
                        'act_profit'        => CommonHelper::decimalNumberLimit($tValue->{'act sthb total profit'}, 999999999999999.99, -999999999999999.99),
                        'act_payoff_amount' => CommonHelper::decimalNumberLimit($tValue->{'act sthb total payoff amount'}, 999999999999999.99, -999999999999999.99),
                    ];
                }

                DB::transaction(function () use (
                    $tempValue
                ) {
                    Log::emergency("Total STHB Total N List - " . count($tempValue));
                    echo "<br/>STHB N  total- " . count($tempValue);
                    WholesaleBuyerNTotalModel::insert($tempValue);
                });
                Log::emergency("Total STHB Total N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }
    private function propertyAcquisitionBtoC($chunk, $limit, $houseId = "")
    {
        // DELETE FROM `hb property sale` where `house id` in (select `house id` from `home information` where is_deleted = 'yes' )
        $counter = 0;
        $chunkLimit = $limit;
        $firsLast = PropertyAcquisitionBtoCFirstModel::orderBy('house_id', 'DESC')->first();
        $secondLast = PropertyAcquisitionBtoCSecondModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;

        if (!empty($firsLast)) $parimaryId = @$firsLast->house_id;

        if (!empty($secondLast)) {
            if($parimaryId < @$secondLast->house_id)
            $parimaryId = @$secondLast->house_id;
        }


        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('hb property sale')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('hb property sale.house id', "=", $houseId);
                else
                    $q->where('hb property sale.house id', ">", $parimaryId);
            })
            ->orderBy('hb property sale.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValueAB = [];
                $tempValueBC = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;


                $homeBuyerInfo2 = $this->gethomebuyer_info2($objectTemp);
                $hbPropertyAcquisition = $this->getHbPropertyAcquisition($objectTemp);

                foreach ($objectTemp as $tValue) {

                    $homeBuyerInfo2Object = $homeBuyerInfo2->get($tValue->{'house id'});
                    $homeBuyerInfo2Array = (array) $homeBuyerInfo2->get($tValue->{'house id'});
                    $hbPropertyAcquisitionObject = $hbPropertyAcquisition->get($tValue->{'house id'});

                    $cma_arv='';

                    if(!empty($homeBuyerInfo2Array['wholesale_cma']))
                        $cma_arv=$homeBuyerInfo2Array['wholesale_cma'];
                    elseif(!empty($homeBuyerInfo2Array['third_cma']))
                        $cma_arv=$homeBuyerInfo2Array['third_cma'];
                    elseif(!empty($homeBuyerInfo2Array['second_cma']))
                        $cma_arv=$homeBuyerInfo2Array['second_cma'];
                    elseif(!empty($homeBuyerInfo2Array['first_cma']))
                        $cma_arv=$homeBuyerInfo2Array['first_cma'];

                    if(!empty($tValue->{'est conservative contract sale price'}))
                    {
                        // $cma_arv = $tValue->{'est conservative contract sale price'};
                    }


                    $tempInfo = [
                        'house_id' => $tValue->{'house id'},

                        'cma_arv_est' => CommonHelper::decimalNumberLimit($cma_arv),
                        'cma_arv_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act conservative contract sale price'}),
                        'cma_arv_diff' => CommonHelper::hbDiff(@$cma_arv, @$tValue->{'act conservative contract sale price'}),
                        'cma_arv_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc conservative contract sale price'}),

                        'hud_fees_seller_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est seller HUD fees'}),
                        'hud_fees_seller_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act seller HUD fees'}),
                        'hud_fees_seller_diff' => CommonHelper::hbDiff(@$tValue->{'est seller HUD fees'}, @$tValue->{'act seller HUD fees'}),
                        'hud_fees_seller_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc seller HUD fees'}),

                        'add_taxes_paid_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est taxes paid in advance'}),
                        'add_taxes_paid_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act taxes paid in advance'}),
                        'add_taxes_paid_diff' => CommonHelper::hbDiff(@$tValue->{'est taxes paid in advance'}, @$tValue->{'act taxes paid in advance'}),
                        'add_taxes_paid_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc taxes paid in advance'}),

                        'percent_less_title_service_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est title service and insurance percent'}),
                        'percent_less_title_service_act' => CommonHelper::decimalNumberLimit(@$tValue->{'est title service and insurance percent'}),
                        'percent_less_title_service_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc title service and insurance percent'}),

                        'less_title_service_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est title service and insurance'}),
                        'less_title_service_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act title service and insurance'}),
                        'less_title_service_diff' => CommonHelper::hbDiff(@$tValue->{'est title service and insurance'}, @$tValue->{'act title service and insurance'}),
                        'less_title_service_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc title service and insurance'}),

                        'percent_less_owner_policy_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est policy paid percent'}),
                        'percent_less_owner_policy_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act policy paid percent'}),
                        'percent_less_owner_policy_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc policy paid percent'}),

                        'less_owner_policy_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est policy paid'}),
                        'less_owner_policy_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act policy paid'}),
                        'less_owner_policy_diff' => CommonHelper::hbDiff(@$tValue->{'est policy paid'}, @$tValue->{'act policy paid'}),
                        'less_owner_policy_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc policy paid'}),

                        'seller_cooncession_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est seller concessions'}),
                        'seller_cooncession_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act seller concessions'}),
                        'seller_cooncession_diff' => CommonHelper::hbDiff(@$tValue->{'est seller concessions'}, @$tValue->{'act seller concessions'}),
                        'seller_cooncession_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'sdsd'}),

                        'title_service_cls_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est title service closing fees'}),
                        'title_service_cls_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act title service closing fees'}),
                        'title_service_cls_diff' => CommonHelper::hbDiff(@$tValue->{'est title service closing fees'}, @$tValue->{'act title service closing fees'}),
                        'title_service_cls_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc title service closing fees'}),

                        'ins_utl_misc_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est utilities'}),
                        'ins_utl_misc_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act utilities'}),
                        'ins_utl_misc_diff' => CommonHelper::hbDiff(@$tValue->{'est utilities'}, @$tValue->{'act utilities'}),
                        'ins_utl_misc_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc utilities'}),

                        'percent_commission_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est commissions percent'}),
                        'percent_commission_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act commissions percent'}),
                        'percent_commission_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc commissions percent'}),

                        'commission_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est commissions'}),
                        'commission_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act commissions'}),
                        'commission_diff' => CommonHelper::hbDiff(@$tValue->{'est commissions'}, @$tValue->{'act commissions'}),
                        'commission_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc commissions'}),

                        'home_insurance_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est home insurance'}),
                        'home_insurance_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act home insurance'}),
                        'home_insurance_diff' => CommonHelper::hbDiff(@$tValue->{'est home insurance'}, @$tValue->{'act home insurance'}),
                        'home_insurance_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc home insurance'}),
                    ];

                    $tempInfoBC = [
                        'house_id' => $tValue->{'house id'},

                        'irs_tax_liens_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est sale irs tax'}),
                        'irs_tax_liens_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act sale irs tax'}),
                        'irs_tax_liens_diff' => CommonHelper::hbDiff(@$tValue->{'est sale irs tax'}, @$tValue->{'act sale irs tax'}),
                        'irs_tax_liens_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc sale irs tax'}),

                        'web_fee_est' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'est web fee'}),
                        'web_fee_act' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'act web fee'}),
                        'web_fee_diff' => CommonHelper::hbDiff(@$hbPropertyAcquisitionObject->{'est web fee'}, @$hbPropertyAcquisitionObject->{'act web fee'}),
                        'web_fee_calc' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'calc web fee'}),

                        'data_input_est' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'est data input'}),
                        'data_input_act' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'act data input'}),
                        'data_input_diff' => CommonHelper::hbDiff(@$hbPropertyAcquisitionObject->{'est data input'}, @$hbPropertyAcquisitionObject->{'act data input'}),
                        'data_input_calc' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'calc data input'}),

                        'accounting_services_est' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'est accounting services'}),
                        'accounting_services_act' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'act accounting services'}),
                        'accounting_services_diff' => CommonHelper::hbDiff(@$hbPropertyAcquisitionObject->{'est accounting services'}, @$hbPropertyAcquisitionObject->{'act accounting services'}),
                        'accounting_services_calc' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'calc accounting services'}),

                        'tvl_exp_gas_est' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'est other expenses'}),
                        'tvl_exp_gas_act' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'act other expenses'}),
                        'tvl_exp_gas_diff' => CommonHelper::hbDiff(@$hbPropertyAcquisitionObject->{'est other expenses'}, @$hbPropertyAcquisitionObject->{'act other expenses'}),
                        'tvl_exp_gas_calc' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionObject->{'calc other expenses'}),

                        'lender_cost_points_est' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'est_lender_cost_points'}),
                        'lender_cost_points_act' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'act_lender_cost_points'}),
                        'lender_cost_points_diff' => CommonHelper::hbDiff(@$homeBuyerInfo2Object->{'est_lender_cost_points'}, @$homeBuyerInfo2Object->{'act_lender_cost_points'}),
                        'lender_cost_points_calc' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'calc_lender_cost_points'}),

                        'lender_cost_interest_est' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'est_lender_cost_interest'}),
                        'lender_cost_interest_act' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'act_lender_cost_interest'}),
                        'lender_cost_interest_diff' => CommonHelper::hbDiff(@$homeBuyerInfo2Object->{'est_lender_cost_interest'}, @$homeBuyerInfo2Object->{'act_lender_cost_interest'}),
                        'lender_cost_interest_calc' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'calc_lender_cost_interest'}),

                        'title_first_one' => (@$tValue->{'other1 sale title'}),
                        'title_first_two' => CommonHelper::decimalNumberLimit(@$tValue->{'est sale other1'}),
                        'title_first_three' => CommonHelper::decimalNumberLimit(@$tValue->{'act sale other1'}),
                        'title_first_four' => CommonHelper::hbDiff(@$tValue->{'est sale other1'}, @$tValue->{'act sale other1'}),
                        'title_first_fifth' => CommonHelper::decimalNumberLimit(@$tValue->{'calc sale other1'}),

                        'title_second_one' => (@$tValue->{'other2 sale title'}),
                        'title_second_two' => CommonHelper::decimalNumberLimit(@$tValue->{'est sale other2'}),
                        'title_second_three' => CommonHelper::decimalNumberLimit(@$tValue->{'act sale other2'}),
                        'title_second_four' => CommonHelper::hbDiff(@$tValue->{'est sale other2'}, @$tValue->{'act sale other2'}),
                        'title_second_fifth' => CommonHelper::decimalNumberLimit(@$tValue->{'calc sale other2'}),

                        'legal_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est sale legal'}),
                        'legal_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act sale legal'}),
                        'legal_diff' => CommonHelper::hbDiff(@$tValue->{'est sale legal'}, @$tValue->{'act sale legal'}),
                        'legal_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc sale legal'}),

                        'is_manual_excise_tax' => CommonHelper::isBoolean($tValue->{'hb sale excise tax manual ip'}),
                        'excise_tax_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est sale excise tax'}),
                        'excise_tax_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act sale excise tax'}),
                        'excise_tax_diff' => CommonHelper::hbDiff(@$tValue->{'est sale excise tax'}, @$tValue->{'act sale excise tax'}),
                        'excise_tax_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc sale excise tax'}),

                        'county_tax_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est sale county tax'}),
                        'county_tax_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act sale county tax'}),
                        'county_tax_diff' => CommonHelper::hbDiff(@$tValue->{'est sale county tax'}, @$tValue->{'act sale county tax'}),
                        'county_tax_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc sale county tax'}),

                        'costs_est' => CommonHelper::decimalNumberLimit(@$tValue->{'est costs'}),
                        'costs_act' => CommonHelper::decimalNumberLimit(@$tValue->{'act costs'}),
                        'costs_diff' => CommonHelper::hbDiff(@$tValue->{'est costs'}, @$tValue->{'act costs'}),
                        'costs_calc' => CommonHelper::decimalNumberLimit(@$tValue->{'calc costs'}),

                        'total_est' => CommonHelper::decimalNumberLimitV2(@$tValue->{'est total'}, true),
                        'total_act' => CommonHelper::decimalNumberLimitV2(@$tValue->{'act total'}, true),
                        'total_diff' => CommonHelper::hbDiff(@$tValue->{'est total'}, @$tValue->{'act total'}),
                        'total_calc' => CommonHelper::decimalNumberLimitV2(@$tValue->{'calc total'}, true),

                        'net_spread_est' => CommonHelper::decimalNumberLimitV2(@$tValue->{'est net spread'}, true),
                        'net_spread_act' => CommonHelper::decimalNumberLimitV2(@$tValue->{'act net spread'}, true),
                        'net_spread_diff' => CommonHelper::hbDiff(@$tValue->{'est net spread'}, @$tValue->{'act net spread'}),
                        'net_spread_calc' => CommonHelper::decimalNumberLimitV2(@$tValue->{'calc net spread'}, true),

                        'net_profit_est' => CommonHelper::decimalNumberLimitV2(@$tValue->{'est net profit'}, true),
                        'net_profit_act' => CommonHelper::decimalNumberLimitV2(@$tValue->{'act net profit'}, true),
                        'net_profit_diff' => CommonHelper::hbDiff(@$tValue->{'est net profit'}, @$tValue->{'act net profit'}),
                        'net_profit_calc' => CommonHelper::decimalNumberLimitV2(@$tValue->{'calc net profit'}, true),

                        'total_cost_sell_b_to_c_est' => CommonHelper::decimalNumberLimitV2(@$tValue->{'est total sell cost2'}, true),
                        'total_cost_sell_b_to_c_act' => CommonHelper::decimalNumberLimitV2(@$tValue->{'act total sell cost2'}, true),
                        'total_cost_sell_b_to_c_diff' => CommonHelper::hbDiff(@$tValue->{'est total sell cost2'}, @$tValue->{'act total sell cost2'}),
                        'total_cost_sell_b_to_c_calc' => CommonHelper::decimalNumberLimitV2(@$tValue->{'calc total sell cost2'}, true),

                    ];

                    if ((count(array_filter($tempInfo))) > 1) $tempValueAB[] = $tempInfo;
                    if ((count(array_filter($tempInfoBC))) > 1) $tempValueBC[] = $tempInfoBC;
                }

//                echo '<pre>';
//                print_r($tempValueAB);
//                print_r($tempValueBC);

                DB::transaction(function () use (
                    $tempValueAB, $tempValueBC
                ) {
                    Log::emergency("PropertyAcquisitionBtoCFirstModel N Total List - " . count($tempValueAB));
                    echo "<br/>PropertyAcquisitionBtoCFirstModel N total - " . count($tempValueAB);
                    PropertyAcquisitionBtoCFirstModel::insert($tempValueAB);

                    Log::emergency("PropertyAcquisitionBtoCSecondModel - " . count($tempValueAB));
                    echo "<br/>PropertyAcquisitionBtoCSecondModel- " . count($tempValueAB);
                    PropertyAcquisitionBtoCSecondModel::insert($tempValueBC);

                });
                Log::emergency("Total property acquisition Total N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }


    private function homeBuyersAlias2Darren($chunk, $limit, $houseId = "")
    {
        // DELETE FROM `home_buyers_alias2_darren` where `houseid` in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter = 0;
        $chunkLimit = $limit;
        $last = HomeBuyersAlias2DarrenModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('home_buyers_alias2_darren')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('home_buyers_alias2_darren.houseid', "=", $houseId);
                else
                    $q->where('home_buyers_alias2_darren.houseid', ">", $parimaryId);
            })
            ->orderBy('home_buyers_alias2_darren.houseid', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];

                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $tempInfo = [
                        'house_id' => $tValue->{'houseid'},
                        'office_fee_check' => CommonHelper::isBoolean($tValue->{'office_fee_check'}),
                        'office_fee_date' => CommonHelper::dbDateFormat($tValue->{'office_fee_date'}),
                        'lmod_check' => CommonHelper::isBoolean($tValue->{'lmod_check'}),
                        'lmod_date' => CommonHelper::dbDateFormat($tValue->{'lmod_date'}),
                        'auction_check' => CommonHelper::isBoolean($tValue->{'auction_check'}),
                        'auction_date' => CommonHelper::dbDateFormat($tValue->{'auction_date'}),
                        'llc_check' => CommonHelper::isBoolean($tValue->{'llc_check'}),
                        'llc_date' => CommonHelper::dbDateFormat($tValue->{'llc_date'}),
                        'accounting_check' => CommonHelper::isBoolean($tValue->{'accounting_check'}),
                        'accounting_date' => CommonHelper::dbDateFormat($tValue->{'accounting_date'}),
                        'travel_check' => CommonHelper::isBoolean($tValue->{'travel_check'}),
                        'travel_date' => CommonHelper::dbDateFormat($tValue->{'travel_date'}),
                        'web_fee_check' => CommonHelper::isBoolean($tValue->{'web_fee_check'}),
                        'web_fee_check_date' => CommonHelper::dbDateFormat($tValue->{'web_fee_check_date'}),
                        'data_input_check' => CommonHelper::isBoolean($tValue->{'data_input_check'}),
                        'data_input_date' => CommonHelper::dbDateFormat($tValue->{'data_input_date'}),
                        'inspection_check' => CommonHelper::isBoolean($tValue->{'inspection_check'}),
                        'inspection_date' => CommonHelper::dbDateFormat($tValue->{'inspection_date'}),
                    ];

                    if ((count(array_filter($tempInfo))) > 1) $tempValue[] = $tempInfo;
                }

                // echo "<pre>";
                // print_r($objectTemp);
                //print_r($tempValue);


                DB::transaction(function () use (
                    $tempValue
                ) {
                    Log::emergency("home_buyers_alias2_darren N Total List - " . count($tempValue));
                    echo "<br/>property acquisition N total - " . count($tempValue);
                    HomeBuyersAlias2DarrenModel::insert($tempValue);

                });

                Log::emergency("Total home_buyers_alias2_darren Total N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function propertyAcquisitionAtoB($chunk, $limit, $houseId = "")
    {
        // DELETE FROM `hb property acquisition` where `house id` in (select `house id` from `home information` where is_deleted = 'yes' )
        $counter = 0;
        $chunkLimit = $limit;
        $last = PropertyAcquisitionAtoBFirstModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('hb property acquisition')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('hb property acquisition.house id', "=", $houseId);
                else
                    $q->where('hb property acquisition.house id', ">", $parimaryId);
            })
            ->orderBy('hb property acquisition.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValueAB = [];
                $tempValueBC = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;


                $homeBuyerInfo2 = $this->gethomebuyer_info2($objectTemp);
                $homeBuyersAlias1 = $this->getHome_buyers_alias1($objectTemp);

                foreach ($objectTemp as $tValue) {

                    $homeBuyerInfo2Object = $homeBuyerInfo2->get($tValue->{'house id'});
                    $homeBuyersAlias1Object = $homeBuyersAlias1->get($tValue->{'house id'});

                    $tempInfo = [
                        'house_id' => $tValue->{'house id'},
                        'is_manual_contract_purchase_price_est' => CommonHelper::isBoolean($tValue->{'est contract purchase price manual input'}),
                        'contract_purchase_price_est' => CommonHelper::decimalNumberLimit($tValue->{'est contract purchase price'}),
                        'is_manual_contract_purchase_price_act' => CommonHelper::isBoolean($tValue->{'act contract purchase price manual input'}),
                        'contract_purchase_price_act' => CommonHelper::decimalNumberLimit($tValue->{'act contract purchase price'}),
                        'contract_purchase_price_diff' => CommonHelper::hbDiff($tValue->{'est contract purchase price'}, $tValue->{'act contract purchase price'}),
                        'contract_purchase_price_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc contract purchase price'}),

                        'attorney_fees_litigation_est' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'hb_est_attorney_fees'}),
                        'attorney_fees_litigation_act' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'hb_act_attorney_fees'}),
                        'attorney_fees_litigation_diff' => CommonHelper::hbDiff(@$homeBuyerInfo2Object->{'hb_est_attorney_fees'}, @$homeBuyerInfo2Object->{'hb_act_attorney_fees'}),
                        'attorney_fees_litigation_calc' => CommonHelper::decimalNumberLimit(@$homeBuyerInfo2Object->{'hb_calc_attorney_fees'}),

                        'hud_fees_buyer_est' => CommonHelper::decimalNumberLimit($tValue->{'est buyer HUD fees'}),
                        'hud_fees_buyer_act' => CommonHelper::decimalNumberLimit($tValue->{'act buyer HUD fees'}),
                        'hud_fees_buyer_diff' => CommonHelper::hbDiff($tValue->{'est buyer HUD fees'}, $tValue->{'act buyer HUD fees'}),
                        'hud_fees_buyer_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc buyer HUD fees'}),

                        'lenders_title_insurance_est' => CommonHelper::decimalNumberLimit($tValue->{'est lender title insurance'}),
                        'lenders_title_insurance_act' => CommonHelper::decimalNumberLimit($tValue->{'act lender title insurance'}),
                        'lenders_title_insurance_diff' => CommonHelper::hbDiff($tValue->{'est lender title insurance'}, $tValue->{'act lender title insurance'}),
                        'lenders_title_insurance_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc lender title insurance'}),

                        'owner_title_insurance_est' => CommonHelper::decimalNumberLimit($tValue->{'est owner title insurance'}),
                        'owner_title_insurance_act' => CommonHelper::decimalNumberLimit($tValue->{'act owner title insurance'}),
                        'owner_title_insurance_diff' => CommonHelper::hbDiff($tValue->{'est owner title insurance'}, $tValue->{'act owner title insurance'}),
                        'owner_title_insurance_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc owner title insurance'}),

                        'recording_est' => CommonHelper::decimalNumberLimit($tValue->{'est recording'}),
                        'recording_act' => CommonHelper::decimalNumberLimit($tValue->{'act recording'}),
                        'recording_diff' => CommonHelper::hbDiff($tValue->{'est recording'}, $tValue->{'act recording'}),
                        'recording_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc recording'}),

                        'property_taxes_est' => CommonHelper::decimalNumberLimit($tValue->{'est property taxes'}),
                        'property_taxes_act' => CommonHelper::decimalNumberLimit($tValue->{'act property taxes'}),
                        'property_taxes_diff' => CommonHelper::hbDiff($tValue->{'est property taxes'}, $tValue->{'act property taxes'}),
                        'property_taxes_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc property taxes'}),

                        'office_fee_est' => CommonHelper::decimalNumberLimit($tValue->{'est mitigation on deposits'}),
                        'office_fee_act' => CommonHelper::decimalNumberLimit($tValue->{'act mitigation on deposits'}),
                        'office_fee_diff' => CommonHelper::hbDiff($tValue->{'est mitigation on deposits'}, $tValue->{'act mitigation on deposits'}),
                        'office_fee_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc mitigation on deposits'}),

                        'is_manual_loss_mitigation_on_deposits_est' => CommonHelper::isBoolean(@$homeBuyersAlias1Object->{'est mitigation loss manual ip'}),
                        'loss_mitigation_on_deposits_est' => CommonHelper::decimalNumberLimit(@$homeBuyersAlias1Object->{'est mitigation loss on deposits'}),
                        'loss_mitigation_on_deposits_act' => CommonHelper::decimalNumberLimit(@$homeBuyersAlias1Object->{'act mitigation loss on deposits'}),
                        'loss_mitigation_on_deposits_diff' => CommonHelper::hbDiff(@$homeBuyersAlias1Object->{'est mitigation loss on deposits'}, @$homeBuyersAlias1Object->{'act mitigation loss on deposits'}),
                        'loss_mitigation_on_deposits_calc' => CommonHelper::decimalNumberLimit(@$homeBuyersAlias1Object->{'calc mitigation loss on deposits'}),

                        'sale_fee_est' => CommonHelper::decimalNumberLimit($tValue->{'est auction fee'}),
                        'sale_fee_act' => CommonHelper::decimalNumberLimit($tValue->{'act auction fee'}),
                        'sale_fee_diff' => CommonHelper::hbDiff($tValue->{'est auction fee'}, $tValue->{'act auction fee'}),
                        'sale_fee_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc auction fee'}),

                        'llc_changes_est' => CommonHelper::decimalNumberLimit($tValue->{'est LLC changes'}),
                        'llc_changes_act' => CommonHelper::decimalNumberLimit($tValue->{'act LLC changes'}),
                        'llc_changes_diff' => CommonHelper::hbDiff($tValue->{'est LLC changes'}, $tValue->{'act LLC changes'}),
                        'llc_changes_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc LLC changes'}),

                        'utilities_est' => CommonHelper::decimalNumberLimit($tValue->{'est power'}),
                        'utilities_act' => CommonHelper::decimalNumberLimit($tValue->{'act power'}),
                        'utilities_diff' => CommonHelper::hbDiff($tValue->{'est power'}, $tValue->{'act power'}),
                        'utilities_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc power'}),

                        'is_manual_insurance_est' => CommonHelper::isBoolean(@$homeBuyerInfo2Object->{'hb_est_insurance_checkbox'}),
                        'insurance_est' => CommonHelper::decimalNumberLimit($tValue->{'est insurance'}),
                        'insurance_act' => CommonHelper::decimalNumberLimit($tValue->{'act insurance'}),
                        'insurance_diff' => CommonHelper::hbDiff($tValue->{'est insurance'}, $tValue->{'act insurance'}),
                        'insurance_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc insurance'}),

                        'wire_fees_est' => CommonHelper::decimalNumberLimit($tValue->{'est wire fees'}),
                        'wire_fees_act' => CommonHelper::decimalNumberLimit($tValue->{'act wire fees'}),
                        'wire_fees_diff' => CommonHelper::hbDiff($tValue->{'est wire fees'}, $tValue->{'act wire fees'}),
                        'wire_fees_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc wire fees'}),

                        'airport_transport_wire_est' => CommonHelper::decimalNumberLimit($tValue->{'est airport tranport wire'}),
                        'airport_transport_wire_act' => CommonHelper::decimalNumberLimit($tValue->{'act airport tranport wire'}),
                        'airport_transport_wire_diff' => CommonHelper::hbDiff($tValue->{'est airport tranport wire'}, $tValue->{'act airport tranport wire'}),
                        'airport_transport_wire_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc airport tranport wire'}),

                        'is_manual_excise_tax_nc_wire_est' => CommonHelper::isBoolean($tValue->{'hb excise tax manual ip'}),
                        'excise_tax_nc_wire_est' => CommonHelper::decimalNumberLimit($tValue->{'est excise tax NC'}),
                        'excise_tax_nc_wire_act' => CommonHelper::decimalNumberLimit($tValue->{'act excise tax NC'}),
                        'excise_tax_nc_wire_diff' => CommonHelper::hbDiff($tValue->{'est excise tax NC'}, $tValue->{'act excise tax NC'}),
                        'excise_tax_nc_wire_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc excise tax NC'}),
                    ];



                    $tempInfoBC = [
                        'house_id' => $tValue->{'house id'},

                        'house_construction_est' => CommonHelper::decimalNumberLimit($tValue->{'est house repairs'}),
                        'house_construction_act' => CommonHelper::decimalNumberLimit($tValue->{'act house repairs'}),
                        'house_construction_diff' => CommonHelper::hbDiff($tValue->{'est house repairs'}, $tValue->{'act house repairs'}),
                        'house_construction_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc house repairs'}),

                        'locks_est' => CommonHelper::decimalNumberLimit($tValue->{'est locks'}),
                        'locks_act' => CommonHelper::decimalNumberLimit($tValue->{'act locks'}),
                        'locks_diff' => CommonHelper::hbDiff($tValue->{'est locks'}, $tValue->{'act locks'}),
                        'locks_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc locks'}),

                        'eviction_est' => CommonHelper::decimalNumberLimit($tValue->{'est eviction'}),
                        'eviction_act' => CommonHelper::decimalNumberLimit($tValue->{'act eviction'}),
                        'eviction_diff' => CommonHelper::hbDiff($tValue->{'est eviction'}, $tValue->{'act eviction'}),
                        'eviction_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc eviction'}),

                        'irs_tax_liens_est' => CommonHelper::decimalNumberLimit($tValue->{'est irs tax'}),
                        'irs_tax_liens_act' => CommonHelper::decimalNumberLimit($tValue->{'act irs tax'}),
                        'irs_tax_liens_diff' => CommonHelper::hbDiff($tValue->{'est irs tax'}, $tValue->{'act irs tax'}),
                        'irs_tax_liens_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc irs tax'}),

                        'irs_tax_liens_date_est' => CommonHelper::dbDateFormat($tValue->{'irs tax liens date'}),
                        'irs_tax_liens_date_act' => NULL,
                        'irs_tax_liens_date_diff' => NULL,
                        'irs_tax_liens_date_calc' => NULL,

                        'attorney_closing_est' => CommonHelper::decimalNumberLimit($tValue->{'est attorney closing'}),
                        'attorney_closing_act' => CommonHelper::decimalNumberLimit($tValue->{'act attorney closing'}),
                        'attorney_closing_diff' => CommonHelper::hbDiff($tValue->{'est attorney closing'}, $tValue->{'act attorney closing'}),
                        'attorney_closing_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc attorney closing'}),

                        'lawn_care_est' => CommonHelper::decimalNumberLimit($tValue->{'est lawn care'}),
                        'lawn_care_act' => CommonHelper::decimalNumberLimit($tValue->{'act lawn care'}),
                        'lawn_care_diff' => CommonHelper::hbDiff($tValue->{'est lawn care'}, $tValue->{'act lawn care'}),
                        'lawn_care_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc lawn care'}),

                        'home_inspection_est' => CommonHelper::decimalNumberLimit($tValue->{'est home inspection'}),
                        'home_inspection_act' => CommonHelper::decimalNumberLimit($tValue->{'act home inspection'}),
                        'home_inspection_diff' => CommonHelper::hbDiff($tValue->{'est home inspection'}, $tValue->{'act home inspection'}),
                        'home_inspection_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc home inspection'}),

                        'inspection_fee_est' => CommonHelper::decimalNumberLimit($tValue->{'est inspection fee'}),
                        'inspection_fee_act' => CommonHelper::decimalNumberLimit($tValue->{'act inspection fee'}),
                        'inspection_fee_diff' => CommonHelper::hbDiff($tValue->{'est inspection fee'}, $tValue->{'act inspection fee'}),
                        'inspection_fee_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc inspection fee'}),

                        'title_first_one' => ($tValue->{'other1 title'}),
                        'title_first_two' => ($tValue->{'est other1'}),
                        'title_first_three' => ($tValue->{'act other1'}),
                        'title_first_four' => CommonHelper::hbDiff($tValue->{'est other1'}, $tValue->{'act other1'}),
                        'title_first_fifth' => ($tValue->{'calc other1'}),
                        'title_second_one' => ($tValue->{'other2 title'}),
                        'title_second_two' => ($tValue->{'est other2'}),
                        'title_second_three' => ($tValue->{'act other2'}),
                        'title_second_four' => CommonHelper::hbDiff($tValue->{'est other2'}, $tValue->{'act other2'}),
                        'title_second_fifth' => ($tValue->{'calc other2'}),

                        'legal_est' => CommonHelper::decimalNumberLimit($tValue->{'est legal'}),
                        'legal_act' => CommonHelper::decimalNumberLimit($tValue->{'act legal'}),
                        'legal_diff' => CommonHelper::hbDiff($tValue->{'est legal'}, $tValue->{'act legal'}),
                        'legal_calc' => ($tValue->{'calc legal'}),

                        'total_additional_cost_est' => CommonHelper::decimalNumberLimit($tValue->{'est total additional costs'}),
                        'total_additional_cost_act' => CommonHelper::decimalNumberLimit($tValue->{'act total additional costs'}),
                        'total_additional_cost_diff' => CommonHelper::hbDiff($tValue->{'est total additional costs'}, $tValue->{'act total additional costs'}),
                        'total_additional_cost_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc total additional costs'}),

                        'total_cost_to_buy_a_to_b_est' => CommonHelper::decimalNumberLimit($tValue->{'est total cost to sell'}),
                        'total_cost_to_buy_a_to_b_act' => CommonHelper::decimalNumberLimit($tValue->{'act total cost to sell'}),
                        'total_cost_to_buy_a_to_b_diff' => CommonHelper::hbDiff($tValue->{'est total cost to sell'}, $tValue->{'act total cost to sell'}),
                        'total_cost_to_buy_a_to_b_calc' => CommonHelper::decimalNumberLimit($tValue->{'calc total cost to sell'}),

                    ];

                    if ((count(array_filter($tempInfo))) > 1) $tempValueAB[] = $tempInfo;
                    if ((count(array_filter($tempInfoBC))) > 1) $tempValueBC[] = $tempInfoBC;
                }

// echo "<pre>";
// print_r($objectTemp);
// print_r($tempValueAB);
// print_r($tempValueBC);
// die;


                DB::transaction(function () use (
                    $tempValueAB, $tempValueBC
                ) {
                    Log::emergency("property acquisition N Total List - " . count($tempValueAB));
                    echo "<br/>property acquisition N total - " . count($tempValueAB);
                    PropertyAcquisitionAtoBFirstModel::insert($tempValueAB);

                    Log::emergency("PropertyAcquisitionAtoBSecondModel - " . count($tempValueAB));
                    echo "<br/>PropertyAcquisitionAtoBSecondModel- " . count($tempValueAB);
                    PropertyAcquisitionAtoBSecondModel::insert($tempValueBC);

                });
                Log::emergency("Total property acquisition Total N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function sthbWholesaleBuyerStrategy($chunk, $limit, $houseId = "")
    {
        //DELETE FROM `hb strategy` where `house id` in (select `house id` from `home information` where is_deleted = 'yes' )
        $counter = 0;
        $chunkLimit = $limit;
        $last = WholesaleBuyerStrategyModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->house_id;

        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('hb strategy')
            ->select([
                '*',
            ])
            ->leftJoin('homebuyer_info2', 'homebuyer_info2.house_id', '=', 'hb strategy.house id')
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('hb strategy.house id', "=", $houseId);
                else
                    $q->where('hb strategy.house id', ">", $parimaryId);
            })
            ->orderBy('hb strategy.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;


                $hbPropertyAcquisition = $this->getHbPropertyAcquisition($objectTemp);
                $hbPropertySale = $this->getHbPropertySale($objectTemp);
                $homeInfo2 = $this->getHome_info2($objectTemp);
                $homeBuyersAlias1 = $this->getHome_buyers_alias1($objectTemp);


                foreach ($objectTemp as $tValue) {

                    $hbPropertyAcquisitionSingleObject = $hbPropertyAcquisition->get($tValue->{'house id'});
                    $hbPropertyAcquisitionSingleArray = (array)$hbPropertyAcquisitionSingleObject;
                    $hbPropertySaleSingleObject = $hbPropertySale->get($tValue->{'house id'});
                    $homeInfo2SingleObject = $homeInfo2->get($tValue->{'house id'});
                    $homeBuyersAlias1SingleObject = $homeBuyersAlias1->get($tValue->{'house id'});

                    if (@$hbPropertyAcquisitionSingleArray['calc total cost to sell'] != '' && @$hbPropertyAcquisitionSingleArray['calc total cost to sell'] != 0)
                        $calc_total_cost_to_sell = @$hbPropertyAcquisitionSingleArray['calc total cost to sell'];
                    else if (@$hbPropertyAcquisitionSingleArray['est total cost to sell'] != '')
                        $calc_total_cost_to_sell = @$hbPropertyAcquisitionSingleArray['est total cost to sell'];
                    else
                        $calc_total_cost_to_sell = '';

                    if ($tValue->{'calc net payout to shortterm home buyers'} != '' && $tValue->{'calc net payout to shortterm home buyers'} != 0)
                        $calc_net_payout = $tValue->{'calc net payout to shortterm home buyers'};
                    else if ($tValue->{'est net payout to shortterm home buyers'} != '')
                        $calc_net_payout = $tValue->{'est net payout to shortterm home buyers'};
                    else
                        $calc_net_payout = '';

                    if ($calc_net_payout != '' && $calc_total_cost_to_sell != '' && $calc_total_cost_to_sell != 0)
                        $calc_project_rr = round(($calc_net_payout / $calc_total_cost_to_sell) * 100, 2);
                    else
                        $calc_project_rr = '';

                    if ($tValue->{'calc days diff'} != '')
                        $calc_days_diff = $tValue->{'calc days diff'};
                    else if ($tValue->{'est days diff'} != '')
                        $calc_days_diff = $tValue->{'est days diff'};
                    else
                        $calc_days_diff = '';

                    if ($calc_project_rr != '' && $calc_days_diff != '' && $calc_days_diff != 0)
                        $calc_anual_rr = round((($calc_project_rr / $calc_days_diff) * 365), 2);
                    else
                        $calc_anual_rr = '';

                    $tempInfo = [
                        'house_id' => $tValue->{'house id'},

                        'is_manual_close_date_a_to_b' => CommonHelper::isBoolean($tValue->{'calc close date1'}),
                        'est_close_date_a_to_b' => CommonHelper::dbDateFormat($tValue->{'est close date1'}),
                        'act_close_date_a_to_b' => CommonHelper::dbDateFormat($tValue->{'act close date1'}),
                        'calc_close_date_a_to_b' => CommonHelper::dbDateFormat(($tValue->{'calc close date1'})),

                        'is_manual_close_date_b_to_c' => CommonHelper::isBoolean($tValue->{'calc close date2'}),
                        'est_close_date_b_to_c' => CommonHelper::dbDateFormat($tValue->{'est close date2'}),
                        'act_close_date_b_to_c' => CommonHelper::dbDateFormat($tValue->{'act close date2'}),
                        'calc_close_date_b_to_c' => CommonHelper::dbDateFormat($tValue->{'calc close date2'}),

                        'est_days_start_to_finish' => CommonHelper::decimalNumberLimit($tValue->{'est days diff'}, 9999999999.99, -9999999999.99),
                        'act_days_start_to_finish' => CommonHelper::decimalNumberLimit($tValue->{'act days diff'}, 9999999999.99, -9999999999.99),
                        'calc_days_start_to_finish' => CommonHelper::decimalNumberLimit($tValue->{'calc days diff'}, 9999999999.99, -9999999999.99),

                        'est_days_on_market' => CommonHelper::decimalNumberLimit($tValue->{'diff_day_of_market'}, 9999999999.99, -9999999999.99),
                        'act_days_on_market' => 0,
                        'calc_days_on_market' => 0,


                        'est_prp_rate_of_return' => CommonHelper::decimalNumberLimit($tValue->{'est project return rate'}, 9999999999.99, -9999999999.99),
                        'act_prp_rate_of_return' => CommonHelper::decimalNumberLimit($tValue->{'act project return rate'}, 9999999999.99, -9999999999.99),
                        'calc_prp_rate_of_return' => CommonHelper::decimalNumberLimit($calc_project_rr, 9999999999.99, -9999999999.99),

                        'est_ann_return_aft_fnl_close' => CommonHelper::decimalNumberLimit($tValue->{'est annualized return after final close'}, 9999999999.99, -9999999999.99),
                        'act_ann_return_aft_fnl_close' => CommonHelper::decimalNumberLimit($tValue->{'act annualized return after final close'}, 9999999999.99, -9999999999.99),
                        'calc_ann_return_aft_fnl_close' => CommonHelper::decimalNumberLimit($calc_anual_rr, 9999999999.99, -9999999999.99),

                        'est_total_cost_to_buy_a_to_b' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionSingleObject->{'est total cost to sell'}, 9999999999.99, -9999999999.99),
                        'act_total_cost_to_buy_a_to_b' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionSingleObject->{'act total cost to sell'}, 9999999999.99, -9999999999.99),
                        'calc_total_cost_to_buy_a_to_b' => CommonHelper::decimalNumberLimit(@$hbPropertyAcquisitionSingleObject->{'calc total cost to sell'}, 9999999999.99, -9999999999.99),

                        'est_total_cost_to_buy_b_to_c' => CommonHelper::decimalNumberLimit(@$hbPropertySaleSingleObject->{'est total sell cost2'}, 9999999999.99, -9999999999.99),
                        'act_total_cost_to_buy_b_to_c' => CommonHelper::decimalNumberLimit(@$hbPropertySaleSingleObject->{'act total sell cost2'}, 9999999999.99, -9999999999.99),
                        'calc_total_cost_to_buy_b_to_c' => CommonHelper::decimalNumberLimit(@$hbPropertySaleSingleObject->{'calc total sell cost2'}, 9999999999.99, -9999999999.99),

                        'est_net_profit' => CommonHelper::decimalNumberLimit(@$hbPropertySaleSingleObject->{'est net profit'}, 9999999999.99, -9999999999.99),
                        'act_net_profit' => CommonHelper::decimalNumberLimit(@$hbPropertySaleSingleObject->{'act net profit'}, 9999999999.99, -9999999999.99),
                        'calc_net_profit' => CommonHelper::decimalNumberLimit(@$hbPropertySaleSingleObject->{'calc net profit'}, 9999999999.99, -9999999999.99),


                        'net_payout_per' => CommonHelper::decimalNumberLimit(@$homeInfo2SingleObject->{'net_payout_per'}, 9999999999.99, -9999999999.99),
                        'est_payout_split' => CommonHelper::decimalNumberLimit($tValue->{'est net payout to shortterm home buyers'}, 9999999999.99, -9999999999.99),
                        'act_payout_split' => CommonHelper::decimalNumberLimit($tValue->{'act net payout to shortterm home buyers'}, 9999999999.99, -9999999999.99),
                        'calc_payout_split' => CommonHelper::decimalNumberLimit($tValue->{'calc net payout to shortterm home buyers'}, 9999999999.99, -9999999999.99),

                        'net_payout_founder' => CommonHelper::decimalNumberLimit(@$homeInfo2SingleObject->{'net_payout_funder'}, 9999999999.99, -9999999999.99),
                        'est_net_payout' => CommonHelper::decimalNumberLimit($tValue->{'net_payout_estimate'}, 9999999999.99, -9999999999.99),
                        'act_net_payout' => CommonHelper::decimalNumberLimit($tValue->{'net_payout_actual'}, 9999999999.99, -9999999999.99),
                        'calc_net_payout' => CommonHelper::decimalNumberLimit($tValue->{'net_hb_calc_payout'}, 9999999999.99, -9999999999.99),

                        'date_listed' => CommonHelper::dbDateFormat($tValue->{'date_listed'}),
                        'date_under_contract' => CommonHelper::dbDateFormat($tValue->{'date_under_contract'}),
                        'date_sold' => CommonHelper::dbDateFormat($tValue->{'date_sold'}),
                        'actual_days_on_market' => CommonHelper::dbDateFormat($tValue->{'actual_date_on_market'}),
                        'lf_dead_property' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb dead property'}),
                        'lf_wo_auction_outbid' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb auction outbid'}),
                        'upst_auction_no_bid' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb auction no bid'}),
                        'potential_buy' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb potential buy'}),
                        'property_in_escrow' => CommonHelper::isBoolean($tValue->{'hb property closing'}),
                        'list_and_flip' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'list and flip'}),
                        'bid_offer_on_property' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb bid on property'}),
                        'property_closed' => CommonHelper::isBoolean($tValue->{'hb property closed'}),
                        'property_closed_date' => CommonHelper::dbDateFormat($tValue->{'hb property closed date'}),
                        'total_days_to_sell' => CommonHelper::decimalNumberLimit($tValue->{'hb total days to sell'}, 9999999999.99, -9999999999.99),
                        'purchased_deed' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'purchase_deed'}),
                        'bidding_in_process' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb bidding in process'}),
                        'assignment' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'assignment'}),
                        'bid_offer_confirmed' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'hb bid confirmed'}),
                        'deposit_to_be_returned' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'deposit to be returned'}),
                        'exclusive_agency' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'exclusive agency'}),
                        'off_site_or_no_sale' => CommonHelper::isBoolean(@$homeBuyersAlias1SingleObject->{'off_site'}),
                        'property_purchased_acq_a_to_b' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb property purchased'}),
                        'dead_property' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb dead property'}),
                        'attended_sale_outbid' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb auction outbid'}),
                        'attended_sale_no_bid' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb auction no bid'}),
                        'property_not_purchased' => CommonHelper::isBoolean(@$hbPropertyAcquisitionSingleObject->{'hb property not purchased'}),

                    ];


                    if ((count(array_filter($tempInfo))) > 1) $tempValue[] = $tempInfo;
                }

// echo "<pre>";
// print_r($objectTemp);
// print_r($hbPropertyAcquisitionSingleObject);
// print_r(@$homeBuyersAlias1SingleObject);
// print_r($tempValue);
// die;

                DB::transaction(function () use (
                    $tempValue
                ) {
                    Log::emergency("Total WholesaleBuyerStrategyModel Total N List - " . count($tempValue));
                    echo "<br/>WholesaleBuyerStrategyModel N total- " . count($tempValue);
                    WholesaleBuyerStrategyModel::insert($tempValue);
                });
                Log::emergency("Total WholesaleBuyerStrategyModel Total N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function gethomebuyer_info2($records)
    {
        $result = DB::connection('olddb')
            ->table('homebuyer_info2')
            ->select([
                '*',
            ])
            ->whereIn('house_id', $records->pluck('house id')->toArray())
            ->orderBy('homebuyer_info2.house_id', 'asc')->get();

        $temp = $result->keyBy('house_id');
        return $temp;
    }


    private function getHome_buyers_alias1($records)
    {
        $result = DB::connection('olddb')
            ->table('home_buyers_alias1')
            ->select([
                '*',
            ])
            ->whereIn('house id', $records->pluck('house id')->toArray())
            ->orderBy('home_buyers_alias1.house id', 'asc')->get();

        $temp = $result->keyBy('house id');
        // print_r(($temp->get('18')->{'house id'}));
        return $temp;
    }

    private function getHome_info2($records)
    {
        $result = DB::connection('olddb')
            ->table('home_info2')
            ->select([
                '*',
            ])
            ->whereIn('house_id', $records->pluck('house id')->toArray())
            ->orderBy('home_info2.house_id', 'asc')->get();

        $temp = $result->keyBy('house_id');
        // print_r(($temp->get('18')->{'house id'}));
        return $temp;
    }

    private function getHbPropertyAcquisition($records)
    {

        $result = DB::connection('olddb')
            ->table('hb property acquisition')
            ->select([
                '*',
            ])
            ->whereIn('house id', $records->pluck('house id')->toArray())
            ->orderBy('hb property acquisition.house id', 'asc')->get();

        $temp = $result->keyBy('house id');
        return $temp;
    }

    private function getHbPropertySale($records)
    {

        $result = DB::connection('olddb')
            ->table('hb property sale')
            ->select([
                '*',
            ])
            ->whereIn('house id', $records->pluck('house id')->toArray())
            ->orderBy('hb property sale.house id', 'asc')->get();

        $temp = $result->keyBy('house id');
        return $temp;
    }

    //


    private function removeLastHouseIdArray($array)
    {
        $temp = $array;

        $lastPop = array_pop($temp);

        $house_id = $lastPop['house_id'];
        while ($lastPop = array_pop($temp)) {
            // if house is is not same, re assign lastPop and break loop
            if ($house_id != $lastPop['house_id']) {
                $temp[] = $lastPop;
                break;
            }
        }

        if (count($temp) < 1) {
            return $array;
        }

        return $temp;
    }





    public function migrateUserInfo()
    {


        $last = User::orderBy('id', 'DESC')->first();
        $uid = 0;
        if (!empty($last)) $uid = $last->id;

        $this->updateMigrateUserInfo($uid);
    }

    public function updateMigrateUserInfo($uid = 0)
    {

        $userSqlBy = DB::connection('olddb')
            ->table('user_details')
            ->select('*')
            ->where('uid', '>', $uid)
            ->orderBy('user_details.uid', 'asc')->get();
        Log::emergency("DB user By - " . count($userSqlBy));

        // get all roles with id
        $roles = array();
        $roles_ref = RolesModel::all()->map(function ($item) use (&$roles) {
            $roles[$item->role_key] = $item->toArray();
        });


        if ($userSqlBy != null) {
            foreach ($userSqlBy as $tkey => $tvalue) {

                $userInfo = [];
                $userInfo[$tvalue->uid]['id'] = $tvalue->uid;

                if ($tvalue->uid == 855) {
                    $uuid = 'craig+homebuyer1@theestates.com';
                    $userInfo[$tvalue->uid]['email'] = CommonHelper::parseEmailJunk($uuid, false, false);
                    $userInfo[$tvalue->uid]['username'] = CommonHelper::parseEmailJunk($uuid, false, false);
                } else if ($tvalue->uid == 10 || $tvalue->uid == 1) {
                    $uuid = $tvalue->email;
                    $userInfo[$tvalue->uid]['email'] = CommonHelper::parseEmailJunk($uuid, true);
                    $userInfo[$tvalue->uid]['username'] = CommonHelper::parseEmailJunk($uuid, true);
                } else {
                    $userInfo[$tvalue->uid]['email'] = CommonHelper::parseEmailJunk($tvalue->email, false, false);
                    $userInfo[$tvalue->uid]['username'] = CommonHelper::parseEmailJunk($tvalue->username, false, false);
                }

                $userInfo[$tvalue->uid]['password'] = $tvalue->password;
                $userInfo[$tvalue->uid]['master_password'] = $tvalue->master_password;
                $userInfo[$tvalue->uid]['current_role'] = '';

                if ($tvalue->status == 'pending' || $tvalue->status == 'inactive') {
                    $status = 'pending';
                } else {
                    $status = $tvalue->status;
                }


                $userInfo[$tvalue->uid]['status'] = $status;
                $userInfo[$tvalue->uid]['is_payed'] = strtolower($tvalue->is_payed) == 'yes' ? 1 : 0;
                $userInfo[$tvalue->uid]['is_agree'] = $tvalue->is_agree;
                $userInfo[$tvalue->uid]['is_popup'] = $tvalue->is_popup;
                $userInfo[$tvalue->uid]['first_name'] = $tvalue->first_name;
                $userInfo[$tvalue->uid]['last_name'] = $tvalue->last_name;
                $userInfo[$tvalue->uid]['address'] = $tvalue->user_address;
                $userInfo[$tvalue->uid]['city'] = $tvalue->user_city;
                //$userInfo[$tvalue->uid]['state'] = null;
                $userInfo[$tvalue->uid]['mobile'] = $tvalue->user_mobile;
                //$userInfo[$tvalue->uid]['last_login'] = null;
                $userInfo[$tvalue->uid]['created_at'] = date('Y-m-d H:i:s', $tvalue->joined_on);
                $userInfo[$tvalue->uid]['updated_at'] = date('Y-m-d H:i:s', $tvalue->joined_on);


                // User roles List
                //$roles_ = include dirname(__FILE__).'/../../config/constants.php';// $in;
                // $roles = array("admin"=>"Admin","web_team"=>"Web Team")+$roles_['roles_es']+$roles_['roles_buyer'];

                $inser_user_roles = [];
                $current_role = 'first_dtc';
                if ($tvalue->is_home_buyer == 'yes') {
                    $current_role = 'home_buyer';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['home_buyer']['id']];
                }
                if ($tvalue->is_wholesale == 'yes') {
                    $current_role = 'wholesale_buyer';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['wholesale_buyer']['id']];
                }
                if ($tvalue->is_funder_lender == 'yes') {
                    $current_role = 'fund_lander';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['fund_lander']['id']];
                }
                if ($tvalue->is_im == 'yes') {
                    $current_role = 'im_by';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['im_by']['id']];
                }
                if ($tvalue->is_nos == 'yes') {
                    $current_role = 'nos_by';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['nos_by']['id']];
                }

                if ($tvalue->is_data_input == 'yes') {
                    $current_role = 'first_dtc';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['first_dtc']['id']];
                }
                if ($tvalue->is_dca == 'yes') {
                    $current_role = 'second_dca';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['second_dca']['id']];
                }
                if ($tvalue->is_dpb == 'yes') {
                    $current_role = 'third_dca';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['third_dca']['id']];
                }

                if ($tvalue->is_admin == 'yes') {
                    $current_role = 'admin';
                    $inser_user_roles[] = ['user_id' => $tvalue->uid, 'role_id' => $roles['admin']['id']];
                }

                $userInfo[$tvalue->uid]['current_role'] = $current_role;


                $transactionResult = DB::transaction(function () use ($userInfo, $tvalue, $inser_user_roles) {

                    Log::emergency("Total Users : " . count($userInfo));

                    User::updateOrCreate(
                        ['id' => $tvalue->uid],
                        $userInfo[$tvalue->uid]
                    );

                    foreach ($inser_user_roles as $key => $value) {
                        UserRolesModel::firstOrCreate($value);
                    }

                });

                //`uid`, ``, ``, ``, ``, ``, ``,
                // ``, ``, ``,
                // ``, ``, ``, ``,
                // ``, ``, ``, ``, ``, ``, ``, `nc_region`, `user_county`, ``, ``, ``, `price`, `user_state`
            }

        }
    }

    function print_mem()
    {
        /* Currently used memory */
        $mem_usage = memory_get_usage();

        /* Peak memory usage */
        $mem_peak = memory_get_peak_usage();

        $str1 = 'The script is now using: <strong>' . round($mem_usage / (1024 * 1024)) . 'MB</strong> of memory.<br>';
        $str2 = 'Peak usage: <strong>' . round($mem_peak / (1024 * 1024)) . 'MB</strong> of memory.<br><br>';

        Log::emergency("-------------------------------------");
        Log::emergency($str1);
        Log::emergency($str2);
        Log::emergency("-------------------------------------");


    }

    private function priceHistoryData($house_id, $house, &$priceHistoryData)
    {
        $temp = [];
        $ph_description = config('property_information.ph_description');

        $temp['house_id'] = $house_id;
        $temp['price_date'] = CommonHelper::dbDateFormat($house->{'ph1 date'});
        $temp['price'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 price'});
        // Not In Use $priceHistoryData['sdsd'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 change'};
        $temp['cost_per_sqft'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 pricesq'});
        $temp['source'] = $house->{'ph1 source'};
        $temp['description'] = CommonHelper::searchSimilarMatch($house->{'ph1 description'}, $ph_description, 0);
        if ((count(array_filter($temp))) > 1) $priceHistoryData[] = $temp;


        $temp = [];
        $temp['house_id'] = $house_id;
        $temp['price_date'] = CommonHelper::dbDateFormat($house->{'ph2 date'});
        $temp['price'] = CommonHelper::decimalRemovalCharacter($house->{'ph2 price'});
        // Not In Use $priceHistoryData['sdsd'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 change'};
        $temp['cost_per_sqft'] = CommonHelper::decimalRemovalCharacter($house->{'ph2 pricesq'});
        $temp['source'] = $house->{'ph2 source'};
        $temp['description'] = CommonHelper::searchSimilarMatch($house->{'ph2 description'}, $ph_description, 0);

        if ((count(array_filter($temp))) > 1) $priceHistoryData[] = $temp;

        $temp = [];
        $temp['house_id'] = $house_id;
        $temp['price_date'] = CommonHelper::dbDateFormat($house->{'ph3 date'});
        $temp['price'] = CommonHelper::decimalRemovalCharacter($house->{'ph3 price'});
        // Not In Use $priceHistoryData['sdsd'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 change'};
        $temp['cost_per_sqft'] = CommonHelper::decimalRemovalCharacter($house->{'ph3 pricesq'});
        $temp['source'] = $house->{'ph3 source'};
        $temp['description'] = CommonHelper::searchSimilarMatch($house->{'ph3 description'}, $ph_description, 0);

        if ((count(array_filter($temp))) > 1) $priceHistoryData[] = $temp;

        $temp = [];
        $temp['house_id'] = $house_id;
        $temp['price_date'] = CommonHelper::dbDateFormat($house->{'ph4 date'});
        $temp['price'] = CommonHelper::decimalRemovalCharacter($house->{'ph4 price'});
        // Not In Use $priceHistoryData['sdsd'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 change'};
        $temp['cost_per_sqft'] = CommonHelper::decimalRemovalCharacter($house->{'ph4 pricesq'});
        $temp['source'] = $house->{'ph4 source'};
        $temp['description'] = CommonHelper::searchSimilarMatch($house->{'ph4 description'}, $ph_description, 0);
        if ((count(array_filter($temp))) > 1) $priceHistoryData[] = $temp;

        $temp = [];
        $temp['house_id'] = $house_id;
        $temp['price_date'] = CommonHelper::dbDateFormat($house->{'ph5 date'});
        $temp['price'] = CommonHelper::decimalRemovalCharacter($house->{'ph5 price'});
        // Not In Use $priceHistoryData['sdsd'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 change'};
        $temp['cost_per_sqft'] = CommonHelper::decimalRemovalCharacter($house->{'ph5 pricesq'});
        $temp['source'] = $house->{'ph5 source'};
        $temp['description'] = CommonHelper::searchSimilarMatch($house->{'ph5 description'}, $ph_description, 0);
        if ((count(array_filter($temp))) > 1) $priceHistoryData[] = $temp;

        $temp = [];
        $temp['house_id'] = $house_id;
        $temp['price_date'] = CommonHelper::dbDateFormat($house->{'ph6 date'});
        $temp['price'] = CommonHelper::decimalRemovalCharacter($house->{'ph6 price'});
        // Not In Use $priceHistoryData['sdsd'] = CommonHelper::decimalRemovalCharacter($house->{'ph1 change'};
        $temp['cost_per_sqft'] = CommonHelper::decimalRemovalCharacter($house->{'ph6 pricesq'});
        $temp['source'] = $house->{'ph6 source'};
        $temp['description'] = CommonHelper::searchSimilarMatch($house->{'ph6 description'}, $ph_description, 0);
        if ((count(array_filter($temp))) > 1) $priceHistoryData[] = $temp;


    }

    private function schoolAndNeighbourHood($house_id, $house, &$schoolAndNeighbourHood)
    {
        $temp = [];
        $temp['house_id'] = $house_id;
        $temp['elementary_school'] = ($house->{'ele_school'});
        $temp['middle_school'] = ($house->{'mid_school'});
        $temp['high_school'] = ($house->{'high_school'});
        $temp['elementary_ranking'] = ($house->{'ele_ranking'});
        $temp['middle_ranking'] = ($house->{'mid_ranking'});
        $temp['high_ranking'] = ($house->{'high_ranking'});
        $temp['elementary_distance'] = ($house->{'ele_distance'});
        $temp['middle_distance'] = ($house->{'mid_distance'});
        $temp['high_distance'] = ($house->{'high_distance'});


        if ((count(array_filter($temp))) > 1) $schoolAndNeighbourHood[] = $temp;
    }

    private function foreclosureInfo($house_ids, &$temp, &$localRealStateData, &$sale_details, &$sale_details_descriptions, &$sale_id, &$house_id_sale_id)
    {
        $foreclosure = DB::connection('olddb')->table('forclosure_information')->leftJoin('forclouser_info', 'forclouser_info.houseid', '=', 'forclosure_information.house id')->leftJoin('forclouser_info2', 'forclouser_info2.houseid', '=', 'forclosure_information.house id')->leftJoin('home_buyers_alias1', 'home_buyers_alias1.house id', '=', 'forclosure_information.house id')->select(['forclosure_information.*',
            'forclouser_info.*',
            'forclouser_info2.*',
            'home_buyers_alias1.fr_im_by',
            'home_buyers_alias1.fr_im_by_date',])->whereIn('forclosure_information.house id', $house_ids)->orderBy('forclosure_information.house id', 'asc')->get();
        Log::emergency("DB FORECLOSURE - " . count($foreclosure));


        if ($foreclosure != null) {

            foreach ($foreclosure as $fkey => $fvalue) {

                $house_id = $fvalue->{'house id'};

                $temp[$house_id]['country_assessor_url'] = $fvalue->{'county assesor1 url'};
                $temp[$house_id]['treasurer_url'] = $fvalue->{'county teasurer1 url'} ? $fvalue->{'county teasurer1 url'} : '';
                $temp[$house_id]['tax_bill_url'] = $fvalue->{'county site url 2'} ? $fvalue->{'county site url 2'} : '';
                $temp[$house_id]['county_value'] = CommonHelper::decimalRemovalCharacter($fvalue->counvalue);

                $total_sqft = (!empty($temp[$house_id]['total_sqft'])) ? $temp[$house_id]['total_sqft'] : 1;
                $costsqft = round(CommonHelper::decimalRemovalCharacter($fvalue->comp) / ($total_sqft == 0 ? 1 : $total_sqft), 2);
                $temp[$house_id]['cost_sqft'] = $costsqft ? $costsqft : null;

                $localRealStateData[$house_id]['zestimate'] = CommonHelper::decimalRemovalCharacter($fvalue->{'zestimate'}); // forclosure_information
                $localRealStateData[$house_id]['truila_est'] = CommonHelper::decimalRemovalCharacter($fvalue->{'trulia est'}); // forclosure_information
                $localRealStateData[$house_id]['realtor_est'] = CommonHelper::decimalRemovalCharacter($fvalue->{'realtor est'}); // forclosure_information
                $localRealStateData[$house_id]['redfin_est'] = CommonHelper::decimalRemovalCharacter($fvalue->{'redfine_est'}); // forclosure_information

                // 551 doesn't exists
                $fvalue->{'fr_nos_by'} = is_numeric($fvalue->{'fr_nos_by'}) ? $fvalue->{'fr_nos_by'} : null;
                $fvalue->{'fr_nos_by'} = (in_array($fvalue->{'fr_nos_by'}, [551,
                    15,
                    9,
                    8,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    19,
                    910])) ? 10 : intval($fvalue->{'fr_nos_by'});

                if (!$this->isuserExists($fvalue->{'fr_nos_by'})) $fvalue->{'fr_nos_by'} = null;

                ++$sale_id;

                //if(in_array($house_id,[373267,384240]))
                $fvalue->{'auction place'} = preg_replace("/[^a-zA-Z0-9.,;:'\s]/", "", $fvalue->{'auction place'});


            }


        }

    }

    private function bankInformation($house_ids, &$assessmentTaxes, &$mortgageLiens, &$mortgage_id, &$house_id_mortgage_id, &$mortgageOtherLiens, &$mortgageHoaLiens

    )
    {

        $tempSQL = DB::connection('olddb')->table('bank information')->leftJoin('bank_info2', 'bank_info2.house_id', '=', 'bank information.house id')->leftJoin('bank_info3', 'bank_info3.house_id', '=', 'bank information.house id')->select('*')->whereIn('bank information.house id', $house_ids)->orderBy('bank information.house id', 'asc')->get();
        Log::emergency("DB bank information - " . count($tempSQL));

        if ($tempSQL != null) {
            foreach ($tempSQL as $fkey => $fvalue) {
                $house_id = $fvalue->{'house id'};

                // We have 1 to 5 records,
                $temp = [];
                $temp['house_id'] = $house_id;
                $temp['property_taxes_owed'] = CommonHelper::decimalRemovalCharacter($fvalue->{'hafa'});
                $temp['property_taxes_owed_year'] = CommonHelper::dbIntValValue999999($fvalue->{'hafa date'}, 32767); // year only
                $temp['taxes_assessed'] = CommonHelper::decimalRemovalCharacter($fvalue->{'taxes assessed1'});
                $temp['taxes_year'] = CommonHelper::dbIntValValue999999($fvalue->{'taxes assessed1 date'}, 32767); // year only


                if ((count(array_filter($temp))) > 1) $assessmentTaxes[] = $temp;

                for ($i = 2; $i <= 5; $i++) {
                    $temp = [];
                    $temp['house_id'] = $house_id;
                    $temp['property_taxes_owed'] = CommonHelper::decimalRemovalCharacter($fvalue->{'hafa' . $i . ''});
                    $temp['property_taxes_owed_year'] = CommonHelper::dbIntValValue999999($fvalue->{'hafa' . $i . ' date'}, 32767); // year only
                    $temp['taxes_assessed'] = CommonHelper::decimalRemovalCharacter($fvalue->{'taxes assessed' . $i . ''});
                    $temp['taxes_year'] = CommonHelper::dbIntValValue999999($fvalue->{'taxes assessed' . $i . ' date'}, 32767); // year only

                    if ((count(array_filter($temp))) > 1) $assessmentTaxes[] = $temp;
                }

                // Lien 1st.
                ++$mortgage_id;
                $d1stLienBank1 = null;
                $d1stLienBank_broken_priority = '';
                $d1stLienBank_foreclosinglein = '';
                if (!empty($fvalue->{'1st lien & amount'})) list($d1stLienBank1, $d1stLienBank_broken_priority, $d1stLienBank_foreclosinglein) = explode('/', $fvalue->{'1st lien & amount'} . "/" . "/");

                $temp = [];
                $temp['mortgage_id'] = $mortgage_id;
                $temp['house_id'] = $house_id;
                $temp['lien_type'] = 1;
                $temp['lien_foreclosing'] = !empty($d1stLienBank_foreclosinglein) ? 1 : 0;
                $no_str = explode('||', $fvalue->{'no_str'});
                $temp['no_str_no_appt'] = in_array('1', $no_str) ? 1 : 0;
                $temp['defective_lien'] = !empty($d1stLienBank_broken_priority) ? 1 : 0;
                $temp['lender'] = $d1stLienBank1;
                $temp['lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'lien1_amount'});
                $temp['date_recorded'] = CommonHelper::dbDateFormat($fvalue->{'1st lien date'});
                $temp['dt_book_page'] = $fvalue->{'1st lien book'} . '-' . $fvalue->{'1st lien page'};
                if ($temp['dt_book_page'] == '-') $temp['dt_book_page'] = null;
                $temp['assignment_bp'] = $fvalue->{'1st lien related'};
                $loan_type = CommonHelper::searchSimilarMatch($fvalue->{'1stLien_loan_type'}, config('property_information.loan_type'), null);
                $temp['loan_type'] = $loan_type;
                $temp['loan_term'] = $fvalue->{'1stLien_loan_term'};
                $temp['maturity_date'] = CommonHelper::dbDateFormat($fvalue->{'1stLien_maturity_date'});
                $temp['right_to_cure'] = $fvalue->{'1stLien_before_sale_date'};
                $temp['trustee_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLine_trustee_fee'});
                $temp['str_book_page'] = $fvalue->{'1stLien_str_book'};
                $temp['str_date'] = CommonHelper::dbDateFormat($fvalue->{'1stLien_str_date'});
                $temp['trustee'] = $fvalue->{'1stLien_trustee_name'};
                $temp['reasonable_attorney_fees'] = $fvalue->{'1stLien_attorney_fee'} == 1 ? 1 : 0;
                $temp['est_equity'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_est_equity'});
                $temp['est_late_payment_and_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_estimated_payment'});
                $temp['total_est_debt'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_late_attorney_fee'});

                $temp['estimated_a_match'] = 0;
                $temp['dt_nos'] = 0;
                $temp['amortization_calculation'] = $fvalue->{'1stLine_amortization'} == 1 ? 1 : 0;
                $temp['amortization_annual_interest'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_i'});
                $temp['amortization_monthly_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_mo'});
                $temp['amortization_monthly_principal_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_p'});
                $temp['amortization_monthly_interest_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_mi'});
                $temp['amortization_loan_estimate_balance'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_est_loan_balance'});


                $temp['modification_agreement'] = $fvalue->{'1stLien_mod'} == 1 ? 1 : 0;
                $temp['modification_book_page'] = $fvalue->{'1stLien_mod_book'};
                $temp['modification_date'] = CommonHelper::dbDateFormat($fvalue->{'1stLien_mod_date'});
                $temp['modification_lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_mod_amount'});
                $temp['modification_loan_term'] = $fvalue->{'1stLien_mod_loan_term'};
                $temp['modification_maturity_date'] = CommonHelper::dbDateFormat($fvalue->{'1stLien_mod_maturity_date'});
                $temp['modification_annual_interest'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_mod_annual_interest'});
                $temp['modification_monthly_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_mod_month_payment'});
                $temp['modification_loan_estimate_balance'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_loan_est_balance'});
                $temp['modification_est_late_payment_and_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'1stLien_est_late_payment_fee'});


                $temp['subordination_agreement'] = $fvalue->{'1stLien_sub'} == 1 ? 1 : 0;
                $temp['sub_a_book_page'] = $fvalue->{'1stLien_sub_book'};
                $temp['sub_a_date'] = CommonHelper::dbDateFormat($fvalue->{'1stLien_sub_date'});
                $temp['sub_lien_position'] = $fvalue->{'1stLien_sub_position'} == 'SUPERIOR_LIEN' ? 1 : ($fvalue->{'1stLien_sub_position'} == 'INFERIOR_LIEN' ? 2 : 0);

                $stLien_ps_owner1 = explode('||', $fvalue->{'1stLien_ps_owner'});
                $temp['property_owner_1'] = in_array('1', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_2'] = in_array('2', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_3'] = in_array('3', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_4'] = in_array('4', $stLien_ps_owner1) ? 1 : 0;
                $temp['company_not_current'] = $fvalue->{'1stLien_deed_trust'} == 1 ? 1 : 0;

                $stLien_dtc = json_decode($fvalue->{'1stLien_dtc'});
                $stLien_dtc = $stLien_dtc == null ? [] : $stLien_dtc;
                $temp['dtc_first_check'] = in_array('1dtc', $stLien_dtc) ? 1 : 0;
                $temp['dca_second_check'] = in_array('2dca', $stLien_dtc) ? 1 : 0;
                $temp['dca_final_check'] = in_array('3dca', $stLien_dtc) ? 1 : 0;

                if ((count(array_filter($temp))) > 3) $mortgageLiens[] = $temp;

                $house_id_mortgage_id[$house_id . '_1'] = $mortgage_id; // 1 is lien type

                // LIEN second
                ++$mortgage_id;
                $d1stLienBank1 = null;
                $d1stLienBank_broken_priority = '';
                $d1stLienBank_foreclosinglein = '';
                if (!empty($fvalue->{'2nd lien & amount'})) list($d1stLienBank1, $d1stLienBank_broken_priority, $d1stLienBank_foreclosinglein) = explode('/', $fvalue->{'2nd lien & amount'} . "/" . "/");

                $temp = [];
                $temp['mortgage_id'] = $mortgage_id;
                $temp['house_id'] = $house_id;
                $temp['lien_type'] = 2;
                $temp['lien_foreclosing'] = !empty($d1stLienBank_foreclosinglein) ? 1 : 0;
                $no_str = explode('||', $fvalue->{'no_str'});
                $temp['no_str_no_appt'] = in_array('2', $no_str) ? 1 : 0;
                $temp['defective_lien'] = !empty($d1stLienBank_broken_priority) ? 1 : 0;
                $temp['lender'] = $d1stLienBank1;
                $temp['lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'lien2_amount'});
                $temp['date_recorded'] = CommonHelper::dbDateFormat($fvalue->{'2nd lien date'});
                $temp['dt_book_page'] = $fvalue->{'2nd lien book'} . '-' . $fvalue->{'2nd lien page'};
                if ($temp['dt_book_page'] == '-') $temp['dt_book_page'] = null;
                $temp['assignment_bp'] = $fvalue->{'2nd lien related'};
                $loan_type = CommonHelper::searchSimilarMatch($fvalue->{'2ndLien_loan_type'}, config('property_information.loan_type'), null);
                $temp['loan_type'] = $loan_type;
                $temp['loan_term'] = $fvalue->{'2ndLien_loan_term'};
                $temp['maturity_date'] = CommonHelper::dbDateFormat($fvalue->{'2ndLien_maturity_date'});
                $temp['right_to_cure'] = $fvalue->{'2ndLien_before_sale_date'};
                $temp['trustee_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLine_trustee_fee'});
                $temp['str_book_page'] = $fvalue->{'2ndLien_str_book'};
                $temp['str_date'] = CommonHelper::dbDateFormat($fvalue->{'2ndLien_str_date'});
                $temp['trustee'] = $fvalue->{'2ndLien_trustee_name'};
                $temp['reasonable_attorney_fees'] = $fvalue->{'2ndLien_attorney_fee'} == 1 ? 1 : 0;
                $temp['est_equity'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_est_equity'});
                $temp['est_late_payment_and_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLine_estimated_payment'});
                $temp['total_est_debt'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_late_attorney_fee'});

                $temp['estimated_a_match'] = 0;
                $temp['dt_nos'] = 0;
                $temp['amortization_calculation'] = $fvalue->{'2ndLine_amortization'} == 1 ? 1 : 0;
                $temp['amortization_annual_interest'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_i'});
                $temp['amortization_monthly_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_mo'});
                $temp['amortization_monthly_principal_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_p'});
                $temp['amortization_monthly_interest_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_mi'});
                $temp['amortization_loan_estimate_balance'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_est_loan_balance'});


                $temp['modification_agreement'] = $fvalue->{'2ndLien_mod'} == 1 ? $fvalue->{'2ndLien_mod'} : 0;
                $temp['modification_book_page'] = $fvalue->{'2ndLien_mod_book'};
                $temp['modification_date'] = CommonHelper::dbDateFormat($fvalue->{'2ndLien_mod_date'});
                $temp['modification_lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_mod_amount'});
                $temp['modification_loan_term'] = $fvalue->{'2ndLien_mod_loan_term'};
                $temp['modification_maturity_date'] = CommonHelper::dbDateFormat($fvalue->{'2ndLien_mod_maturity_date'});
                $temp['modification_annual_interest'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_mod_annual_interest'});
                $temp['modification_monthly_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_mod_month_payment'});
                $temp['modification_loan_estimate_balance'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_loan_est_balance'});
                $temp['modification_est_late_payment_and_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'2ndLien_est_late_payment_fee'});


                $temp['subordination_agreement'] = $fvalue->{'2ndLien_sub'} == 1 ? 1 : 0;
                $temp['sub_a_book_page'] = $fvalue->{'2ndLien_sub_book'};
                $temp['sub_a_date'] = CommonHelper::dbDateFormat($fvalue->{'2ndLien_sub_date'});
                $temp['sub_lien_position'] = $fvalue->{'2ndLien_sub_position'} == 'SUPERIOR_LIEN' ? 1 : ($fvalue->{'2ndLien_sub_position'} == 'INFERIOR_LIEN' ? 2 : 0);

                $stLien_ps_owner1 = explode('||', $fvalue->{'d2ndLien_ps_owner'});
                $temp['property_owner_1'] = in_array('1', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_2'] = in_array('2', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_3'] = in_array('3', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_4'] = in_array('4', $stLien_ps_owner1) ? 1 : 0;
                $temp['company_not_current'] = $fvalue->{'d2ndLien_deed_trust'} == 1 ? 1 : 0;

                $stLien_dtc = json_decode($fvalue->{'2ndLien_dtc'});
                $stLien_dtc = $stLien_dtc == null ? [] : $stLien_dtc;
                $temp['dtc_first_check'] = in_array('1dtc', $stLien_dtc) ? 1 : 0;
                $temp['dca_second_check'] = in_array('2dca', $stLien_dtc) ? 1 : 0;
                $temp['dca_final_check'] = in_array('3dca', $stLien_dtc) ? 1 : 0;
                // $mortgageLiens[] = $temp;
                if ((count(array_filter($temp))) > 3) $mortgageLiens[] = $temp;

                $house_id_mortgage_id[$house_id . '_2'] = $mortgage_id; // 2 is lien type


                // Third second
                ++$mortgage_id;
                $d1stLienBank1 = null;
                $d1stLienBank_broken_priority = '';
                $d1stLienBank_foreclosinglein = '';
                if (!empty($fvalue->{'3rd lien & amount'})) list($d1stLienBank1, $d1stLienBank_broken_priority, $d1stLienBank_foreclosinglein) = explode('/', $fvalue->{'3rd lien & amount'} . "/" . "/");

                $temp = [];
                $temp['mortgage_id'] = $mortgage_id;
                $temp['house_id'] = $house_id;
                $temp['lien_type'] = 3;
                $temp['lien_foreclosing'] = !empty($d1stLienBank_foreclosinglein) ? 1 : 0;
                $no_str = explode('||', $fvalue->{'no_str'});
                $temp['no_str_no_appt'] = in_array('3', $no_str) ? 1 : 0;
                $temp['defective_lien'] = !empty($d1stLienBank_broken_priority) ? 1 : 0;
                $temp['lender'] = $d1stLienBank1;
                $temp['lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'lien3_amount'});
                $temp['date_recorded'] = CommonHelper::dbDateFormat($fvalue->{'3rd lien date'});
                $temp['dt_book_page'] = $fvalue->{'3rd lien book'} . '-' . $fvalue->{'3rd lien page'};
                if ($temp['dt_book_page'] == '-') $temp['dt_book_page'] = null;
                $temp['assignment_bp'] = $fvalue->{'3rd lien related'};
                $loan_type = CommonHelper::searchSimilarMatch($fvalue->{'3rdLien_loan_type'}, config('property_information.loan_type'), null);
                $temp['loan_type'] = $loan_type;
                $temp['loan_term'] = $fvalue->{'3rdLien_loan_term'};
                $temp['maturity_date'] = CommonHelper::dbDateFormat($fvalue->{'3rdLien_maturity_date'});
                $temp['right_to_cure'] = $fvalue->{'3rdLien_before_sale_date'};
                $temp['trustee_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLine_trustee_fee'});
                $temp['str_book_page'] = $fvalue->{'3rdLien_str_book'};
                $temp['str_date'] = CommonHelper::dbDateFormat($fvalue->{'3rdLien_str_date'});
                $temp['trustee'] = $fvalue->{'3rdLien_trustee_name'};
                $temp['reasonable_attorney_fees'] = $fvalue->{'3rdLien_attorney_fee'} == 1 ? 1 : 0;
                $temp['est_equity'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_est_equity'});
                $temp['est_late_payment_and_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLine_estimated_payment'});
                $temp['total_est_debt'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_late_attorney_fee'});

                $temp['estimated_a_match'] = 0;
                $temp['dt_nos'] = 0;
                $temp['amortization_calculation'] = $fvalue->{'3rdLine_amortization'} == 1 ? 1 : 0;
                $temp['amortization_annual_interest'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_i'});
                $temp['amortization_monthly_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_mo'});
                $temp['amortization_monthly_principal_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_p'});
                $temp['amortization_monthly_interest_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_mi'});
                $temp['amortization_loan_estimate_balance'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_est_loan_balance'});


                $temp['modification_agreement'] = $fvalue->{'3rdLien_mod'} == 1 ? 1 : 0;
                $temp['modification_book_page'] = $fvalue->{'3rdLien_mod_book'};
                $temp['modification_date'] = CommonHelper::dbDateFormat($fvalue->{'3rdLien_mod_date'});
                $temp['modification_lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_mod_amount'});
                $temp['modification_loan_term'] = $fvalue->{'3rdLien_mod_loan_term'};
                $temp['modification_maturity_date'] = CommonHelper::dbDateFormat($fvalue->{'3rdLien_mod_maturity_date'});
                $temp['modification_annual_interest'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_mod_annual_interest'});
                $temp['modification_monthly_payment'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_mod_month_payment'});
                $temp['modification_loan_estimate_balance'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_loan_est_balance'});
                $temp['modification_est_late_payment_and_fees'] = CommonHelper::decimalRemovalCharacter($fvalue->{'3rdLien_est_late_payment_fee'});


                $temp['subordination_agreement'] = $fvalue->{'3rdLien_sub'} == 1 ? 1 : 0;
                $temp['sub_a_book_page'] = $fvalue->{'3rdLien_sub_book'};
                $temp['sub_a_date'] = CommonHelper::dbDateFormat($fvalue->{'3rdLien_sub_date'});
                $temp['sub_lien_position'] = $fvalue->{'3rdLien_sub_position'} == 'SUPERIOR_LIEN' ? 1 : ($fvalue->{'3rdLien_sub_position'} == 'INFERIOR_LIEN' ? 2 : 0);

                $stLien_ps_owner1 = explode('||', $fvalue->{'d3rdLien_ps_owner'});
                $temp['property_owner_1'] = in_array('1', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_2'] = in_array('2', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_3'] = in_array('3', $stLien_ps_owner1) ? 1 : 0;
                $temp['property_owner_4'] = in_array('4', $stLien_ps_owner1) ? 1 : 0;
                $temp['company_not_current'] = $fvalue->{'d3rdLien_deed_trust'} == 1 ? 1 : 0;

                $stLien_dtc = json_decode($fvalue->{'3rdLien_dtc'});
                $stLien_dtc = $stLien_dtc == null ? [] : $stLien_dtc;
                $temp['dtc_first_check'] = in_array('1dtc', $stLien_dtc) ? 1 : 0;
                $temp['dca_second_check'] = in_array('2dca', $stLien_dtc) ? 1 : 0;
                $temp['dca_final_check'] = in_array('3dca', $stLien_dtc) ? 1 : 0;
                // $mortgageLiens[] = $temp;
                if ((count(array_filter($temp))) > 3) $mortgageLiens[] = $temp;

                $house_id_mortgage_id[$house_id . '_3'] = $mortgage_id; // 2 is lien type


                $temp = [];
                $temp['house_id'] = $house_id;
                $d1stLienBank1 = null;
                $d1stLienBank_broken_priority = '';
                $d1stLienBank_foreclosinglein = '';
                if (!empty($fvalue->{'otherlien'})) list($d1stLienBank1, $d1stLienBank_broken_priority, $d1stLienBank_foreclosinglein) = explode('/', $fvalue->{'otherlien'} . "/" . "/");

                $temp['lender'] = $d1stLienBank1;
                $temp['lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'other lein amount'});
                $temp['date_recorded'] = CommonHelper::dbDateFormat($fvalue->{'other lien date'});
                $temp['book_page_assignment_bp'] = $fvalue->{'other lein related'};
                $temp['assignment_bp'] = $fvalue->{'other lien book'} . '-' . $fvalue->{'other lien page'};
                $temp['assignment_bp'] = $temp['assignment_bp'] == '-' ? null : $temp['assignment_bp'];
                // $mortgageOtherLiens[] = $temp;
                if ((count(array_filter($temp))) > 1) $mortgageOtherLiens[] = $temp;


                $temp = [];
                $temp['house_id'] = $house_id;
                $d1stLienBank1 = null;
                $d1stLienBank_broken_priority = '';
                $d1stLienBank_foreclosinglein = '';
                if (!empty($fvalue->{'hoalien'})) list($d1stLienBank1, $d1stLienBank_broken_priority, $d1stLienBank_foreclosinglein) = explode('/', $fvalue->{'hoalien'} . "/" . "/");

                $temp['defective_notice_hoa'] = !empty($d1stLienBank_broken_priority) ? 1 : 0;;
                $temp['hoa_lien_foreclosing'] = !empty($d1stLienBank_foreclosinglein) ? 1 : 0;;
                $temp['manual_search_hoa'] = $fvalue->{'hoalienmansearch'} == 1 ? 1 : 0;

                $no_str = explode('||', $fvalue->{'no_str'});
                $temp['no_str'] = in_array('hoa', $no_str) ? 1 : 0;

                $temp['hoa_name'] = $d1stLienBank1;
                $temp['hoa_lien_amount'] = CommonHelper::decimalRemovalCharacter($fvalue->{'hoa lien amount'});
                $temp['date_of_hoa_lien'] = CommonHelper::dbDateFormat($fvalue->{'hoa lien date'});
                $temp['hoa_lien_book_page'] = $fvalue->{'hoa lien book'} . '-' . $fvalue->{'hoa lien page'};
                $temp['hoa_lien_book_page'] = $temp['hoa_lien_book_page'] == '-' ? null : $temp['hoa_lien_book_page'];
                $temp['str_date'] = CommonHelper::dbDateFormat($fvalue->{'hoaLien_str_date'});
                $temp['str_book_page'] = $fvalue->{'hoaLien_str_book'};
                $temp['trustee_hoa'] = $fvalue->{'hoaLien_trustee_name'};

                $hoaLien_ps_owner = explode('||', $fvalue->{'hoaLien_ps_owner'});
                $temp['prop_sign_owner_1'] = in_array('1', $hoaLien_ps_owner) ? 1 : 0;
                $temp['prop_sign_owner_2'] = in_array('2', $hoaLien_ps_owner) ? 1 : 0;
                $temp['prop_sign_owner_3'] = in_array('3', $hoaLien_ps_owner) ? 1 : 0;
                $temp['prop_sign_owner_4'] = in_array('4', $hoaLien_ps_owner) ? 1 : 0;

                $temp['company_not_ct_rcd'] = $fvalue->{'hoaLien_deed_trust'} == 1 ? 1 : 0;
                $stLien_dtc = json_decode($fvalue->{'hoaLien_dtc'});
                $stLien_dtc = $stLien_dtc == null ? [] : $stLien_dtc;
                $temp['dtc_first_check'] = in_array('1dtc', $stLien_dtc) ? 1 : 0;
                $temp['dca_second_check'] = in_array('2dca', $stLien_dtc) ? 1 : 0;
                $temp['dca_final_check'] = in_array('3dca', $stLien_dtc) ? 1 : 0;
                // $mortgageHoaLiens[] = $temp;
                if ((count(array_filter($temp))) > 1) $mortgageHoaLiens[] = $temp;

            }

        }

    }

    private function driveByInformation($house_ids, &$temp, &$managerNotes)
    {
        // Drive By Information
        $driverby = DB::connection('olddb')->table('drive-by information')->select('*')->whereIn('drive-by information.house id', $house_ids)->orderBy('drive-by information.house id', 'asc')->get();
        Log::emergency("DB Drive By - " . count($driverby));
        if ($driverby != null) {
            foreach ($driverby as $tkey => $tvalue) {

                $house_id = $tvalue->{'house id'};
                $temp[$house_id]['stories'] = CommonHelper::dbIntValValue255($tvalue->{'storie(s)'});

                $dTemp = [];
                $dTemp['house_id'] = $house_id;
                $dTemp['user_id'] = 1;
                $dTemp['notes'] = trim($tvalue->notes);
                $dTemp['note_type'] = 1;
                $dTemp['created_at'] = 0;
                $dTemp['updated_at'] = 0;
                if (!empty($dTemp['notes'])) $managerNotes[] = $dTemp;

                $dTemp = [];
                $dTemp['house_id'] = $house_id;
                $dTemp['user_id'] = 1;
                $dTemp['notes'] = trim($tvalue->{'neighbor 1 notes'});
                $dTemp['note_type'] = 2;
                $dTemp['created_at'] = 0;
                $dTemp['updated_at'] = 0;
                if (!empty($dTemp['notes'])) $managerNotes[] = $dTemp;

            }
        }
    }

    private function ownerByInfo($house_ids, &$temp, &$ownerByInfo, &$borrowerByInfo)
    {
        $ownerby = DB::connection('olddb')->table('owner information')->leftJoin('owner_info2', 'owner_info2.house_id', '=', 'owner information.house id')->select('*')->whereIn('owner information.house id', $house_ids)->orderBy('owner information.house id', 'asc')->get();
        Log::emergency("DB owner By - " . count($ownerby));
        $borrowerByInfo = [];
        if ($ownerby != null) {
            foreach ($ownerby as $tkey => $tvalue) {

                $house_id = $tvalue->{'house id'};
                $temp[$house_id]['parcel_id1'] = intval($tvalue->mParcel1);
                $temp[$house_id]['parcel_id2'] = intval($tvalue->mParcel2);


                $tempOwner = [];
                $tempOwner['house_id'] = $tvalue->{'house id'};
                $tempOwner['full_name'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'full name'}, 60);
                $tempOwner['full_name'] = preg_replace("/[^A-Za-z0-9 ]/", '', $tempOwner['full_name']);

                $tempOwner['full_address'] = CommonHelper::addressMakerFormat($tvalue->{'address'}, $tvalue->{'mCity'}, $tvalue->{'mCounty'}, $tvalue->{'mstate'}, $tvalue->{'mZipCode'});
                $tempOwner['email'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'owner email'}, 50);
                $tempOwner['phone'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'owner phone'}, 15);
                // $tempOwner['phone2'] = $tvalue->{'house id'};
                $tempOwner['deed_bp_instrument'] = $tvalue->{'deep_book_page'};
                $tempOwner['deed_recorded_date'] = CommonHelper::dbDateFormat($tvalue->{'date_recorded'});
                // $tempOwner['beenverified_url'] = $tvalue->{'house id'};
                // $ownerByInfo[] = $tempOwner;
                if ((count(array_filter($tempOwner))) > 1) $ownerByInfo[] = $tempOwner;


                $tempOwner = [];
                $tempOwner['house_id'] = $tvalue->{'house id'};
                $tempOwner['full_name'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'owner2 full name'}, 60);
                $tempOwner['full_name'] = preg_replace("/[^A-Za-z0-9 ]/", '', $tempOwner['full_name']);
                $tempOwner['full_address'] = CommonHelper::addressMakerFormat($tvalue->{'owner2 address'}, $tvalue->{'owner2 mCity'}, $tvalue->{'owner2 mCounty'}, $tvalue->{'owner2 mstate'}, $tvalue->{'owner2 mZipCode'});
                $tempOwner['email'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'owner2 email'}, 50);
                $tempOwner['phone'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'owner2 phone'}, 15);
                $tempOwner['deed_bp_instrument'] = null;
                $tempOwner['deed_recorded_date'] = null;
                if ((count(array_filter($tempOwner))) > 1) $ownerByInfo[] = $tempOwner;
                // $ownerByInfo[] = $tempOwner;

                $tempOwner = [];
                $tempOwner['house_id'] = $tvalue->{'house id'};
                $tempOwner['full_name'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'borrower'}, 60);
                $tempOwner['full_name'] = preg_replace("/[^A-Za-z0-9 ]/", '', $tempOwner['full_name']);
                $tempOwner['full_address'] = CommonHelper::addressMakerFormat($tvalue->{'borrower address'}, $tvalue->{'mBorrowerCity'}, $tvalue->{'mBorrowerCounty'}, $tvalue->{'mBorrowerState'}, $tvalue->{'mBorrowerZip'});
                $tempOwner['email'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'borrower email'}, 50);
                $tempOwner['phone'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'borrower phone'}, 15);

                if ((count(array_filter($tempOwner))) > 1) $borrowerByInfo[] = $tempOwner;

                $tempOwner = [];
                $tempOwner['house_id'] = $tvalue->{'house id'};
                $tempOwner['full_name'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'borrower2 full name'}, 60);
                $tempOwner['full_name'] = preg_replace("/[^A-Za-z0-9 ]/", '', $tempOwner['full_name']);
                $tempOwner['full_address'] = CommonHelper::addressMakerFormat($tvalue->{'borrower2 address'}, $tvalue->{'borrower2 mCity'}, $tvalue->{'borrower2 mCounty'}, $tvalue->{'borrower2 mState'}, $tvalue->{'borrower2 mZipCode'});
                $tempOwner['email'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'borrower2 email'}, 50);
                $tempOwner['phone'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->{'borrower2 phone'}, 15);

                if ((count(array_filter($tempOwner))) > 1) $borrowerByInfo[] = $tempOwner;

            }
        }
    }

    private function commonDocuments($house_ids, &$property_document, &$recorded_document, &$house_id_sale_id, &$owner_document, &$house_id_mortgage_id, &$mortgageLienDocuments, &$mortgageOtherDocument, &$mortgageHoaDocument)
    {
        $tempSql = DB::connection('olddb')->table('common_documents')->select('*')->whereIn('common_documents.house_id', $house_ids)->where('is_deleted', '!=', 'yes')//->limit(10000)
        ->get();
        Log::emergency("DB Common Documents By - " . count($tempSql));
        if ($tempSql != null) {
            foreach ($tempSql as $tkey => $tvalue) {
                $house_id = $tvalue->{'house_id'};

                $document_date = '';

                // Date parsing
                $t = substr($tvalue->doc_name, 0, 10);
                $t = str_replace(" ", "-", trim($t));
                $temp = ((bool)strtotime($t));
                if ($temp) {
                    $document_date = date('Y-m-d', strtotime($t));
                } else {
                    $t = explode(".", $tvalue->doc_name);
                    $t = $t[0];
                    $t = (strlen($t) > 10) ? substr($t, -10) : $t;
                    $t = str_replace(" ", "-", trim($t));
                    $temp = ((bool)strtotime($t));

                    if ($temp) {
                        $document_date = date('Y-m-d', strtotime($t));
                    }
                }
                // Date parsing


                $temp = [];
                $temp['house_id'] = $house_id;
                $temp['added_by'] = intval($tvalue->added_by);
                $temp['document_type'] = 99; // Other
                $temp['org_name'] = $tvalue->doc_name;
                $temp['store_name'] = $tvalue->store_doc_name;
                $temp['other_name'] = '..';
                $temp['case_number'] = '..';
                $temp['document_date'] = CommonHelper::dbDateFormat($document_date);
                $temp['created_at'] = $tvalue->created_date;

                if ($tvalue->object_type == 'property') {
                    $property_document[] = $temp;
                }


                if ($tvalue->object_type == 'owner') {
                    $owner_document[] = $temp;
                }
                if ($tvalue->object_type == 'otherLienBank') {
                    $mortgageOtherDocument[] = $temp;
                }
                if ($tvalue->object_type == 'HOALien') {
                    $mortgageHoaDocument[] = $temp;
                }

                $mortgageTemp = $temp;
                if ($tvalue->object_type == 'firstLienBank') {
                    unset($mortgageTemp['house_id']);
                    $mortgageTemp['mortgage_id'] = $house_id_mortgage_id[$house_id . '_1'];
                    $mortgageLienDocuments[] = $mortgageTemp;
                }
                if ($tvalue->object_type == 'secondLienBank') {
                    unset($mortgageTemp['house_id']);
                    $mortgageTemp['mortgage_id'] = $house_id_mortgage_id[$house_id . '_2'];
                    $mortgageLienDocuments[] = $mortgageTemp;
                }
                if ($tvalue->object_type == 'thirdLienBank') {
                    unset($mortgageTemp['house_id']);
                    $mortgageTemp['mortgage_id'] = $house_id_mortgage_id[$house_id . '_3'];
                    $mortgageLienDocuments[] = $mortgageTemp;
                }

            }


        }
    }

    private function getGeo($house_ids, &$pictureAndVideo)
    {
        $tempSql = DB::connection('olddb')->table('geo')->select('*')->whereIn('geo.houseid', $house_ids)->orderBy('geo.houseid', 'asc')->get();
        Log::emergency("DB GEO By - " . count($tempSql));
        $geoData = [];
        if ($tempSql != null) {
            foreach ($tempSql as $tkey => $tvalue) {
                //var_dump($tvalue);
                $lat = preg_replace("/[^0-9.-]/", "", $tvalue->lat);
                $lng = str_replace("--", "-", ($tvalue->lng));
                $lng = preg_replace("/[^0-9.-]/", "", $lng);
                if (in_array($tvalue->houseid, [1689499])) {
                    $lat = '38.68967';
                }
                if (in_array($tvalue->houseid, [1836493,
                    1872713,
                    1927598,
                    1932619,1964362])) {
                    $lat = '';
                    $lng = '';
                }
                if ($lat < -180 || $lat > 180 || $lng < -90 || $lng > 90) {
                    $lat = '';
                    $lng = '';
                }

                $house_id = $tvalue->houseid;
                $geoData[$house_id]['house_id'] = $house_id;
                $geoData[$house_id]['address'] = CommonHelper::lengthCheckerSplitStringToLimit($tvalue->address, 255);
                $geoData[$house_id]['latitude'] = ($lat ? $lat : null);
                $geoData[$house_id]['longitude'] = $lng ? $lng : null;


                if (!isset($pictureAndVideo[$house_id])) {
                    $pictureAndVideo[$house_id]['house_id'] = $house_id;
                    $pictureAndVideo[$house_id]['google_map_url'] = '';
                    $pictureAndVideo[$house_id]['image_url'] = "";
                    $pictureAndVideo[$house_id]['video_url'] = '';
                    $pictureAndVideo[$house_id]['video_type'] = ''; // Youtube,Vimeo,Dailymotion
                }
                $pictureAndVideo[$house_id]['google_map_url'] = "https://www.google.com/maps/search/" . $geoData[$house_id]['latitude'] . "," . $geoData[$house_id]['longitude'] . "/data=!3m1!1e3";
            }
        }

        return $geoData;
    }

    private function pictureAndVideo($house_ids, &$pictureAndVideo)
    {

        $tempSql = DB::connection('olddb')->table('videos')->select('*')->whereIn('videos.house_id', $house_ids)->whereNotNull('url_or_key')->get();
        Log::emergency("DB videos By - " . count($tempSql));
        if ($tempSql != null) {
            foreach ($tempSql as $tkey => $tvalue) {

                $house_id = $tvalue->{'house_id'};
                $pictureAndVideo[$house_id]['house_id'] = $house_id;
                // $pictureAndVideo[$house_id]['google_map_url'] = !empty($googleMapLink[$house_id]) ? $googleMapLink[$house_id] : '';
                if (!isset($pictureAndVideo[$house_id]['google_map_url'])) $pictureAndVideo[$house_id]['google_map_url'] = '';
                if (!isset($pictureAndVideo[$house_id]['image_url'])) $pictureAndVideo[$house_id]['image_url'] = '';
                $pictureAndVideo[$house_id]['video_url'] = 'http://www.youtube.com/v/' . $tvalue->url_or_key . '&hl=en&fs=1';
                $pictureAndVideo[$house_id]['video_type'] = ''; // Youtube,Vimeo,Dailymotion
                if ($tvalue->is_type == 0) {
                    $pictureAndVideo[$house_id]['video_type'] = 'Youtube';
                } else if ($tvalue->is_type == 1) {
                    $pictureAndVideo[$house_id]['video_type'] = 'Vimeo';
                }
                $pictureAndVideo[$house_id]['video_type'] = 'Youtube';



            }
        }
    }


    private function picture($chunk, $limit, $houseId = '')
    {

        # TODO: delete unwanted pictures .
        // SELECT count(*) FROM `pictures` where pictures.`house id` not in (select `house id` from `home information`)
        // DELETE FROM `pictures` WHERE `pictures`.`house id` not in (select `house id` from `home information`)
        // DELETE FROM `pictures` WHERE `pictures`.`house id` in (select `house id` from `home information` where `is_deleted` = 'yes')


        // Last record id
        $pictureInfo = DocumentPictureModel::orderBy('id', 'desc')->first();
        $last_record_house_id = intval(@$pictureInfo->{'house_id'});

        $counter = 0;
        $chunkLimit = $limit;

        Log::emergency("-----------------Last House Id-" . $last_record_house_id . "------------ ");
        Log::emergency("-----------------House Id-" . $houseId . "------------ ");

        DB::connection('olddb')
            ->table('pictures')
            ->select('*')
            ->where(function ($q) use ($houseId, $last_record_house_id) {
                if (!empty($houseId))
                    $q->where('pictures.house id', "=", $houseId);
                else
                    $q->where('pictures.house id', ">", $last_record_house_id);

            })
            //->where('pictures.house id', ">", $last_record_house_id)
            ->where('is_deleted', '!=', 'yes')
            ->orderBy('pictures.house id', 'asc')
            ->chunk($chunk, function ($pictures) use (
                &$counter, $chunkLimit

            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $pictureInsert = [];
                $counter = $counter + count($pictures);
                if ($counter > $chunkLimit) return false;

                $picture_type = ["sold_comps" => 8,
                    "after_repairs" => 7,
                    "before_repairs" => 6,
                    "after_pictures" => 3,
                    "before_pictures" => 2,
                    "scraped_pictures" => 1,
                    "highlight_pictures" => 4,
                    "all" => 1,

                ];
                foreach ($pictures as $picture) {

                    $ptype = trim($picture->{'type'});
                    $user_id = empty($picture->{'image_file_by'}) ? 0 : $picture->{'image_file_by'};
                    $user_id = $user_id == "Willow Storm" ? 551 : $user_id;

                    $pictureInsert[] = [
                        "house_id" => $picture->{'house id'},
                        "added_by" => $user_id,
                        "order" => CommonHelper::dbIntValValue255(intval($picture->{'order'})),
                        "picture_type" => $picture_type[$ptype] ? $picture_type[$ptype] : 0,
                        "org_name" => $picture->{'original file name'},
                        "store_name" => $picture->{'store name'},
                        "created_at" => CommonHelper::dbDateFormat($picture->{'image_file_by_date'}, 1),
                    ];

                }

                DB::transaction(function () use (
                    $pictureInsert
                ) {
                    Log::emergency("Total Picture - " . count($pictureInsert));
                    DocumentPictureModel::insert($pictureInsert);

                });

                Log::emergency("Total Picture Completed - " . $counter);
            });


        Log::emergency("Picture: Final End of Records Completed - " . $chunkLimit);
    }



    private function cmaARVHomebuyer1($chunk, $limit, $houseId = "")
    {

        //DELETE FROM `homebuyer_info2` WHERE `homebuyer_info2`.`house_id` in (select `house id` from `home information` where is_deleted = 'yes' )

        //SELECT * FROM `` WHERE 1 group by wholesale_buyer_name

        $counter = 0;
        $chunkLimit = $limit;

        // CmaArvModel::where('id', '>', 0)->forceDelete();
        $last = CmaArvModel::orderBy('house_id', 'DESC')->first();

        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->house_id;

        Log::emergency("----------------------homebuyer_info2 Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------houseId---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('homebuyer_info2')
            ->leftJoin('hb property details', 'homebuyer_info2.house_id', '=', 'hb property details.house id')
            ->leftJoin('forclosure_information', 'homebuyer_info2.house_id', '=', 'forclosure_information.house id')
            ->select([
                'homebuyer_info2.*',
                'hb property details.*',
                'forclosure_information.cma by',
                'forclosure_information.comp date',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('homebuyer_info2.house_id', "=", $houseId);
                else
                    $q->where('homebuyer_info2.house_id', ">", $parimaryId);
            })
            // ->where('homebuyer_info2.house_id', ">", $parimaryId)
            ->orderBy('homebuyer_info2.house_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $qInsert = [];
                $counter = $counter + count($objectTemp);

                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    ## WHolesale Buyer
                    $t0 = explode("-", $tValue->{'wholesale_price_sq_sale_comps'});
                    $t1 = explode("-", $tValue->{'wholesale_price_sq_sold_comps'});
                    $user_id = $this->findUserByName($tValue->{'wholesale_buyer_name'});
                    $tempValue = [
                        'house_id' => $tValue->{'house_id'},
                        'user_id' => $user_id,
                        'date' => CommonHelper::dbDateFormat($tValue->{'wholesale_buyer_date'}),
                        'info_added_by' => 'wholesale_buyer',
                        'specific_demand' => $tValue->{'wholesale_specific_demand'},
                        'general_demand' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'wholesale_general_demand'}, 30),
                        'days_on_market' => CommonHelper::decimalRemovalCharacter($tValue->{'wholesale_days_on_market'}),
                        'phase_renovation' => $tValue->{'wholesale_phase_renovation'},
                        'price_sqft_sale_comps_from' => CommonHelper::decimalRemovalCharacter(@$t0[0]),
                        'price_sqft_sale_comps_to' => CommonHelper::decimalRemovalCharacter(@$t0[1]),
                        'ssd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'wholesale_sale_comps_ssd'}, 250),
                        'gsd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'gsd_sale_comps'}, 250),
                        'rent_gsd' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'rent_gsd'}, 50),
                        'price_sqft_sold_comps_from' => CommonHelper::decimalRemovalCharacter(@$t1[0]),
                        'price_sqft_sold_comps_to' => CommonHelper::decimalRemovalCharacter(@$t1[1]),
                        'ssd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'wholesale_sold_comps_ssd'}, 250),
                        'gsd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'gsd_sold_comps'}, 250),
                        'rental_comps_map' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'hb url2'}, 250),
                        'p1_value' => CommonHelper::decimalRemovalCharacter($tValue->{'p1_value'}),
                        'p2_value' => CommonHelper::decimalRemovalCharacter($tValue->{'p2_value'}),
                        'p3_value' => CommonHelper::decimalRemovalCharacter($tValue->{'p3_value'}),
                        'rents_zestimate' => $tValue->{'rents_zestimate'},
                        'p1_adom' => CommonHelper::decimalRemovalCharacter($tValue->{'p1_adom'}),
                        'p2_adom' => CommonHelper::decimalRemovalCharacter($tValue->{'p1_adom'}),
                        'p3_adom' => CommonHelper::decimalRemovalCharacter($tValue->{'p1_adom'}),
                        'rental_rate' => CommonHelper::decimalRemovalCharacter($tValue->{'rental_rate'}),
                        'comp_url_1' => $tValue->{'property_check_url1'},
                        'comp_url_2' => $tValue->{'property_check_url2'},
                        'comp_url_3' => $tValue->{'property_check_url3'},
                        'comp_url_4' => $tValue->{'property_check_url4'},
                        'recommended_cma_arv' => CommonHelper::decimalRemovalCharacter($tValue->{'wholesale_cma'}),
                        'wholetail_value' => CommonHelper::decimalRemovalCharacter($tValue->{'wholetail_value'})

                    ];

                    if (empty($user_id) && (count(array_filter($tempValue))) > 2)
                        $qInsert[] = $tempValue;
                    else if ((count(array_filter($tempValue))) > 3)
                        $qInsert[] = $tempValue;

                    $specific_demand = json_decode($tValue->{'specific_demand'});
                    $general_demand = json_decode($tValue->{'general_demand'});
                    $days_on_market = json_decode($tValue->{'days_on_market'});
                    $phase_renovation = json_decode($tValue->{'phase_renovation'});
                    $p1_dca_value = json_decode($tValue->{'p1_dca_value'});
                    $p2_dca_value = json_decode($tValue->{'p2_dca_value'});
                    $p3_dca_value = json_decode($tValue->{'p3_dca_value'});
                    $p1_dca_adom = json_decode($tValue->{'p1_dca_adom'});
                    $p2_dca_adom = json_decode($tValue->{'p2_dca_adom'});
                    $p3_dca_adom = json_decode($tValue->{'p3_dca_adom'});

                    ## First DTC
                    $t0 = explode("-", $tValue->{'price sq on sale comps'});
                    $t1 = explode("-", $tValue->{'price sq on sold comps'});
                    $user_id = $tValue->{'first_dtc_name'};
                    if (!$this->isuserExists($user_id)) $user_id = null;
                    $tempValue = [
                        'house_id' => $tValue->{'house_id'},
                        'user_id' => $user_id ? $user_id : null,
                        'date' => CommonHelper::dbDateFormat($tValue->{'first_dtc_date'}),
                        'info_added_by' => 'first_dtc',
                        'specific_demand' => CommonHelper::lengthCheckerSplitStringToLimit(@$specific_demand[2], 30),
                        'general_demand' => CommonHelper::lengthCheckerSplitStringToLimit(@$general_demand[2], 30),
                        'days_on_market' => CommonHelper::decimalRemovalCharacter(@$days_on_market[2]),
                        'phase_renovation' => @$phase_renovation[2],
                        'price_sqft_sale_comps_from' => CommonHelper::decimalRemovalCharacter(@$t0[0]),
                        'price_sqft_sale_comps_to' => CommonHelper::decimalRemovalCharacter(@$t0[1]),
                        'ssd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'hb sale comps url'}, 250),
                        'gsd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'first_gsd_sale_comps'}, 250),
                        'rent_gsd' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'hb url1'}, 50),
                        'price_sqft_sold_comps_from' => CommonHelper::decimalRemovalCharacter(@$t1[0]),
                        'price_sqft_sold_comps_to' => CommonHelper::decimalRemovalCharacter(@$t1[1]),
                        'ssd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'hb sold comp url'}, 250),
                        'gsd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'first_gsd_sold_comps'}, 250),
                        'rental_comps_map' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'rentometer_url'},250),
                        'p1_value' => CommonHelper::decimalRemovalCharacter(@$p1_dca_value[2]),
                        'p2_value' => CommonHelper::decimalRemovalCharacter(@$p2_dca_value[2]),
                        'p3_value' => CommonHelper::decimalRemovalCharacter(@$p3_dca_value[2]),
                        'rents_zestimate' => $tValue->{'first_rents_zestimate'},
                        'p1_adom' => CommonHelper::decimalRemovalCharacter(@$p1_dca_adom[2]),
                        'p2_adom' => CommonHelper::decimalRemovalCharacter(@$p2_dca_adom[2]),
                        'p3_adom' => CommonHelper::decimalRemovalCharacter(@$p3_dca_adom[2]),
                        'rental_rate' => CommonHelper::decimalRemovalCharacter($tValue->{'first_rental_rate'}),
                        'comp_url_1' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'tc_property_url1'}, 250),
                        'comp_url_2' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'tc_property_url2'}, 250),
                        'comp_url_3' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'tc_property_url3'}, 250),
                        'comp_url_4' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'tc_property_url4'}, 250),
                        'recommended_cma_arv' => CommonHelper::decimalRemovalCharacter($tValue->{'first_cma'}),
                        'wholetail_value' => CommonHelper::decimalRemovalCharacter($tValue->{'first_wholetail_value'})

                    ];

                    if (empty($user_id) && (count(array_filter($tempValue))) > 2)
                        $qInsert[] = $tempValue;
                    else if ((count(array_filter($tempValue))) > 3)
                        $qInsert[] = $tempValue;

                    // second DCA
                    $t0 = explode("-", $tValue->{'second_price_sq_sale_comps'});
                    $t1 = explode("-", $tValue->{'second_price_sq_sold_comps'});
                    $user_id = $tValue->{'second_dca_name'};
                    if (!$this->isuserExists($user_id)) $user_id = null;
                    $tempValue = [
                        'house_id' => $tValue->{'house_id'},
                        'user_id' => $user_id ? $user_id : null,
                        'date' => CommonHelper::dbDateFormat($tValue->{'second_dca_date'}),
                        'info_added_by' => 'second_dca',
                        'specific_demand' => CommonHelper::lengthCheckerSplitStringToLimit(@$specific_demand[1], 30),
                        'general_demand' => CommonHelper::lengthCheckerSplitStringToLimit(@$general_demand[1], 30),
                        'days_on_market' => CommonHelper::decimalRemovalCharacter(@$days_on_market[1]),
                        'phase_renovation' => @$phase_renovation[1],
                        'price_sqft_sale_comps_from' => CommonHelper::decimalRemovalCharacter(@$t0[0]),
                        'price_sqft_sale_comps_to' => CommonHelper::decimalRemovalCharacter(@$t0[1]),
                        'ssd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'second_sale_comps_ssd'}, 250),
                        'gsd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'second_gsd_sale_comps'}, 250),
                        'rent_gsd' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'second_sale_comps_gsp'}, 50),
                        'price_sqft_sold_comps_from' => CommonHelper::decimalRemovalCharacter(@$t1[0]),
                        'price_sqft_sold_comps_to' => CommonHelper::decimalRemovalCharacter(@$t1[1]),
                        'ssd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'second_sold_comps_ssd'}, 250),
                        'gsd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'second_gsd_sold_comps'}, 250),
                        'rental_comps_map' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'second_sold_comps_gsp'},250),
                        'p1_value' => CommonHelper::decimalRemovalCharacter(@$p1_dca_value[1]),
                        'p2_value' => CommonHelper::decimalRemovalCharacter(@$p2_dca_value[1]),
                        'p3_value' => CommonHelper::decimalRemovalCharacter(@$p3_dca_value[1]),
                        'rents_zestimate' => $tValue->{'second_rents_zestimate'},
                        'p1_adom' => CommonHelper::decimalRemovalCharacter(@$p1_dca_adom[1]),
                        'p2_adom' => CommonHelper::decimalRemovalCharacter(@$p2_dca_adom[1]),
                        'p3_adom' => CommonHelper::decimalRemovalCharacter(@$p3_dca_adom[1]),
                        'rental_rate' => CommonHelper::decimalRemovalCharacter($tValue->{'second_rental_rate'}),
                        'comp_url_1' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'dca_property_url1'}, 250),
                        'comp_url_2' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'dca_property_url2'}, 250),
                        'comp_url_3' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'dca_property_url3'}, 250),
                        'comp_url_4' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'dca_property_url4'}, 250),
                        'recommended_cma_arv' => CommonHelper::decimalRemovalCharacter($tValue->{'second_cma'}),
                        'wholetail_value' => CommonHelper::decimalRemovalCharacter($tValue->{'second_wholetail_value'})
                    ];

                    if (empty($user_id) && (count(array_filter($tempValue))) > 2)
                        $qInsert[] = $tempValue;
                    else if ((count(array_filter($tempValue))) > 3)
                        $qInsert[] = $tempValue;


                    // Third DCA
                    $t0 = explode("-", $tValue->{'first_price_sq_sale_comps'});
                    $t1 = explode("-", $tValue->{'first_price_sq_sold_comps'});
                    $user_id = $tValue->{'cma by'};
                    if (!$this->isuserExists($user_id)) $user_id = null;
                    $tempValue = [
                        'house_id' => $tValue->{'house_id'},
                        'user_id' => $user_id ? $user_id : null,
                        //
                        'date' => CommonHelper::dbDateFormat($tValue->{'comp date'}),
                        //
                        'info_added_by' => 'third_dca',
                        'specific_demand' => CommonHelper::lengthCheckerSplitStringToLimit(@$specific_demand[0], 30),
                        'general_demand' => CommonHelper::lengthCheckerSplitStringToLimit(@$general_demand[0], 30),
                        'days_on_market' => CommonHelper::decimalRemovalCharacter(@$days_on_market[0]),
                        'phase_renovation' => @$phase_renovation[0],
                        'price_sqft_sale_comps_from' => CommonHelper::decimalRemovalCharacter(@$t0[0]),
                        'price_sqft_sale_comps_to' => CommonHelper::decimalRemovalCharacter(@$t0[1]),
                        'ssd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'first_sale_comps_ssd'}, 250),
                        'gsd_sale_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'third_gsd_sale_comps'}, 250),
                        'rent_gsd' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'first_sale_comps_gsp'}, 50),
                        'price_sqft_sold_comps_from' => CommonHelper::decimalRemovalCharacter(@$t1[0]),
                        'price_sqft_sold_comps_to' => CommonHelper::decimalRemovalCharacter(@$t1[1]),
                        'ssd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'first_sold_comps_ssd'}, 250),
                        'gsd_sold_comps' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'third_gsd_sold_comps'}, 250),
                        'rental_comps_map' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'first_sold_comps_gsp'},250),
                        'p1_value' => CommonHelper::decimalRemovalCharacter(@$p1_dca_value[0]),
                        'p2_value' => CommonHelper::decimalRemovalCharacter(@$p2_dca_value[0]),
                        'p3_value' => CommonHelper::decimalRemovalCharacter(@$p3_dca_value[0]),
                        'rents_zestimate' => $tValue->{'third_rents_zestimate'},
                        'p1_adom' => CommonHelper::decimalRemovalCharacter(@$p1_dca_adom[0]),
                        'p2_adom' => CommonHelper::decimalRemovalCharacter(@$p2_dca_adom[0]),
                        'p3_adom' => CommonHelper::decimalRemovalCharacter(@$p3_dca_adom[0]),
                        'rental_rate' => CommonHelper::decimalRemovalCharacter($tValue->{'third_rental_rate'}),
                        'comp_url_1' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'final_check_dca_url1'}, 250),
                        'comp_url_2' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'final_check_dca_url2'}, 250),
                        'comp_url_3' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'final_check_dca_url3'}, 250),
                        'comp_url_4' => CommonHelper::lengthCheckerSplitStringToLimit($tValue->{'final_check_dca_url4'}, 250),
                        'recommended_cma_arv' => CommonHelper::decimalRemovalCharacter($tValue->{'third_cma'}),
                        'wholetail_value' => CommonHelper::decimalRemovalCharacter($tValue->{'third_wholetail_value'})
                    ];

                    if (empty($user_id) && (count(array_filter($tempValue))) > 2)
                        $qInsert[] = $tempValue;
                    else if ((count(array_filter($tempValue))) > 3)
                        $qInsert[] = $tempValue;


                }
                //echo '<pre>'; print_r($qInsert);
                DB::transaction(function () use (
                    $qInsert
                ) {
                    Log::emergency("CmaArvModel - " . count($qInsert));
                    echo "<br/>CmaArvModel - " . count($qInsert);
                    CmaArvModel::insert($qInsert);

                });
                Log::emergency("CmaArvModel - " . $counter);
            });


        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function findUserByName($name)
    {

        if (empty($name))
            return null;

        // if(strtolower($name) == strtolower("Hank Feller"))
        // return null;
        if (strtolower($name) == strtolower("Mike Tripp"))
            return null;
        if (strtolower($name) == strtolower("Ashlyn Martin"))
            return null;
        if (strtolower($name) == strtolower("David Ginn"))
            return null;


        if (isset($this->keyStoreForRecall[$name]))
            return $this->keyStoreForRecall[$name];

        $t = explode(" ", $name);
        $t1 = @$t[0];
        $t2 = @$t[1];
        if (count($t) > 2) {
            $t2 = end($t);
        }


        $value = $this->findByFirstNameLastName($t1, $t2);
        if (empty($value)) {
            $value = $this->findByFirstName($t1, $t2);
        }
        if (empty($value)) {
            $value = $this->findByLastName($t1, $t2);
        }


        if (!empty($value)) {
            $this->keyStoreForRecall[$name] = $value->uid;
            return $this->keyStoreForRecall[$name];
        } else
            return null;
    }
    private function findByFirstNameLastName($t1, $t2)
    {
        $info = DB::connection('olddb')
            ->table('user_details')
            ->select('*')
            ->where(function ($q) use ($t1, $t2) {
                $q->where('first_name', $t1);
                $q->where('last_name', $t2);
            })->get()->first();

        if (!empty($info)) {
            return $info;
        }
        return false;
    }

    private function findByFirstName($t1, $t2)
    {
        $info = DB::connection('olddb')
            ->table('user_details')
            ->select('*')
            ->where(function ($q) use ($t1, $t2) {
                $q->where('first_name', $t1);
            })->get()->first();

        if (!empty($info)) {
            return $info;
        }
        return false;
    }

    private function findByLastName($t1, $t2)
    {
        $info = DB::connection('olddb')
            ->table('user_details')
            ->select('*')
            ->where(function ($q) use ($t1, $t2) {
                $q->where('last_name', $t2);
            })->get()->first();

        if (!empty($info)) {
            return $info;
        }
        return false;
    }

    private function isuserExists($user_id)
    {
        if (isset($this->isExistsUser[$user_id])) {
            return $this->isExistsUser[$user_id];
        }

        if (User::where('id', $user_id)->exists()) {
            $this->isExistsUser[$user_id] = true;
        } else {
            $this->isExistsUser[$user_id] = false;
        }
        return $this->isExistsUser[$user_id];
    }


    private function propertyQueueListHouses($chunk, $limit, $houseId = "")
    {

        //DELETE FROM `search_operation` WHERE `search_operation`.`house id`
        // in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter = 0;
        $chunkLimit = $limit;

        DB::connection('olddb')
            ->table('search_operation')
            ->select('*')
            ->where(function ($q) use ($houseId) {
                if (!empty($houseId))
                    $q->where('search_operation.house_id', "=", $houseId);
                else
                    $q->where('search_operation.list_id', ">", 0);

            })
            ->orderBy('search_operation.list_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit

            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $qInsert = [];
                $counter = $counter + count($objectTemp);

                if ($counter > $chunkLimit) return false;


                foreach ($objectTemp as $tValue) {

                    $qInsert[] = [
                        "list_id" => $tValue->{'list_id'},
                        "user_id" => $tValue->{'user_id'},
                        "house_id" => $tValue->{'house_id'},
                        // "created_at" => $ttime,
                        // "updated_at" => $ttime,
                    ];
                }

                DB::transaction(function () use (
                    $qInsert
                ) {
                    Log::emergency("Total PropertyQueueListHousesModel List - " . count($qInsert));
                    echo "Total PropertyQueueListHousesModel List - " . count($qInsert);
                    PropertyQueueListHousesModel::insert($qInsert);

                });

                Log::emergency("Total PropertyQueueListHousesModel Completed - " . $counter);
            });


        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function propertyUserFavourite($chunk, $limit, $houseId = "")
    {

        //

        //
        $counter = 0;
        $chunkLimit = $limit;

        if (!empty($houseId)) {
            UserFavoritesModel::where('house_id', '=', $houseId)->delete();
        } else {
            UserFavoritesModel::where('house_id', '>', 0)->delete();
        }

        $last = UserFavoritesModel::orderBy('house_id', 'DESC')->first();
        $primaryHouseId = 0;
        if (!empty($last)) $primaryHouseId = $last->house_id;

        Log::emergency("----------------------HouseId---" . $primaryHouseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('user_favorites')
            ->select('*')
            ->where(function ($q) use ($houseId, $primaryHouseId) {
                if (!empty($houseId))
                    $q->where('user_favorites.house id', "=", $houseId);
                else
                    $q->where('user_favorites.house id', ">", $primaryHouseId);

            })
            ->orderBy('user_favorites.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit

            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");

                $this->print_mem();
                $qInsert = [];
                $counter = $counter + count($objectTemp);

                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $tempValue = ['house_id' => $tValue->{'house id'},
                        'user_id' => $tValue->uid,
                        'created_at' => $tValue->addedon,
                        'updated_at' => $tValue->addedon,

                    ];

                    $qInsert[] = $tempValue;
                }


                DB::transaction(function () use (
                    $qInsert
                ) {
                    Log::emergency("Total UserFavoritesModel List - " . count($qInsert));
                    echo "<br/>Total UserFavoritesModel List - " . count($qInsert);
                    UserFavoritesModel::insert($qInsert);

                });
                Log::emergency("Total UserFavoritesModel Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function alarmMeProcess($chunk, $limit, $houseId = "")
    {

        // DELETE FROM `alarm_me` WHERE `alarm_me`.`houseid`
        // in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter = 0;
        $chunkLimit = $limit;

        //AlarmMeModel::where('alarm_id', '>', 0)->forceDelete();

        $last = AlarmMeModel::orderBy('alarm_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->alarm_id;

        Log::emergency("----------------------AlarmMe Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------AlarmMe Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('alarm_me')
            ->select('*')
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('alarm_me.houseid', "=", $houseId);
                else
                    $q->where('alarm_me.alarm_id', ">", $parimaryId);

            })
            // ->where('alarm_me.alarm_id', ">", $parimaryId)
            ->orderBy('alarm_me.alarm_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $qInsert = [];
                $counter = $counter + count($objectTemp);

                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $tempValue = [
                        'alarm_id' => $tValue->{'alarm_id'},
                        'house_id' => $tValue->{'houseid'},
                        'user_id' => $tValue->uid,
                        'is_send_email' => $tValue->is_send_email,
                        'is_show_alarm' => $tValue->is_show_alarm,
                        'notification_date' => CommonHelper::dbDateFormat($tValue->notification_date, 1),
                        'created_at' => CommonHelper::dbDateFormat($tValue->date_added, 1),
                        'updated_at' => CommonHelper::dbDateFormat(($tValue->last_updated), 1),
                        'deleted_At' => $tValue->is_deleted == 1 ? CommonHelper::dbDateFormat(($tValue->last_updated), 1) : null,
                    ];

                    $qInsert[] = $tempValue;
                }

                DB::transaction(function () use (
                    $qInsert
                ) {
                    Log::emergency("Total Alarm Me List - " . count($qInsert));
                    echo "<br/>Total Alarm Me List - " . count($qInsert);
                    AlarmMeModel::insert($qInsert);

                });
                Log::emergency("Total Alarm Me Completed - " . $counter);
            });


        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function emailsAM($chunk, $limit, $houseId = "")
    {

        // DELETE FROM `hb email alert info` where `house id` in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter = 0;
        $chunkLimit = $limit;
        $last = EmailsAmModel::orderBy('house_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->house_id;

        Log::emergency("----------------------HomeBuyer House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('hb email alert info')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('hb email alert info.house id', "=", $houseId);
                else
                    $q->where('hb email alert info.house id', ">", $parimaryId);
            })
            ->where('hb email alert info.email_type', "=", "am_email")
            ->orderBy('hb email alert info.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $user_id = 0;
                    $uinfo = $this->userService->isEmailExists($tValue->{'email'});
                    if ($uinfo != null) {
                        $user_id = $uinfo->id;
                    }

                    $tempValue[] = [
                        'house_id' => $tValue->{'house id'},
                        'user_id' => $user_id,
                        'email' => CommonHelper::parseEmailJunk($tValue->{'email'}, false, false),
                        // 'created_at' => $tValue->{'created_at'},
                    ];
                }

                // $temp = $this->removeLastHouseIdArray($tempValue);
                DB::transaction(function () use (
                    $tempValue
                ) {
                    Log::emergency("Total EmailsAmModel List - " . count($tempValue));
                    echo "<br/>Total EMAIL AM List - " . count($tempValue);
                    EmailsAmModel::insert($tempValue);
                });
                Log::emergency("Total EmailsAmModel Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function sthbWholesaleBuyerN($chunk, $limit, $houseId = "")
    {
        // No Data Loss in it ..

        // DELETE FROM `sthb` where `house_id` in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter = 0;
        $chunkLimit = $limit;
        $last = WholesaleBuyerNModel::orderBy('wholesale_buyer_n_id', 'DESC')->first();
        $parimaryId = 0;
        if (!empty($last)) $parimaryId = @$last->wholesale_buyer_n_id;

        Log::emergency("----------------------STHB Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");

        DB::connection('olddb')
            ->table('sthb')
            ->select([
                '*',
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('sthb.house_id', "=", $houseId);
                else
                    $q->where('sthb.id', ">", $parimaryId);
            })
            ->orderBy('sthb.id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit
            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $tempValue = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {

                    $user_id = 0;

                    $email = CommonHelper::parseEmailJunk($tValue->{'email'}, false, false);
                    $uinfo = $this->userService->isEmailExists($email);
                    if ($uinfo != null) {
                        $user_id = $uinfo->id;
                    }

                    $tempValue[] = [
                        'wholesale_buyer_n_id' => $tValue->{'id'},
                        'house_id' => $tValue->{'house_id'},
                        'user_id' => $user_id,
                        'name' => CommonHelper::parseEmailJunk($tValue->name, false, false),
                        'email' => $email,
                        'receive_update' => $tValue->{'receive_update'} ? $tValue->{'receive_update'} : 0,
                        'est_amount' => $tValue->{'est_amount'},
                        'est_percent' => $tValue->{'est_percent'},
                        'est_profit' => $tValue->{'est_profit'},
                        'est_payoff_amount' => $tValue->{'est_payoff_amount'},
                        'act_amount' => $tValue->{'act_amount'},
                        'act_percent' => $tValue->{'act_percent'},
                        'act_profit' => $tValue->{'act_profit'},
                        'act_payoff_amount' => $tValue->{'act_payoff_amount'},
                        'added_by' => 0,
                        'created_at' => CommonHelper::dbDateFormat($tValue->{'added_on'}, 1),
                    ];
                }


                DB::transaction(function () use (
                    $tempValue
                ) {
                    Log::emergency("Total STHB N List - " . count($tempValue));
                    echo "<br/>STHB N total- " . count($tempValue);
                    WholesaleBuyerNModel::insert($tempValue);
                });
                Log::emergency("Total STHB N Completed - " . $counter);
            });

        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }


    private function migrateSaleDetailsBidder($chunk, $limit, $houseId = "") {
        $counter    = 0;
        $chunkLimit = $limit;
        $lastSale       = SaleDetailsModel::orderBy('sale_id', 'DESC')->first();
        $parimaryId = 0;
        $sale_id    = 0;
        if (!empty($lastSale)) {
            $sale_id    = @$lastSale->sale_id;
        }
        $lastHouse       = SaleDetailsModel::orderBy('house_id', 'DESC')->first();
        if (!empty($lastHouse)) {
            $parimaryId = @$lastHouse->house_id;
        }
        $last      = SaleBidderModel::orderBy('bidder_id', 'DESC')->first();
        $bidder_id = 0;
        if (!empty($last)) {
            $bidder_id = @$last->bidder_id;
        }
        Log::emergency("----------------------House Id---" . $parimaryId . "-------------------------- ");
        Log::emergency("----------------------Single House Id---" . $houseId . "-------------------------- ");
        DB::connection('olddb')
            ->table('forclosure_information')
            //->leftJoin('home information', 'home information.house id', '=', 'forclosure_information.house id')
            ->leftJoin('forclouser_info', 'forclouser_info.houseid', '=', 'forclosure_information.house id')
            ->leftJoin('forclouser_info2', 'forclouser_info2.houseid', '=', 'forclosure_information.house id')
            ->leftJoin('home_buyers_alias1', 'home_buyers_alias1.house id', '=', 'forclosure_information.house id')
            ->select(['forclosure_information.*'
                ,
                'forclouser_info.*'
                ,
                'forclouser_info2.*'
                ,
                'home_buyers_alias1.*',
                'forclosure_information.house id'
            ])
            ->where(function ($q) use ($houseId, $parimaryId) {
                if (!empty($houseId))
                    $q->where('forclosure_information.house id', "=", $houseId);
                else
                    $q->where('forclosure_information.house id', ">", $parimaryId);
            })
            //->where('home information.is_deleted', "!=", 'yes')
            ->orderBy('forclosure_information.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit, &$sale_id, &$bidder_id
            ) {
                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $sale_details              = [];
                $sale_details_descriptions = [];
                $bidder                    = [];
                $bidder_notes              = [];
                $bidder_id_houseid = [];
                $house_id_sale_id  = [];
                $houseIds          = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;
                foreach ($objectTemp as $fvalue) {
                    $house_id   = $fvalue->{'house id'};
                    $houseIds[] = $house_id;
                    // 551 doesn't exists
                    $fvalue->{'fr_nos_by'} = is_numeric($fvalue->{'fr_nos_by'}) ? $fvalue->{'fr_nos_by'} : null;
                    $fvalue->{'fr_nos_by'} = (in_array($fvalue->{'fr_nos_by'},
                        [551,
                            15,
                            9,
                            8,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                            19,
                            910]
                    )) ? 10 : intval($fvalue->{'fr_nos_by'});
                    if (!$this->isuserExists($fvalue->{'fr_nos_by'})) $fvalue->{'fr_nos_by'} = null;

                    ++$sale_id;
                    $fvalue->{'auction place'} = preg_replace("/[^a-zA-Z0-9.,;:'\s]/", "", $fvalue->{'auction place'});

                    $trustee_scraped = NULL;
                    if (!is_int($fvalue->{'ts date scraped'}) &&
                        !empty($fvalue->{'ts date scraped'}) && (strtotime($fvalue->{'ts date scraped'})) !== false)
                    {
                        $trustee_scraped = strtotime($fvalue->{'ts date scraped'});
                    }

                    // Sale Details
                    $sale_info = ['sale_id'                => $sale_id,
                        'house_id'               => $house_id,
                        'sale_date'              => CommonHelper::dbDateFormat($fvalue->{'sale date'}),
                        'opening_bid'            => CommonHelper::decimalRemovalCharacter($fvalue->{'opening bid'}),
                        // 'sale_type'              => $fvalue->{'sp number'},
                        // 'sale_status'              => $fvalue->{'sp number'},
                        'case_number'            => $fvalue->{'sp number'},
                        'sale_place'             => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'auction place'}, 255),
                        'sale_time'              => $fvalue->{'auction time'},
                        'trustee_file_no'        => $fvalue->{'ts#'},
                        'trustee_scraped'        => $trustee_scraped,
                        'trustee'                => $fvalue->{'trustee'},
                        'trustee_url'            => $fvalue->{'trustee url'},
                        'trustee_address'        => CommonHelper::lengthChecker($fvalue->{'trustee address'}, 255),
                        'trustee_phone'          => CommonHelper::lengthChecker($fvalue->{'trustee phone'}, 15),
                        'trustee_hours'          => $fvalue->{'trustee hours'},
                        'legal_notice_url'       => $fvalue->{'legals url'},
                        'legal_date_pulled'      => CommonHelper::dbDateFormat($fvalue->{'legals date scraped'}),
                        'newspapaer_url'         => $fvalue->{'newspaper site url'},
                        'newspapaer_date_pulled' => CommonHelper::dbDateFormat($fvalue->{'newspaper site date scraped'}),
                        'created_at'             => null,
                        'updated_at'             => null,
                        'priceint'            => CommonHelper::dbIntValValue255($fvalue->{'precinct'}),
                        // precinct forclouser_info2[precinct] // tiny int
                        'auction_com_url'     => @$fvalue->{'auctioncomurl'},
                        // forclouser_info2[auctioncomurl]
                        'auction_date_pulled' => CommonHelper::dbDateFormat($fvalue->{'auctioncomurl_date_scraped'}),
                        // forclouser_info2[auctioncomurl_date_scraped]
                        'nos_by'              => $fvalue->{'fr_nos_by'} ? $fvalue->{'fr_nos_by'} : null,
                        // fr_nos_by forclouser_info2
                        'nos_date'            => CommonHelper::dbDateFormat($fvalue->{'fr_nos_by_date'}),
                        // fr_nos_by_date forclouser_info2
                    ];
                    $sale_details[$sale_id]                = $sale_info;
                    $sale_details[$sale_id]['sale_id']     = $sale_id;
                    $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale date'});
                    $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening bid'});
                    if (!empty($fvalue->{'sale date 2'}) || !empty($fvalue->{'opening bid 2'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale date 2'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening bid 2'});
                    }
                    if (!empty($fvalue->{'sale date 3'}) || !empty($fvalue->{'opening bid 3'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale date 3'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening bid 3'});
                    }
                    if (!empty($fvalue->{'sale date 4'}) || !empty($fvalue->{'opening bid 4'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale date 4'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening bid 4'});
                    }
                    if (!empty($fvalue->{'sale_date_5'}) || !empty($fvalue->{'opening_bid_5'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale_date_5'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening_bid_5'});
                    }
                    if (!empty($fvalue->{'sale_date_6'}) || !empty($fvalue->{'opening_bid_6'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale_date_6'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening_bid_6'});
                    }
                    if (!empty($fvalue->{'sale_date_7'}) || !empty($fvalue->{'opening_bid_7'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale_date_7'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening_bid_7'});
                    }
                    if (!empty($fvalue->{'sale_date_8'}) || !empty($fvalue->{'opening_bid_8'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale_date_8'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening_bid_8'});
                    }
                    if (!empty($fvalue->{'sale_date_9'}) || !empty($fvalue->{'opening_bid_9'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale_date_9'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening_bid_9'});
                    }
                    if (!empty($fvalue->{'sale_date_10'}) || !empty($fvalue->{'opening_bid_10'})) {
                        ++$sale_id;
                        $sale_details[$sale_id]                = $sale_info;
                        $sale_details[$sale_id]['sale_id']     = $sale_id;
                        $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($fvalue->{'sale_date_10'});
                        $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter($fvalue->{'opening_bid_10'});
                    }
                    // more sale date
                    $more_sale_date   = $fvalue->more_sale_date;
                    $more_opening_bid = $fvalue->more_opening_bid;
                    if (!empty($more_sale_date)) {
                        $more_sale_date = json_decode($more_sale_date);
                    }
                    if (!empty($more_opening_bid)) {
                        $more_opening_bid = json_decode($more_opening_bid);
                    }
                    // var_dump($more_opening_bid);
                    if (!empty($more_sale_date)) {
                        foreach ($more_sale_date as $key => $value) {
                            ++$sale_id;
                            $sale_details[$sale_id]                = $sale_info;
                            $sale_details[$sale_id]['sale_id']     = $sale_id;
                            $sale_details[$sale_id]['sale_date']   = CommonHelper::dbDateFormat($value);
                            $sale_details[$sale_id]['opening_bid'] = CommonHelper::decimalRemovalCharacter(@$more_opening_bid[$key]);
                        }
                    }
                    $house_id_sale_id[$house_id] = $sale_id;
                    $fvalue->{'fr_im_by'}  = is_numeric($fvalue->{'fr_im_by'}) && $fvalue->{'fr_im_by'}!= 0 &&  $fvalue->{'fr_im_by'} <= 10546 ? $fvalue->{'fr_im_by'} : null;

                    if (!$this->isuserExists($fvalue->{'fr_im_by'})) $fvalue->{'fr_im_by'} = null;

                    $fvalue->fr_im_by_date = CommonHelper::dbDateFormat($fvalue->fr_im_by_date);
                    $tempDD = ['sale_id'                   => $sale_id,
                        'notice_of_foreclosure'     => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'recorded notices'}, 65535),
                        'before_sale_trustee_notes' => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'before auction notes'}, 65535),
                        'after_sale_trustee_notes'  => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'after auction notes'}, 65535),];
                    if ((count(array_filter($tempDD))) > 1) {
                        $sale_details_descriptions[$sale_id] = $tempDD;
                    }
                    // Bidder 1
                    ++$bidder_id;
                    $temp_bidder = [
                        'bidder_id'                   => $bidder_id,
                        'sale_id'                     => $sale_id,
                        'house_id'                    => $house_id,
                        'name_upset_bidder'           => $fvalue->{'bidder1 name'},
                        //'amount_of_bid'          => CommonHelper::decimalRemovalCharacter($fvalue->scraper_bidder1_amount),
                        'amount_of_bid'               => CommonHelper::decimalRemovalCharacter($fvalue->{'bidder1 amount'}),
                        'bid_date'                    => CommonHelper::dbDateFormat($fvalue->{'bidder1 bid upset date'}),
                        'last_date_to_upset_bid'      => CommonHelper::dbDateFormat($fvalue->{'bidder1 last date upset bid'}),
                        'min_amt_nxt_ub'              => CommonHelper::decimalRemovalCharacter($fvalue->minimum_amount_next_upset_bid),
                        'email'                       => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder1 email'},50),
                        'address'                     => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder1 address'}, 150),
                        'phone'                       => CommonHelper::phoneCharRemove($fvalue->{'bidder1 phone'},20),
                        'date_of_sale'                => CommonHelper::dbDateFormat($fvalue->fr_date_of_sale),
                        'date_of_report'              => CommonHelper::dbDateFormatHIS($fvalue->date_of_report . ' ' . $fvalue->date_of_report_time),
                        'deposit_upset'               => CommonHelper::decimalRemovalCharacter($fvalue->{'deposit_required_upset_bid'}),
                        'name_of_mortage'             => $fvalue->name_of_mortagee,
                        'name_of_cryer'               => $fvalue->name_of_cryer,
                        // 'bid_confirmed' => $fvalue->ssssss,
                        'bid_upset'                   => $fvalue->{'bidder1 upset'} == 'yes' ? 1 : 0,
                        'im_by'                       => $fvalue->fr_im_by,
                        'im_date'                     => $fvalue->fr_im_by_date,
                        'fax'                         => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->fax,14),
                        // Bidder 2 this information will be used.
                        'city'                        => @$fvalue->sdsdsd,
                        'zipcode'                     => @$fvalue->sdsdsd,
                        'attorney_name'               => @$fvalue->sdsdsd,
                        'attorney_address'            => @$fvalue->sdsdsd,
                        'attorney_city'               => @$fvalue->sdsdsd,
                        'attorney_zipcode'            => @$fvalue->sdsdsd,
                        'attorney_phone'              => @$fvalue->sdsdsd,
                        'deposit_clerk'               => @$fvalue->sdsdsd,
                        'filling_date'                => CommonHelper::dbDateFormat(@$fvalue->upset_date_of_filing),
                        'last_date_to_next_upset_bid' => CommonHelper::dbDateFormat(@$fvalue->{'bidder1 last date upset bid'}),
                        'deposit_amt_nxt_ub'          => @$fvalue->sdsdsd,
                        'deputy_csc'                  => @$fvalue->sdsdsd ? 1 : 0,
                        'assistant_csc'               => @$fvalue->sdsdsd ? 1 : 0,
                        'clerk_superior_court'        => @$fvalue->sdsdsd ? 1 : 0,
                    ];
                    $bidder[]    = $temp_bidder;
                    if (!empty($fvalue->{'bidder1 notes'})) {
                        $bidder_notes[] = [
                            'bidder_id' => $bidder_id,
                            'notes'     => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder1 notes'}, 65535),
                        ];
                    }
                    $bidder_id_houseid['bidder1'][$house_id] = $bidder_id;
                    // Bidder 2
                    ++$bidder_id;
                    $temp_bidder = [
                        'bidder_id'              => $bidder_id,
                        'sale_id'                => $sale_id,
                        'house_id'               => $house_id,
                        'name_upset_bidder'      => $fvalue->{'bidder2 name'},
                        //'amount_of_bid'          => CommonHelper::decimalRemovalCharacter($fvalue->scraper_bidder1_amount),
                        'amount_of_bid'          => CommonHelper::decimalRemovalCharacter($fvalue->{'bidder2 amount'}),
                        'bid_date'               => CommonHelper::dbDateFormat($fvalue->{'bidder2 bid upset date'}),
                        'last_date_to_upset_bid' => CommonHelper::dbDateFormat($fvalue->{'bidder2 last date upset bid'}),
                        'min_amt_nxt_ub'         => CommonHelper::decimalRemovalCharacter($fvalue->upset_minimum_amount_next_upset_bid),
                        'email'                  => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder2 email'},50),
                        'address'                => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder2 address'},150),
                        'phone'                  => CommonHelper::phoneCharRemove($fvalue->{'bidder2 phone'},20),
                        'date_of_sale'           => CommonHelper::dbDateFormat(@$fvalue->sssssss),
                        'date_of_report'         => @$fvalue->ssssss,
                        'deposit_upset'               => CommonHelper::decimalRemovalCharacter($fvalue->{'upset_amount_of_deposit_next_upset_bid'}),
                        'name_of_mortage'             => @$fvalue->ssss,
                        'name_of_cryer'               => @$fvalue->ssss,
                        // 'bid_confirmed' => $fvalue->ssssss,
                        'bid_upset'                   => $fvalue->{'bidder2 upset'} == 'yes' ? 1 : 0,
                        'im_by'                       => $fvalue->fr_im_by,
                        'im_date'                     => $fvalue->fr_im_by_date,
                        'fax'                         => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->fax,14),
                        // Bidder 2 this information will be used.
                        'city'                        => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_city_of_upset_bidder,50),
                        'zipcode'                     => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_zipcode_of_upset_bidder,10),
                        'attorney_name'               => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_name_of_attorny_upset_bidder,100),
                        'attorney_address'            => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_address_of_attorny_upset_bidder,100),
                        'attorney_city'               => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_city_of_attorny_upset_bidder,50),
                        'attorney_zipcode'            => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_zipcode_of_attorny_upset_bidder,10),
                        'attorney_phone'              => CommonHelper::lengthCheckerSplitStringToLimit(@$fvalue->upset_phone_of_attorny_upset_bidder,20),
                        'deposit_clerk'               => CommonHelper::decimalRemovalCharacter($fvalue->upset_deposit_with_clerck),
                        'filling_date'                => CommonHelper::dbDateFormat($fvalue->upset_date_of_filing),
                        'last_date_to_next_upset_bid' => CommonHelper::dbDateFormat($fvalue->{'bidder2 last date upset bid'}),
                        'deposit_amt_nxt_ub'          => CommonHelper::decimalRemovalCharacter($fvalue->upset_amount_of_deposit_next_upset_bid),
                        'deputy_csc'                  => @$fvalue->upset_deputy_csc ? 1 : 0,
                        'assistant_csc'               => @$fvalue->upset_assitant_csc ? 1 : 0,
                        'clerk_superior_court'        => @$fvalue->upset_clerck_of_superior_court ? 1 : 0,
                    ];
                    if (!empty($fvalue->{'bidder2 notes'})) {
                        $bidder_notes[] = [
                            'bidder_id' => $bidder_id,
                            'notes'     => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder2 notes'}, 65535),
                        ];
                    }
                    $bidder2COunt = $temp_bidder;
                    unset($bidder2COunt['im_by']);
                    unset($bidder2COunt['im_date']);
                    if ((count(array_filter($bidder2COunt, function($value){
                            return ($value !== false && $value !== null && $value !== '' && $value !== '0.00'&& $value !== '0' && $value !== 0);
                        }))) > 3 || !empty($fvalue->{'bidder2 notes'}))
                    {
                        $bidder_id_houseid['bidder2'][$house_id] = $bidder_id;
                        $bidder[]    = $temp_bidder;
                    }
                    else{
                        --$bidder_id;
                    }
                    $bidder_n                = $this->change_sp_input_json($fvalue);
                    $total_bidders_available = $bidder_n['upset_name_of_attorny_upset_bidder_3_n'] != null ? count(@$bidder_n['upset_name_of_attorny_upset_bidder_3_n']) : 0;
                    $total_bidders_available = ($total_bidders_available < 2) ? 4 : $total_bidders_available;
                    for ($i = 1; $i <= ($total_bidders_available); $i++) {
                        $bid_no         = 2 + $i;
                        $bid_no_start   = $i - 1;
                        $bid_no_start_7 = $bid_no - 7;
                        // Bidder 3 to 7
                        ++$bidder_id;
                        if ($bid_no <= 6) {
                            $temp_bidder1 = [
                                'bidder_id'              => $bidder_id,
                                'sale_id'                => $sale_id,
                                'house_id'               => $house_id,
                                'name_upset_bidder'      => $fvalue->{'bidder' . $bid_no . ' name'},
                                'email'                  => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder' . $bid_no . ' email'},50),
                                'address'                => $fvalue->{'bidder' . $bid_no . ' address'},
                                'phone'                  => CommonHelper::phoneCharRemove(@$fvalue->{'bidder' . $bid_no . ' phone'},20),
                                'city'                   => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_city_of_upset_bidder_3_n'][$bid_no_start],50),
                                'zipcode'                => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_zipcode_of_upset_bidder_3_n'][$bid_no_start],10),
                                'bid_upset'              => @$fvalue->{'bidder' . $bid_no . ' upset'} == 'yes' ? 1 : 0,
                                'amount_of_bid'          => CommonHelper::decimalRemovalCharacter($fvalue->{'bidder' . $bid_no . ' amount'}),
                                'bid_date'               => CommonHelper::dbDateFormat($fvalue->{'bidder' . $bid_no . ' bid upset date'}),
                                'last_date_to_upset_bid' => CommonHelper::dbDateFormat($fvalue->{'bidder' . $bid_no . ' last date upset bid'}),
                                'deputy_csc'                  => @$fvalue->{'upset_deputy_csc_' . $bid_no} ? 1 : 0,
                                'assistant_csc'               => @$fvalue->{'upset_assitant_csc_' . $bid_no} ? 1 : 0,
                                'clerk_superior_court'        => @$fvalue->{'upset_clerck_of_superior_court_' . $bid_no} ? 1 : 0,
                                'last_date_to_next_upset_bid' => CommonHelper::dbDateFormat($fvalue->{'bidder' . $bid_no . ' last date upset bid'}),
                            ];
                            if (!empty($fvalue->{'bidder' . $bid_no . ' notes'})) {
                                $bidder_notes[] = [
                                    'bidder_id' => $bidder_id,
                                    'notes'     => CommonHelper::lengthCheckerSplitStringToLimit($fvalue->{'bidder' . $bid_no . ' notes'}, 65535),
                                ];
                            }
                        }
                        else {
                            // working here
                            $temp_bidder1 = [
                                'bidder_id'                   => $bidder_id,
                                'sale_id'                     => $sale_id,
                                'house_id'                    => $house_id,
                                'name_upset_bidder'           => $bidder_n['fc_bidder_name_7_n'][$bid_no_start_7],
                                'email'                       => CommonHelper::lengthCheckerSplitStringToLimit($bidder_n['fc_bidder_email_7_n'][$bid_no_start_7],50),
                                'address'                     => $bidder_n['fc_bidder_address_7_n'][$bid_no_start_7],
                                'phone'                       => CommonHelper::phoneCharRemove(@$bidder_n['fc_bidder_phone_7_n'][$bid_no_start_7],20),
                                'city'                        => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_city_of_upset_bidder_3_n'][$bid_no_start],50),
                                'zipcode'                     => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_zipcode_of_upset_bidder_3_n'][$bid_no_start],10),
                                'bid_upset'                   => @$bidder_n['fc_bidder_upset_7_n'][$bid_no_start_7] == 'yes' ? 1 : 0,
                                'amount_of_bid'               => CommonHelper::decimalRemovalCharacter($bidder_n['fc_bidder7_n_amount'][$bid_no_start_7]),
                                'bid_date'                    => CommonHelper::dbDateFormat($bidder_n['fc_bidder6_n_upset_date'][$bid_no_start_7]),
                                'last_date_to_upset_bid'      => CommonHelper::dbDateFormat($bidder_n['upset_last_day_next_upset_bid_7_n'][$bid_no_start_7]),
                                'deputy_csc'                  => @$bidder_n['upset_deputy_csc_7_n'][$bid_no_start_7] ? 1 : 0,
                                'assistant_csc'               => @$bidder_n['upset_assitant_csc_7_n'][$bid_no_start_7] ? 1 : 0,
                                'clerk_superior_court'        => @$bidder_n['upset_clerck_of_superior_court_7_n'][$bid_no_start_7] ? 1 : 0,
                                'last_date_to_next_upset_bid' => CommonHelper::dbDateFormat($bidder_n['upset_last_day_next_upset_bid_7_n'][$bid_no_start_7]),
                            ];
                            if (!empty(trim($bidder_n['fc_bidder_notes_7_n'][$bid_no_start_7]))) {
                                $bidder_notes[] = [
                                    'bidder_id' => $bidder_id,
                                    'notes'     => CommonHelper::lengthCheckerSplitStringToLimit($bidder_n['fc_bidder_notes_7_n'][$bid_no_start_7], 65535),
                                ];
                            }
                        }
                        $strt_upset   = 3;
                        $strt_upset_7 = 7;
                        $strt_upset_6 = 6;
                        $upset_n      = $bid_no;
                        $temp_bidder2 = [
                            //'amount_of_bid'          => CommonHelper::decimalRemovalCharacter($fvalue->scraper_bidder1_amount),
                            'min_amt_nxt_ub'     => CommonHelper::decimalRemovalCharacter(@$bidder_n['upset_minimum_amount_next_upset_bid_3_n'][$upset_n - $strt_upset]),
                            'date_of_sale'       => CommonHelper::dbDateFormat(@$fvalue->sssssss),
                            'date_of_report'     => null,
                            'deposit_upset'      => CommonHelper::decimalRemovalCharacter(@$bidder_n['upset_amount_of_deposit_next_upset_bid_3_n'][$upset_n - $strt_upset]),
                            // ToDo: Verify this one @$bidder_n['upset_name_of_mortagee_3_n'][$upset_n - $strt_upset],
                            'name_of_mortage'    => @$fvalue->sssss,
                            'name_of_cryer'      => @$fvalue->sssss,
                            // 'bid_confirmed' => $fvalue->ssssss,
                            'im_by'              => $fvalue->fr_im_by,
                            'im_date'            => $fvalue->fr_im_by_date,
                            'fax'                => "",
                            // Bidder 2 this information will be used.
                            'attorney_name'      => @$bidder_n['upset_name_of_attorny_upset_bidder_3_n'][$upset_n - $strt_upset],
                            'attorney_address'   => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_address_of_attorny_upset_bidder_3_n'][$upset_n - $strt_upset],100),
                            'attorney_city'      => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_city_of_attorny_upset_bidder_3_n'][$upset_n - $strt_upset],50),
                            'attorney_zipcode'   => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_zipcode_of_attorny_upset_bidder_3_n'][$upset_n - $strt_upset],10),
                            'attorney_phone'     => CommonHelper::lengthCheckerSplitStringToLimit(@$bidder_n['upset_phone_of_attorny_upset_bidder_3_n'][$upset_n - $strt_upset], 20),
                            'deposit_clerk'      => CommonHelper::decimalRemovalCharacter(@$bidder_n['upset_deposit_with_clerck_3_n'][$upset_n - $strt_upset]),
                            'filling_date'       => CommonHelper::dbDateFormat(@$bidder_n['upset_date_of_filing_3_n'][$upset_n - $strt_upset]),
                            'deposit_amt_nxt_ub' => CommonHelper::decimalRemovalCharacter(@$bidder_n['upset_amount_of_deposit_next_upset_bid_3_n'][$upset_n - $strt_upset]),
                        ];
                        $tempBidder   = $temp_bidder1 + $temp_bidder2;
                        // Count total filled column
                        unset($tempBidder['im_by']);
                        unset($tempBidder['im_date']);

                        if ((count(array_filter($tempBidder, function($value){
                                return ($value !== false && $value !== null && $value !== '' && $value !== '0.00'&& $value !== '0' && $value !== 0
                                );
                            }))) > 3

                            || (($bid_no <= 6 && !empty($fvalue->{'bidder' . $bid_no . ' notes'}))
                               ||  ($bid_no > 6 && !empty($bidder_n['fc_bidder_notes_7_n'][$bid_no_start_7]))
                            )

                        )
                        {
                            $bidder[] = $temp_bidder1 + $temp_bidder2;
                            $bidder_id_houseid['bidder' . $bid_no][$house_id] = $bidder_id;
                        }
                        else{
                            --$bidder_id;
                        }
                    }
                }
//                echo '<pre>';
//                print_r($sale_details);
//                print_r($bidder);
//                print_r($bidder_notes);
//                //print_r($bidderDocumentInfo);
//                print_r($sale_details_descriptions);
//                //print_r($recordedDocuments);
//                die;
                // Get Bidder
                $bidderDocumentInfo = $this->getSaleBidderDocument($houseIds, $bidder_id_houseid);
                $recordedDocuments  = $this->recordedDocument($houseIds, $house_id_sale_id);
                DB::transaction(function () use (
                    $sale_details, $bidder, $bidder_notes, $bidderDocumentInfo, $sale_details_descriptions,
                    $recordedDocuments
                ) {
                    Log::emergency("Total Sale Details- " . count($sale_details));
                    # Sale Details
                    $array_chunk = array_chunk($sale_details, 500, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total SaleDetailsModel - " . count($value));
                        SaleDetailsModel::insert($value);
                    }
                    # Sale Details
                    $array_chunk = array_chunk($bidder, 500, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total SaleBidderModel - " . count($value));
                        SaleBidderModel::insert($value);
                    }
                    # Sale Details
                    $array_chunk = array_chunk($bidder_notes, 500, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total SaleBidderNotesModel - " . count($value));
                        SaleBidderNotesModel::insert($value);
                    }
                    # Sale Details
                    $array_chunk = array_chunk($bidderDocumentInfo, 500, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total DocumentBidderModel - " . count($value));
                        DocumentBidderModel::insert($value);
                    }
                    $array_chunk = array_chunk($sale_details_descriptions, 500, true);
                    foreach ($array_chunk as $key => $value) {
                        Log::emergency("Total SaleDetailsDescriptionsModel - " . count($value));
                        SaleDetailsDescriptionsModel::insert($value);
                    }
                    // #Recorded Document/Picture --- sale details document
                    Log::emergency("Total Document/Picture - " . count($recordedDocuments));
                    DocumentSaleModel::insert($recordedDocuments);
                });
                Log::emergency("Total Sale Details N Completed - " . $counter);
            });
        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }

    private function recordedDocument($house_ids, $house_id_sale_id) {
        $tempSql = DB::connection('olddb')
            ->table('common_documents')
            ->select('*')
            ->whereIn('common_documents.house_id', $house_ids)
            ->where('is_deleted', '!=', 'yes')//->limit(10000)
            ->where('object_type', '=', 'recorded')//->limit(10000)
            ->get();
        Log::emergency("DB Common Documents By - " . count($tempSql));
        $recorded_document = [];
        if ($tempSql != null) {
            foreach ($tempSql as $tkey => $tvalue) {
                $house_id = $tvalue->{'house_id'};
                $document_date = '';
                // Date parsing
                $t    = substr($tvalue->doc_name, 0, 10);
                $t    = str_replace(" ", "-", trim($t));
                $temp = ((bool)strtotime($t));
                if ($temp) {
                    $document_date = date('Y-m-d', strtotime($t));
                }
                else {
                    $t    = explode(".", $tvalue->doc_name);
                    $t    = $t[0];
                    $t    = (strlen($t) > 10) ? substr($t, -10) : $t;
                    $t    = str_replace(" ", "-", trim($t));
                    $temp = ((bool)strtotime($t));
                    if ($temp) {
                        $document_date = date('Y-m-d', strtotime($t));
                    }
                }
                // Date parsing
                $temp                  = [];
                $temp['house_id']      = $house_id;
                $temp['added_by']      = intval($tvalue->added_by);
                $temp['document_type'] = 99; // Other
                $temp['org_name']      = $tvalue->doc_name;
                $temp['store_name']    = $tvalue->store_doc_name;
                $temp['other_name']    = '..';
                $temp['case_number']   = '..';
                $temp['document_date'] = CommonHelper::dbDateFormat($document_date);
                $temp['created_at']    = $tvalue->created_date;
                if ($tvalue->object_type == 'recorded') {
                    unset($temp['house_id']);
                    $temp['added_by']    = intval($temp['added_by']);
                    $temp['sale_id']     = $house_id_sale_id[$house_id];
                    $recorded_document[] = $temp;
                }
            }
        }
        return $recorded_document;
    }
    private function change_sp_input_json(&$temp) {
        $json_data_convert                                               = (array)$temp;
        $json_data_convert['upset_city_of_upset_bidder_3_n']             = json_decode($json_data_convert["upset_city_of_upset_bidder_3_n"], true);
        $json_data_convert['upset_name_of_mortagee_3_n']                 = json_decode($json_data_convert["upset_name_of_mortagee_3_n"], true);
        $json_data_convert["upset_zipcode_of_upset_bidder_3_n"]          = json_decode($json_data_convert["upset_zipcode_of_upset_bidder_3_n"], true);
        $json_data_convert["upset_phone_upset_bidder_3_n"]               = json_decode($json_data_convert["upset_phone_upset_bidder_3_n"], true);
        $json_data_convert["upset_owner_of_reord_3_n"]                   = json_decode($json_data_convert["upset_owner_of_reord_3_n"], true);
        $json_data_convert["upset_name_of_trustee_3_n"]                  = json_decode($json_data_convert["upset_name_of_trustee_3_n"], true);
        $json_data_convert["upset_name_of_holder_3_n"]                   = json_decode($json_data_convert["upset_name_of_holder_3_n"], true);
        $json_data_convert["upset_name_of_attorny_upset_bidder_3_n"]     = json_decode($json_data_convert["upset_name_of_attorny_upset_bidder_3_n"], true);
        $json_data_convert["upset_address_of_attorny_upset_bidder_3_n"]  = json_decode($json_data_convert["upset_address_of_attorny_upset_bidder_3_n"], true);
        $json_data_convert["upset_city_of_attorny_upset_bidder_3_n"]     = json_decode($json_data_convert["upset_city_of_attorny_upset_bidder_3_n"], true);
        $json_data_convert["upset_zipcode_of_attorny_upset_bidder_3_n"]  = json_decode($json_data_convert["upset_zipcode_of_attorny_upset_bidder_3_n"], true);
        $json_data_convert["upset_phone_of_attorny_upset_bidder_3_n"]    = json_decode($json_data_convert["upset_phone_of_attorny_upset_bidder_3_n"], true);
        $json_data_convert["upset_amount_of_new_upset_bid_3_n"]          = json_decode($json_data_convert["upset_amount_of_new_upset_bid_3_n"], true);
        $json_data_convert["upset_deposit_with_clerck_3_n"]              = json_decode($json_data_convert["upset_deposit_with_clerck_3_n"], true);
        $json_data_convert["upset_date_of_filing_3_n"]                   = json_decode($json_data_convert["upset_date_of_filing_3_n"], true);
        $json_data_convert["upset_notice_trustee_date_3_n"]              = json_decode($json_data_convert["upset_notice_trustee_date_3_n"], true);
        $json_data_convert["upset_minimum_amount_next_upset_bid_3_n"]    = json_decode($json_data_convert["upset_minimum_amount_next_upset_bid_3_n"], true);
        $json_data_convert["upset_amount_of_deposit_next_upset_bid_3_n"] = json_decode($json_data_convert["upset_amount_of_deposit_next_upset_bid_3_n"], true);
        $json_data_convert["upset_deposit_with_clerck_name_3_n"]         = json_decode($json_data_convert["upset_deposit_with_clerck_name_3_n"], true);
        $json_data_convert["name_of_cryer_3_n"]                          = json_decode($json_data_convert["name_of_cryer_3_n"], true);
        if (isset($json_data_convert["fc_bidder7_n_amount"]))
            $json_data_convert["fc_bidder7_n_amount"] = json_decode($json_data_convert["fc_bidder7_n_amount"]);
        if (isset($json_data_convert["upset_last_day_next_upset_bid_7_n"]))
            $json_data_convert["upset_last_day_next_upset_bid_7_n"] = json_decode($json_data_convert["upset_last_day_next_upset_bid_7_n"], true);
        if (isset($json_data_convert["fc_bidder6_n_upset_date"]))
            $json_data_convert["fc_bidder6_n_upset_date"] = json_decode($json_data_convert["fc_bidder6_n_upset_date"], true);
        if (isset($json_data_convert["fc_bidder_name_7_n"]))
            $json_data_convert["fc_bidder_name_7_n"] = json_decode($json_data_convert["fc_bidder_name_7_n"], true);
        if (isset($json_data_convert["fc_bidder_address_7_n"]))
            $json_data_convert["fc_bidder_address_7_n"] = json_decode($json_data_convert["fc_bidder_address_7_n"], true);
        if (isset($json_data_convert["fc_bidder_phone_7_n"]))
            $json_data_convert["fc_bidder_phone_7_n"] = json_decode($json_data_convert["fc_bidder_phone_7_n"], true);
        if (isset($json_data_convert["fc_bidder_upset_7_n"]))
            $json_data_convert["fc_bidder_upset_7_n"] = json_decode($json_data_convert["fc_bidder_upset_7_n"], true);
        if (isset($json_data_convert["fc_bidder_email_7_n"]))
            $json_data_convert["fc_bidder_email_7_n"] = json_decode($json_data_convert["fc_bidder_email_7_n"], true);
        if (isset($json_data_convert["fc_bidder_notes_7_n"]))
            $json_data_convert["fc_bidder_notes_7_n"] = json_decode($json_data_convert["fc_bidder_notes_7_n"], true);
        if (isset($json_data_convert["upset_deputy_csc_7_n"]))
            $json_data_convert["upset_deputy_csc_7_n"] = json_decode($json_data_convert["upset_deputy_csc_7_n"], true);
        if (isset($json_data_convert["upset_assitant_csc_7_n"]))
            $json_data_convert["upset_assitant_csc_7_n"] = json_decode($json_data_convert["upset_assitant_csc_7_n"], true);
        if (isset($json_data_convert["fc_bidder_notes_7_n"]))
            $json_data_convert["upset_clerck_of_superior_court_7_n"] = json_decode($json_data_convert["upset_clerck_of_superior_court_7_n"], true);
        return $json_data_convert;
    }
    private function getSaleBidderDocument($houseIds, $bidder_id_houseid) {
        $bidder_document = [];
        $tempSql         = DB::connection('olddb')
            ->table('bidder_documents')
            ->select('*')
            ->whereIn('bidder_documents.house_id', $houseIds)
            ->where('is_deleted', '=', 'no')
            ->get();
        Log::emergency("DB Common getSaleBidderDocument By - " . count($tempSql));
        if ($tempSql != null) {
            foreach ($tempSql as $tkey => $tvalue) {
                $house_id      = $tvalue->{'house_id'};
                $document_date = '';
                // Date parsing
                $t    = substr($tvalue->bidder_doc_name, 0, 10);
                $t    = str_replace(" ", "-", trim($t));
                $temp = ((bool)strtotime($t));
                if ($temp) {
                    $document_date = date('Y-m-d', strtotime($t));
                }
                else {
                    $t    = explode(".", $tvalue->bidder_doc_name);
                    $t    = $t[0];
                    $t    = (strlen($t) > 10) ? substr($t, -10) : $t;
                    $t    = str_replace(" ", "-", trim($t));
                    $temp = ((bool)strtotime($t));
                    if ($temp) {
                        $document_date = date('Y-m-d', strtotime($t));
                    }
                }
                // Date parsing
                $temp                  = [];
                $temp['added_by']      = intval($tvalue->added_by);
                $temp['document_type'] = 99; // Other
                $temp['org_name']      = $tvalue->bidder_doc_name;
                $temp['store_name']    = $tvalue->store_doc_name;
                $temp['other_name']    = '..';
                $temp['case_number']   = '..';
                $temp['document_date'] = CommonHelper::dbDateFormat($document_date);
                $temp['created_at']    = $tvalue->created_date;
                if(isset($bidder_id_houseid[$tvalue->bidder_type][$house_id]))
                {
                    $temp['bidder_id'] = $bidder_id_houseid[$tvalue->bidder_type][$house_id];
                    $bidder_document[] = $temp;
                }
            }
        }
        return $bidder_document;
    }


    private function propertyQueueList($chunk, $limit)
    {

        // DELETE FROM `property_queue_list` WHERE `property_queue_list`.`user_id`
        // not in (select user_details.uid from user_details )

        $counter = 0;
        $chunkLimit = $limit;

        DB::connection('olddb')
            ->table('property_queue_list')
            ->select('*')
            ->orderBy('property_queue_list.list_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit

            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");
                $this->print_mem();
                $qInsert = [];
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;


                foreach ($objectTemp as $tValue) {

                    $ttime = CommonHelper::dbDateFormat($tValue->{'date_added'}, 1);
                    $qInsert[] = [
                        "list_id" => $tValue->{'list_id'},
                        "name" => $tValue->{'list_name'},
                        "user_id" => $tValue->{'user_id'},
                        "created_at" => $ttime,
                        "updated_at" => $ttime,];
                }

                DB::transaction(function () use (
                    $qInsert
                ) {
                    Log::emergency("Total PropertyQueueListModel - " . count($qInsert));
                    echo "Total PropertyQueueListModel - " . count($qInsert);
                    PropertyQueueListModel::insert($qInsert);

                });

                Log::emergency("Total PropertyQueueListModel Completed - " . $counter);
            });


        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
    }

    private function propertyDescription($chunk, $limit, $houseId = "")
    {

        //DELETE FROM `search_operation` WHERE `search_operation`.`house id`
        // in (select `house id` from `home information` where is_deleted = 'yes' )

        $counter = 0;
        $chunkLimit = $limit;

        $last = PropertyDescriptionsModel::orderBy('house_id', 'DESC')->first();
        $primaryHouseId = 0;
        if (!empty($last)) $primaryHouseId = $last->house_id;


        Log::emergency("----------------------HouseId---" . $primaryHouseId . "-------------------------- ");
        Log::emergency("----------------------HouseId---" . $houseId . "-------------------------- ");


        DB::connection('olddb')
            ->table('home information')
            ->leftJoin('home_info2', 'house_id', '=', 'home information.house id')
            ->select(['house id',
                'notesofCondition',
                'legalDescription'])
            ->where(function ($q) use ($houseId, $primaryHouseId) {
                if (!empty($houseId))
                    $q->where('home information.house id', "=", $houseId);
                else
                    $q->where('home information.house id', ">", $primaryHouseId);

            })
            //->where('home information.house id', ">", $primaryHouseId)
            ->where(function ($q) {
                $q->whereNotNull('notesofCondition')
                    ->orWhereNotNull('legalDescription');
            })
            ->where('is_deleted', '!=', 'yes')

            ->orderBy('home information.house id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit

            ) {

                Log::emergency("-------------------------" . $counter . "-------------------------- ");

                $this->print_mem();
                $qInsert = [];
                $counter = $counter + count($objectTemp);

                if ($counter > $chunkLimit) return false;


                foreach ($objectTemp as $tValue) {

                    $tempValue = ['house_id' => $tValue->{'house id'},
                        'property_description' => $tValue->notesofCondition,
                        'legal_description' => $tValue->legalDescription,];

                    if ((count(array_filter($tempValue))) > 1) $qInsert[] = $tempValue;
                }

                DB::transaction(function () use (
                    $qInsert
                ) {
                    Log::emergency(" PropertyDescriptionsModel List - " . count($qInsert));
                    echo "<br/>PropertyDescriptionsModel List - " . count($qInsert);
                    PropertyDescriptionsModel::insert($qInsert);

                });

                Log::emergency("PropertyDescriptionsModel Completed - " . $counter);
            });


        Log::emergency("Queue: Final End of Records Completed - " . $chunkLimit);
        echo "Queue: Final End of Records Completed - " . $chunkLimit;
    }


    private function transferBraintreePaymentLog()
    {
        Log::info('transferBraintreePaymentLog: called');
        echo "Start Time: ". date('Y-m-d H:i:s');

        $dataBy = DB::connection('olddb')
            ->table('user_payment_log')
            ->select('*')
            ->where('id','>',0)
            ->orderBy('user_payment_log.id', 'asc')->get();

        Log::emergency("User Payment Log - " . ($dataBy->count()));

        if ($dataBy != null) {
            foreach ($dataBy as $tkey => $tvalue) {
                $transactionResult = DB::transaction(function () use ( $tvalue) {
                    $insert = [];
                    $insert['id'] = $tvalue->id;
                    $insert['user_id'] = $tvalue->user_id;
                    $insert['status'] = $tvalue->status;
                    $insert['amount'] = !empty($tvalue->price)?$tvalue->price:NULL;
                    $insert['error_code'] = $tvalue->error_code;
                    $insert['response'] = $tvalue->response;
                    $insert['payment_type'] = $tvalue->payment_type;
                    $insert['created_at'] = strtotime($tvalue->date_added);
                    $insert['updated_at'] = strtotime($tvalue->date_added);

                    UserPaymentLog::updateOrCreate(['id'=>$tvalue->id],$insert);
                });
            }
        }


    }

    public function updateSaleDateTrusteeScrapeDate()
    {
        set_time_limit(0);

        Log::info('updateSaleDateTrusteeScrapeDate: called');
        echo "Start Time: ". date('Y-m0d H:i:s');
        // Check key first before migration
        $key = $this->request->input('key');
        $sale_id = $this->request->input('sale_id');
        $chunk = $this->request->input('chunk')?$this->request->input('chunk'):5000;
        $chunkLimit = $this->request->input('chunklimit')?$this->request->input('chunklimit'):100000;

        if ($key != "rati12" || empty($sale_id)) {
            return response()->json(['status' => 'failed',
                'message' => __("error_messages.something_wrong")], 200);
        }

        $counter = 0;
        //$chunk = 10000;
        //$chunkLimit = 100000;
        SaleDetailsModel::
            select('*')
            ->where('sale_id','>',$sale_id)
            ->whereNotNull('trustee_scraped')
            ->orderBy('sale_id', 'asc')
            ->chunk($chunk, function ($objectTemp) use (
                &$counter, $chunkLimit

            ) {

                echo ("<br/>-------------------------counter" . $counter . "-------------------------- ");
                echo ("<br/>-------------------------chunklimit" . $chunkLimit . "-------------------------- ");
                //$this->print_mem();
                $counter = $counter + count($objectTemp);
                if ($counter > $chunkLimit) return false;

                foreach ($objectTemp as $tValue) {
                    echo ("<br/>SaleId : " . $tValue->sale_id . ", trustee_scraped: ".$tValue->trustee_scraped );

                    var_dump(is_numeric($tValue->trustee_scraped));
                    var_dump(strtotime($tValue->trustee_scraped));
                    if (!is_numeric($tValue->trustee_scraped) &&
                        !empty($tValue->trustee_scraped) && (strtotime($tValue->trustee_scraped)) !== false) {

                        $info = [];
                        $info['trustee_scraped'] = strtotime($tValue->trustee_scraped);
                        $tValue->update($info);
                    }
                    else if (!is_numeric($tValue->trustee_scraped) &&
                        !empty($tValue->trustee_scraped) && (strtotime($tValue->trustee_scraped)) === false) {
                        $info = [];
                        $info['trustee_scraped'] = NULL;
                        $tValue->update($info);

                    }

                }

               echo ("<br/>Total  Completed - " . $counter);
            });

        echo "End Time: ". date('Y-m0d H:i:s');
        Log::info('updateSaleDateTrusteeScrapeDate: end');

    }


}