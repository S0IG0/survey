<?php


namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SurveyController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function createSurvey(Request $request): JsonResponse
    {
        $this->validate($request, [
            'question' => 'required|string|min:6',
            'description' => 'nullable|string|min:6',
            'is_anonymous' => 'nullable|boolean',
            'is_multiple' => 'nullable|boolean',
        ]);

        try {
            $survey = new Survey();
            $survey->owner_id = Auth::id();
            $survey->question = $request->input('question');
            $survey->description = $request->input('description');

            if ($request->input('is_anonymous')) {
                $survey->is_anonymous = true;
            } else {
                $survey->is_anonymous = false;
            }

            if ($request->input('is_multiple')) {
                $survey->is_multiple = true;
            } else {
                $survey->is_multiple = false;
            }
            $survey->is_activated = false;
            $survey->save();
            return response()->json($survey, 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Survey Create Failed!',
            ], 409);
        }
    }

    /**
     * @throws ValidationException
     */
    public function updateSurvey($id, Request $request): JsonResponse
    {

        try {
            $survey = Survey::with('answers')->findOrFail($id);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Survey Not Found!'], 404);
        }

        if ($survey->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Survey Not Authorized!'], 403);
        }

        if ($survey->is_activated) {
            return response()->json(['message' => 'The survey is activated, so it cannot be changed!'], 409);
        }


        $this->validate($request, [
            'question' => 'nullable|string|min:6',
            'description' => 'nullable|string|min:6',
            'is_anonymous' => 'nullable|boolean',
            'is_multiple' => 'nullable|boolean',
        ]);

        if ($request->input('question')) {
            $survey->question = $request->input('question');
        }
        if ($request->input('description')) {
            $survey->description = $request->input('description');
        }
        if ($request->has('is_anonymous')) {
            $survey->is_anonymous = $request->input('is_anonymous');
        }
        if ($request->has('is_multiple')) {
            $survey->is_multiple = $request->input('is_multiple');
        }
        $survey->save();
        return response()->json($survey, 200);
    }

    public function activateSurvey($id): JsonResponse
    {
        try {
            $survey = Survey::with('answers')->findOrFail($id);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Survey Not Found!'], 404);
        }

        if ($survey->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Survey Not Authorized!'], 403);
        }

        if ($survey->is_activated) {
            return response()->json(['message' => 'The survey has already been activated.'], 409);
        }

        $survey->is_activated = true;
        $survey->save();

        return response()->json([
            'survey' => $survey,
            'message' => 'The survey is activated, so it cannot be changed!'
        ], 200);
    }

    public function deleteSurvey($id): JsonResponse
    {
        try {
            $survey = Survey::findOrFail($id);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Survey Not Found!'], 404);
        }

        if ($survey->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Survey Not Authorized!'], 403);
        }

        $survey->delete();
        return response()->json(null, 204);
    }

    public function mySurvey(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $surveys = Survey::with('answers')->where('owner_id', Auth::id())->paginate($perPage);
        return response()->json($surveys, 200);
    }

    public function allSurvey(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $surveys = Survey::with('answers')->paginate($perPage);
        return response()->json($surveys, 200);
    }


    /**
     * @throws ValidationException
     */
    public function addAnswerToSurvey($id, Request $request): JsonResponse
    {
        try {
            $survey = Survey::with('answers')->findOrFail($id);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Survey Not Found!'], 404);
        }

        if ($survey->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Survey Not Authorized!'], 403);
        }

        $this->validate($request, [
            'answer' => 'required|string|min:6',
        ]);

        $answer = new Answer();
        $answer->answer = $request->input('answer');
        $answer->surveys_id = $survey->id;
        $survey->answers()->save($answer);
        $survey->load('answers');

        return response()->json($survey, 200);
    }
}
