<?php

namespace App\Http\Controllers\API\UserData;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        $field = $request->get('field');

        if (!array_key_exists($field, $this->fields)) {
            return redirect()->route('userData');
        }

        $rules = [];
        
        switch ($field) {
            case 'current_weight':
                $rules['value'] = 'required|numeric|min:30|max:300';
                break;
            case 'height':
                $rules['value'] = 'required|integer|min:100|max:220';
                break;
            case 'age':
                $rules['value'] = 'required|integer|min:18|max:100';
                break;
            case 'gender':
                $rules['value'] = 'required|in:male,female';
                break;
        }
        $validated = $request->validate($rules);

        $user->update([$field => $validated['value']]);

        return response()->json([
            'success' => true,
            'message' => 'Данные обновлены',
            '_csrf_token' => csrf_token()
        ]);

    }
}
