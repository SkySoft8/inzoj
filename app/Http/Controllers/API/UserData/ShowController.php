<?php

namespace App\Http\Controllers\API\UserData;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    private $fields = [
        'current_weight' => 'Текущий вес',
        'height' => 'Рост',
        'age' => 'Возраст',
        'gender' => 'Пол'
    ];

    public function show(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'show',
            'user' => $user,
            'fields' => $this->fields,
            'editing' => false,
            '_csrf_token' => csrf_token()

        ]);
    }
}
