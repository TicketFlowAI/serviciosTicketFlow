<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\SurveyQuestion;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="API Endpoints for generating reports"
 * )
 */
class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/reports/tickets-solved",
     *     summary="Get the number of tickets solved",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets solved",
     *         @OA\JsonContent(type="integer", example=150)
     *     )
     * )
     */
    public function getTicketsSolved(Request $request)
    {
        $query = Ticket::whereIn('status', [0, 3]);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $count = $query->count();
        return ApiResponseClass::sendResponse($count, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/average-time-to-close",
     *     summary="Get the average time to close tickets",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Average time to close tickets in hours",
     *         @OA\JsonContent(type="number", format="float", example=24.5)
     *     )
     * )
     */
    public function getAverageTimeToClose(Request $request)
    {
        $query = Ticket::whereIn('status', [0, 3]);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $averageTime = $query
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time'))
            ->first()
            ->avg_time;

        return ApiResponseClass::sendResponse($averageTime, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/tickets-escalations",
     *     summary="Get the number of technicians who worked on a ticket to get it solved",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Number of technicians per ticket",
     *         @OA\JsonContent(type="array", @OA\Items(type="object", @OA\Property(property="ticket_id", type="integer"), @OA\Property(property="technicians_count", type="integer")))
     *     )
     * )
     */
    public function getTicketsEscalations(Request $request)
    {
        $query = TicketHistory::select('ticket_id', DB::raw('COUNT(DISTINCT user_id) as technicians_count'))
            ->groupBy('ticket_id');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $escalations = $query->get();
        return ApiResponseClass::sendResponse($escalations, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/tickets-per-complexity",
     *     summary="Get the number of tickets per complexity level",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets per complexity level",
     *         @OA\JsonContent(type="array", @OA\Items(type="object", @OA\Property(property="complexity", type="integer"), @OA\Property(property="count", type="integer")))
     *     )
     * )
     */
    public function getTicketsPerComplexity(Request $request)
    {
        $query = Ticket::select('complexity', DB::raw('COUNT(*) as count'))
            ->groupBy('complexity');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $complexityLevels = $query->get()->map(function ($item) {
            if (is_null($item->complexity)) {
                $item->complexity = 'Asignando parámetro';
            }
            return $item;
        });

        return ApiResponseClass::sendResponse($complexityLevels, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/tickets-human-interaction",
     *     summary="Get the number of tickets that needed human interaction",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets that needed human interaction",
     *         @OA\JsonContent(type="integer", example=120)
     *     )
     * )
     */
    public function getTicketsHumanInteraction(Request $request)
    {
        $query = Ticket::whereHas('history', function ($query) {
            $query->where('action', '!=', 'created');
        });

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $count = $query->count();
        if (is_null($count)) {
            $count = 'Asignando parámetro';
        }

        return ApiResponseClass::sendResponse($count, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/{user_id}/tickets-solved",
     *     summary="Get the number of tickets solved by a technician",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the technician",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         description="Start date for filtering",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         description="End date for filtering",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets solved by the technician",
     *         @OA\JsonContent(type="integer", example=50)
     *     )
     * )
     */
    public function getTechnicianTicketsSolved(Request $request, $user_id)
    {
        $query = Ticket::whereIn('status', [0, 3])->where('user_id', $user_id);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $count = $query->count();
        return ApiResponseClass::sendResponse($count, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/{user_id}/average-time-to-solve",
     *     summary="Get the average time to solve a ticket by a technician",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the technician",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         description="Start date for filtering",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         description="End date for filtering",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Average time to solve a ticket by the technician in hours",
     *         @OA\JsonContent(type="number", format="float", example=12.5)
     *     )
     * )
     */
    public function getTechnicianAverageTimeToSolve(Request $request, $user_id)
    {
        $query = Ticket::whereIn('status', [0, 3])->where('user_id', $user_id);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }

        $averageTime = $query
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_time'))
            ->first()
            ->avg_time;

        return ApiResponseClass::sendResponse($averageTime, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/{user_id}/tickets-assigned-reassigned",
     *     summary="Get the number of tickets assigned and reassigned to a technician",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the technician",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         description="Start date for filtering",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         description="End date for filtering",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets assigned and reassigned to the technician",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="assigned", type="integer", example=30),
     *             @OA\Property(property="reassigned", type="integer", example=5)
     *         )
     *     )
     * )
     */
    public function getTechnicianTicketsAssignedAndReassigned(Request $request, $user_id)
    {
        $query = TicketHistory::select('ticket_id', DB::raw('COUNT(*) as actions_count'))
            ->where('user_id', $user_id)
            ->groupBy('ticket_id');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $actions = $query->get();
        $assigned = $actions->count();
        $reassigned = $actions->filter(fn($action) => $action->actions_count > 1)->count();

        return ApiResponseClass::sendResponse(['assigned' => $assigned, 'reassigned' => $reassigned], '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/{user_id}/current-tickets",
     *     summary="Get the number of tickets a technician is currently working on",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the technician",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets the technician is currently working on",
     *         @OA\JsonContent(type="integer", example=10)
     *     )
     * )
     */
    public function getTechnicianCurrentTickets($user_id)
    {
        $count = Ticket::where('status', 1)->where('user_id', $user_id)->count();
        return ApiResponseClass::sendResponse($count, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/{user_id}/weekly-comparison",
     *     summary="Get the number of tickets closed by a technician in the last 7 days vs the previous week",
     *     tags={"Reports"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the technician",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Number of tickets closed by the technician in the last 7 days vs the previous week",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_week", type="integer", example=15),
     *             @OA\Property(property="previous_week", type="integer", example=10)
     *         )
     *     )
     * )
     */
    public function getTechnicianWeeklyComparison($user_id)
    {
        $currentWeek = Ticket::where('status', 0)
            ->where('user_id', $user_id)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $previousWeek = Ticket::where('status', 0)
            ->where('user_id', $user_id)
            ->whereBetween('updated_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        return ApiResponseClass::sendResponse(['current_week' => $currentWeek, 'previous_week' => $previousWeek], '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/average-score-per-question",
     *     summary="Get the average score per question",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Average score per question",
     *         @OA\JsonContent(type="array", @OA\Items(type="object", @OA\Property(property="question_id", type="integer"), @OA\Property(property="question", type="string"), @OA\Property(property="average_score", type="number", format="float")))
     *     )
     * )
     */
    public function getAverageScorePerQuestion(Request $request)
    {
        $query = Survey::select('question_id', DB::raw('AVG(score) as average_score'))
            ->groupBy('question_id');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $averageScores = $query->get()->map(function ($item) {
            $item->question = SurveyQuestion::find($item->question_id)->question;
            return $item;
        });

        return ApiResponseClass::sendResponse($averageScores, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/average-score-per-question",
     *     summary="Get the average score per question for the logged-in technician",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Average score per question for the logged-in technician",
     *         @OA\JsonContent(type="array", @OA\Items(type="object", @OA\Property(property="question_id", type="integer"), @OA\Property(property="question", type="string"), @OA\Property(property="average_score", type="number", format="float")))
     *     )
     * )
     */
    public function getTechnicianAverageScorePerQuestion(Request $request)
    {
        $user_id = auth()->user()->id;

        $query = Survey::whereHas('ticket', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->select('question_id', DB::raw('AVG(score) as average_score'))
            ->groupBy('question_id');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $averageScores = $query->get()->map(function ($item) {
            $item->question = SurveyQuestion::find($item->question_id)->question;
            return $item;
        });

        return ApiResponseClass::sendResponse($averageScores, '', 200);
    }

    /**
     * @OA\Get(
     *     path="/reports/technician/{$id}/average-score",
     *     summary="Get the average score for the logged-in technician",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Average score for the logged-in technician",
     *         @OA\JsonContent(type="number", format="float", example=4.5)
     *     )
     * )
     */
    public function getTechnicianAverageScore(Request $request)
    {
        $user_id = auth()->user()->id;

        $query = Survey::whereHas('ticket', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $averageScore = $query->avg('score');
        return ApiResponseClass::sendResponse($averageScore, '', 200);
    }

    
}
