<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class ApiQuestionController extends Controller
{
    /**
     * Fetch all questions.
     */
    public function index()
    {
        // Retrieve all questions from the database
        $questions = Question::with(['questionData', 'conditions', 'options', 'nextQuestion'])->get();

        // Return the questions as a JSON response
        return response()->json($questions);
    }


    
    public function show($id)
    {
        // Fetch the question with all related data
        $question = Question::with([
            'questionData',
            'conditions',
            'options'
        ])->find($id);

        // Check if the question exists
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        // Return the question data with relationships
        return response()->json($question);
    }
}
