<?php

namespace App\Http\Controllers;

use App\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\NotFoundHttpException;

class FieldController extends Controller
{
    public function retrieveFields()
    {
        $fields = Field::select(['id', 'title', 'type'])->get();
        if (count($fields) > 0) {
            return response()->json(['data' => $fields], 200);
        }

        return response()->json([], 204);
    }

    public function retrieveField($id)
    {
        $field = Field::find($id);
        if (!$field) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $field = $field->toArray();
        unset($field['updated_at']);
        unset($field['created_at']);

        return response()->json(['data' => $field], 200);
    }

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

    public function deleteField($id)
    {
        $field = Field::find($id);
        if (!$field) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $subscriberFields = SubscriberField::where('field_id', $id)->exists();
        if ($subscriberFields) {
            return response()->json(['errors' => ['id' => ['Cannot delete field as it is assigned to subscribers']]], 422);
        }

        $field->delete();

        return response()->json(['data' =>['msg' => 'Field deleted successfully!']], 200);
    }
}
