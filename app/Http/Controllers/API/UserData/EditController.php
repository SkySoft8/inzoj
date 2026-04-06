<?php

namespace App\Http\Controllers\API\UserData;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditController extends Controller
{
    private $fields = [
        'current_weight' => 'Текущий вес',
        'height' => 'Рост',
        'age' => 'Возраст',
        'gender' => 'Пол'
    ];

    public function edit(Request $request)
    {
        $user = Auth::user();
        $field = $request->get('field');

        return response()->json([
            'success' => true,
            'user' => $user,
            'fields' => $this->fields,
            'editing' => true,
            'editingField' => $field
        ]);
    }
}
