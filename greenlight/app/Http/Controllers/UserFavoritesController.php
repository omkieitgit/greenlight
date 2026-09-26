<?php
/**
 * Created By Rativardhan Singh Sengar  4/18/19 7:59 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/20/19 12:54 AM
 */

namespace App\Http\Controllers;


use App\Http\Validations\UserFavoritesValidations;
use App\Models\UserFavoritesModel;
use App\Services\UserFavoritesService;
use App\Services\UserService;
use Validator;
Use Log;
use App\Helpers\CommonHelper;
use Illuminate\Http\Request;

class UserFavoritesController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    private $userFavoritesService;
    private $userService;


    private function likeWhere($query, $field, $value)
    {
        if (empty($value))
            return $query;
        return $query->where($field, 'LIKE', "%$value%");
    }

    private function where($query, $field, $value, $condition = "=")
    {
        if (empty($value))
            return $query;
        return $query->where($field, $condition, $value);
    }

    private function whereBetween($query, $field, $from, $to)
    {
        if (!empty($from))
            $query->where($field, '>=', $from);

        if (!empty($to))
            $query->where($field, '<=', $to);

        return $query;
    }


    /**
     * UserFavoritesController constructor.
     * @param Request $request
     * @param UserFavoritesService $userFavoritesService
     * @param UserService $userService
     */
    public function __construct(Request $request
        , UserFavoritesService $userFavoritesService
        , UserService $userService
    )
    {
        Log::info("UserFavoritesController: __construct called");
        $this->request                        = $request;
        $this->userFavoritesService             = $userFavoritesService;
        $this->userService = $userService;
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        Log::info("UserFavoritesController: indexAll called");
        $limit              = $this->request->get('limit') ? $this->request->get('limit') : 10;
        if($limit > 50) $limit = 50;
        $offset             = $this->request->get('offset') ? $this->request->get('offset') : 0;
        $address            = $this->request->get('address');
        $city               = $this->request->get('city');
        $county             = $this->request->get('county');
        $state              = $this->request->get('state');
        $zip                = $this->request->get('zip');

        $sale_date_from     = $this->request->get('sale_date_from');
        $sale_date_to       = $this->request->get('sale_date_to');


        $info = UserFavoritesModel::select([
            "home_information.*"
            , "property_descriptions.*"
            , "local_real_estate.*"
            , "school_neighborhood.*"
            , "home_information.house_id" ## Needed this field, Important this line
        ])
        ->leftJoin('home_information', 'home_information.house_id', '=', 'user_favorites.house_id')
        ->leftJoin('local_real_estate', 'home_information.house_id', '=', 'local_real_estate.house_id')
        ->leftJoin('school_neighborhood', 'home_information.house_id', '=', 'school_neighborhood.house_id')
        ->leftJoin('property_descriptions', 'home_information.house_id', '=', 'property_descriptions.house_id')
        ;

        $info = $this->where($info, 'user_favorites.user_id', $this->userService->user_id());
        $info = $this->likeWhere($info, 'address', $address);
        $info = $this->likeWhere($info, 'city', $city);
        $info = $this->likeWhere($info, 'county', $county);
        $info = $this->where($info, 'state', $state);
        $info = $this->where($info, 'zip', $zip);
        $info = $info->whereNull('home_information.deleted_at');
        $isManyJoin = false;

        if (!empty($sale_date_from) || !empty($sale_date_to))
        {
            $info->leftJoin('sale_details', 'home_information.house_id', '=', 'sale_details.house_id');
            $info = $this->whereBetween($info, 'sale_details.sale_date', $sale_date_from, $sale_date_to);
            $isManyJoin = true;
        }


        if ($isManyJoin === false)
            $total = $info->count();
        else
        {
            $info->groupBy('home_information.house_id');
            $total = $info->get()->count();
        }

        $info->with(['sale_details']);
        $info = $info->skip(intval($offset))->take(intval($limit))->get();

        if (empty($info))
        {
            return response()->json(['status' => 'success','total' => $total, 'data' => [], 'message' => __("error_messages.record_not_exists")], 200);
        }

        return response()->json(['status' => 'success','total' => $total, 'data' => $info], 200);
    }


    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreate($house_id)
    {
        Log::info("UserFavoritesController: updateOrCreate called");

        ## check input validation
        Log::info("UserFavoritesController: create validation check");
        $rules = UserFavoritesValidations::updateOrCreate();
        $all = [];
        $all['house_id'] = $house_id;
        $validator = Validator::make($all, $rules);
        $validator->validate();

        # create information
        $where = [];
        $where['user_id'] = $this->userService->user_id();
        $where['house_id'] = $all['house_id'];
        $info = $this->userFavoritesService->updateOrCreate($where, $all);

        return response()->json(['status' => 'success','data' => $info, 'message' => __("messages.record_saved")], 200);
    }

    /**
     * @param $house_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($house_id)
    {
        Log::info("UserFavoritesController: delete called");

        try
        {
            $info = UserFavoritesModel::where(['house_id'=>$house_id,"user_id"=>$this->userService->user_id()]);
            if ($info->count() > 0)
            {
                $info->delete();
                return response()->json(['status' => 'success','message' => __("messages.favourite_delete")
                                         , 'data'  => []], 200);
            }
            else
            {
                return response()->json(['status' => 'failed','message' => __("messages.favourite_not_exists"), 'data' => []], 200);
            }
        }
        catch (Exception $ex)
        {
            return response()->json(['status' => 'failed','message' => __("error_messages.something_wrong")], 400);
        }
    }
}