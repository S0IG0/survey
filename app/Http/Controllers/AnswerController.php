<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AnswerController extends Controller
{
    public function deleteAnswer($id): JsonResponse
    {
        try {
            $answer = Answer::with('survey')->findOrFail($id);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Answer Not Found!',
            ], 404);
        }

        if ($answer->survey->owner_id != Auth::id()) {
            return response()->json(['message' => 'Answer Not Authorized!'], 403);
        }

        if ($answer->survey->is_activated) {
            return response()->json(['message' => 'The survey is activated, so it cannot be changed answer!'], 409);
        }

        $answer->delete();
        return response()->json(null, 204);
    }


    /**
     * @throws ValidationException
     */
    public function updateAnswer($id, Request $request): JsonResponse
    {
        try {
            $answer = Answer::with('survey')->findOrFail($id);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Answer Not Found!',
            ], 404);
        }

        if ($answer->survey->owner_id != Auth::id()) {
            return response()->json(['message' => 'Answer Not Authorized!'], 403);
        }

        if ($answer->survey->is_activated) {
            return response()->json(['message' => 'The survey is activated, so it cannot be changed answer!'], 409);
        }

        $this->validate($request, [
            'answer' => 'nullable|string|min:6',
        ]);


        if ($request->input('answer')) {
            $answer->answer = $request->input('answer');
            $answer->save();
        }

        return response()->json([
            'id' => $answer->id,
            'answer' => $answer->answer,
            'created_at' => $answer->created_at,
            'updated_at' => $answer->updated_at,
        ], 200);
    }

    public function chooseAnswer($id): JsonResponse
    {
        try {
            $answer = Answer::with('survey.answers')->findOrFail($id);
            $survey = $answer->survey;
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Answer Not Found!',
            ], 404);
        }

        if (!$survey->is_activated) {
            return response()->json(['message' => 'Survey is not active, wait for it to be activated'], 409);
        }

        $user = User::find(Auth::id());
        $answerExists = $user->answers()->where('answer_id', $id)->exists();

        if ($answerExists) {
            return response()->json(['message' => 'Answer already selected!'], 409);
        }

        if (!$survey->is_multiple) {
            $answers = $survey->answers;
            foreach ($answers as $answerInSurvey) {
                if ($user->answers()->where('answer_id', $answerInSurvey->id)->exists()) {
                    return response()->json(['message' => 'You can choose only one answer option!'], 409);
                }
            }
        }

        $user->answers()->attach($id);

        return response()->json([
            'id' => $answer->id,
            'answer' => $answer->answer,
        ], 200);
    }

}
