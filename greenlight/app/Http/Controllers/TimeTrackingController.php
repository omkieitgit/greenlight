<?php

namespace App\Http\Controllers;

//use App\Http\Validations\GeoValidations;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Services\TimeTrackingService;
use App\Services\UserService;
use App\Models\AcTimeTrackingModel;


class TimeTrackingController extends Controller
{

    private $request;
    private $timeTrackingService;
    private $userService;


    public function __construct(TimeTrackingService $timeTrackingService, 
                                Request $request,
                                UserService $userService ) {
        Log::info("TimeTrackingController: __construct called");
         $this->request                  = $request;
         $this->timeTrackingService      = $timeTrackingService;
         $this->userService             = $userService;
    }

     /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($house_id)
    {
        Log::info("TimeTrackingController: index called");
        $info['time_tracking_list'] = $this->timeTrackingService->findAllByHouseId($house_id);
        $info['time_tracking_summery']=$this->timeTrackingService->getTimeTrackingSummery($house_id);
       
        if (empty($info))
        {
            return response()->json(['row'=>[],'status' => 'success','message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','row' => $info], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreate($house_id)
    {
        Log::info("McdController: updateOrCreate called");

        ## check input validation
        //$rules = GeoValidations::updateOrCreateValidations();

        $all             = $this->request->all();
        $all['house_id'] = $house_id;
        $id              = $all['id']??'';
        $all['user_id']  = $this->userService->user_id();
        //$validator       = Validator::make($all, $rules);
        //$validator->validate();
        
        $info=$this->timeTrackingService->updateCreate($id, $all);

        return response()->json(['status' => 'success','message' => __("messages.record_saved"),'data'=>$info], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

        Log::info("McdController: mcd called");
        try
        {
            $info = AcTimeTrackingModel::find($id);
            if ($info != null)
            {
                $info->delete();
                return response()->json(['status'  => 'success','message' => __("messages.record_delete")], 200);
            }
            else
            {
                return response()->json(['status'  => 'failed','message' => __("error_messages.record_not_exists")], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status'  => 'failed','message' => __("error_messages.something_wrong")], 400);
        }
    }

    
}
