<?php

namespace App\Http\Controllers;

use App\Field;
use App\SubscriberField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\NotFoundHttpException;

class FieldController extends Controller
{
    /**
     * Retrieves all fields from the database
     * @return Response
     */
    public function retrieveFields()
    {
        $fields = Field::select(['id', 'title', 'type'])->get();
        if (count($fields) > 0) {
            return response()->json(['data' => $fields], 200);
        }

        return response()->json([], 204);
    }

    /**
     * Retreieve a field given an id
     * @param int $id
     */
    public function retrieveField($id)
    {
        $field = Field::select(['id', 'title', 'type'])->where('id', $id)->first();
        if (!$field) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $field = $field->toArray();

        return response()->json(['data' => $field], 200);
    }

    /**
     * Creates a new field. Validation is performed to check
     * if the given types is from the accpeted types before trying
     * to save to the database
     * @param Request $request
     * @return Response
     */
    public function createField(Request $request)
    {
        $acceptedTypes = ['date', 'number', 'boolean', 'string'];
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required|in:'.implode(',', $acceptedTypes),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }
        $input = $request->input();
        $field = Field::create(['title' => $input['title'], 'type' => $input['type']]);
        unset($field['updated_at']);
        unset($field['created_at']);

        return response()->json(['data' =>['msg' => 'Field created successfully!', 'field' => $field]], 201);
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
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $subscriberFields = SubscriberField::where('field_id', $id)->exists();
        if ($subscriberFields) {
            return response()->json(['errors' => ['id' =>
                ['Cannot delete field as it is assigned to subscribers']]], 422);
        }

        $field->delete();

        return response()->json(['data' =>['msg' => 'Field deleted successfully!']], 200);
    }
}
