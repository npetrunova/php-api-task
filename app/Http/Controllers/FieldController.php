<?php

namespace App\Http\Controllers;

use App\Field;
use App\SubscriberField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\NotFoundHttpException;
use App\Http\Requests\FieldRequest;
use App\Http\Resources\Field as FieldResource;
use \Illuminate\Support\Facades\Lang;

class FieldController extends Controller
{
    /**
     * Retrieves all fields from the database
     * @return Response
     */
    public function retrieveFields()
    {
        return FieldResource::collection(Field::all());
    }

    /**
     * Retreieve a field given an id
     * @param int $id
     * @return Response
     */
    public function retrieveField($id)
    {
        $field = new FieldResource(Field::find($id));
        if ($field !== null) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        return $field;
    }

    /**
     * Creates a new field. Validation is performed to check
     * if the given types is from the accpeted types before trying
     * to save to the database
     * @param FieldRequest $request
     * @return Response
     */
    public function createField(FieldRequest $request)
    {
        $input = $request->input();
        $field = Field::create(['title' => $input['title'], 'type' => $input['type']]);

        return new FieldResource($field);
    }

    /**
     * Deletes a field given an id. Performs a check if there are any
     * subscriber fields of this field and if there are, the field is not deleted
     * @param int $id
     * @return Response
     */
    public function deleteField($id)
    {
        $field = Field::find($id);
        if (!$field) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }
        $subscriberFields = SubscriberField::where('field_id', $id)->exists();
        if ($subscriberFields) {
            return response()->json(['errors' => ['id' =>trans('custom.field_in_use')]], 422);
        }

        $field->delete();

        return response()->json(['data' =>['msg' => trans('custom.field_deleted_successfully')]], 200);
    }
}
