<?php

namespace App\Http\Controllers\Diary;

use App\Models\DiaryNote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaterController extends Controller
{
    public function waterCounter(Request $request) {
        $type = $request->get('type');
        $diaryNoteId = $request->get('diary_note_id') ?? session('diary_note_id');
        $note = DiaryNote::find($diaryNoteId);
        
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Diary note not found'
            ], 400);
        }
        
        if ($type == "full") {
            $note->update(['current_water' => $note->current_water - 0.25]);
        } elseif ($type == "empty") {
            $note->update(['current_water' => $note->current_water + 0.25]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $type == 'empty' ? 'Water counter encreased successfully' : 'Water counter decreased successfully',
                'current_water' => $note->fresh()->current_water
            ]);
        }

        return redirect()->route('diary');
    }
}
