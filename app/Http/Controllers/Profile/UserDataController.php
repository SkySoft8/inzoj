<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDataController extends Controller
{
    private $fields = [
        'current_weight' => 'Текущий вес',
        'height' => 'Рост',
        'age' => 'Возраст',
        'gender' => 'Пол'
    ];

    public function show()
    {
        $user = Auth::user();
        return view('profile.userData', [
            'user' => $user,
            'fields' => $this->fields,
            'editing' => false
        ]);
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $field = $request->get('field');

        if (!array_key_exists($field, $this->fields)) {
            return redirect()->route('userData');
        }

        return view('profile.userData', [
            'user' => $user,
            'fields' => $this->fields,
            'editing' => true,
            'editingField' => $field
        ]);
    }

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

        return redirect()->route('userData');
    }
}