<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\Mongo;
use App\Http\Resources\VehicleResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;

/**
 * @subgroup Vehicle
 */
class VehicleController extends Controller
{
    use ApiResponse;

    /**
     * List
     * @authenticated
     */
    public function list()
    {
        try {
            $vehicleCollection = Mongo::collection('vehicles');

            $vehicles = $vehicleCollection->find();
            $vehicles = collect($vehicles)->map(fn($vehicle) => (object)$vehicle);

            return $vehicles->count() ? $this->success('Vehicles list found', VehicleResource::collection($vehicles)) : $this->error('Vehicle list is empty');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Detail
     * @authenticated
     */
    public function detail($_id)
    {
        try {
            $vehicleCollection = Mongo::collection('vehicles');
            $_id = new ObjectId($_id);

            $vehicle = $vehicleCollection->findOne(['_id' => $_id]);
            $vehicle = $vehicle ? (object)$vehicle : null;

            return $vehicle ? $this->success('Vehicle detail found', new VehicleResource($vehicle)) : $this->error('Vehicle detail not found');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Create
     * @authenticated
     *
     * @bodyParam type string required Type of vehicle. Example: 2wheeler,3wheeler,4wheeler,heavyduty
     * @bodyParam brand string required Brand of vehicle. Example: Toyota
     * @bodyParam model string required Model of vehicle. Example: Supra
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'type' => 'required|in:2wheeler,3wheeler,4wheeler,heavyduty',
                    'brand' => 'required|min:1|max:20',
                    'model' => 'required|min:1|max:20'
                ]
            );

            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }

            Mongo::beginTransaction();

            $vehicleCollection = Mongo::collection('vehicles');
            $vehicleCollection->insertOne([
                'type' => $request->type,
                'brand' => $request->brand,
                'model' => $request->model
            ], ['session' => Mongo::session()]);

            Mongo::commit();

            return $this->success('Vehicle data inserted successfully');
        } catch (Exception $e) {
            Mongo::rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update
     * @authenticated
     *
     * @bodyParam _id string required Id of the data set. Example: 67ade7996c0045e49503800e
     * @bodyParam type string required Type of vehicle. Example: 2wheeler,3wheeler,4wheeler,heavyduty
     * @bodyParam brand string required Brand of vehicle. Example: Toyota
     * @bodyParam model string required Model of vehicle. Example: Supra
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    '_id' => 'required|exists:vehicles,_id',
                    'type' => 'required|in:2wheeler,3wheeler,4wheeler,heavyduty',
                    'brand' => 'required|min:1|max:20',
                    'model' => 'required|min:1|max:20'
                ]
            );

            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }

            Mongo::beginTransaction();

            $vehicleCollection = Mongo::collection('vehicles');
            $vehicleCollection->updateOne(
                [
                    '_id' => new ObjectId($request->_id)
                ],
                [
                    '$set' => [
                        'type' => $request->type,
                        'brand' => $request->brand,
                        'model' => $request->model
                    ]
                ],
                ['session' => Mongo::session()]
            );

            Mongo::commit();

            return $this->success('Vehicle data updated successfully');
        } catch (Exception $e) {
            Mongo::rollback();
            return $this->error($e->getMessage());
        }
    }
}
