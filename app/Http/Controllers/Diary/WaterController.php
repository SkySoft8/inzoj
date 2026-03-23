<?php

namespace App\Http\Controllers\Diary;

use App\Models\DiaryNote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaterController extends Controller
{
    public function waterCounter(Request $request) {
        $type = $request->get('type');
        $note = DiaryNote::find(session('diary_note_id'));
        if ($type == "full") {
            $note->update(['current_water' => $note->current_water - 0.25]);
        } elseif ($type == "empty") {
            $note->update(['current_water' => $note->current_water + 0.25]);
        }

        return redirect()->route('diary');
    }
}
