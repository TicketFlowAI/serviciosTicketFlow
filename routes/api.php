<?php

use App\Http\Controllers\{
    CompanyController,
    ServiceController,
    TaxController,
    CategoryController,
    ServiceTermController,
    ServiceContractController,
    TicketController,
    MessageController,
    UserController,
    EmailController,
    IntervalController,
    RolesController,
    ReportController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request, UserController $userController) {
//     $user = $request->user(); // Get the authenticated user
//     return response()->json($userController->getAuthenticatedUser($user));
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Protected with sanctum API routes
    Route::apiResource('/companies', CompanyController::class);
    Route::get('/companies/deleted', [CompanyController::class, 'getDeleted']);
    Route::put('/companies/{id}/restore', [CompanyController::class, 'restore']);
    Route::apiResource('/services', ServiceController::class);
    Route::get('/services/deleted', [ServiceController::class, 'getDeleted']);
    Route::put('/services/{id}/restore', [ServiceController::class, 'restore']);
    Route::apiResource('/taxes', TaxController::class);
    Route::get('/taxes/deleted', [TaxController::class, 'getDeleted']);
    Route::put('/taxes/{id}/restore', [TaxController::class, 'restore']);
    Route::apiResource('/categories', CategoryController::class);
    Route::get('/categories/deleted', [CategoryController::class, 'getDeleted']);
    Route::put('/categories/{id}/restore', [CategoryController::class, 'restore']);
    Route::apiResource('/serviceTerms', ServiceTermController::class);
    Route::get('/serviceTerms/deleted', [ServiceTermController::class, 'getDeleted']);
    Route::put('/serviceTerms/{id}/restore', [ServiceTermController::class, 'restore']);
    Route::apiResource('/servicecontracts', ServiceContractController::class);
    Route::get('/servicecontracts/bycompany/{id}', [ServiceContractController::class, 'getContractsByCompany']);
    Route::get('/servicecontracts/expiring', [ServiceContractController::class, 'getExpiringContracts']);
    Route::get('/servicecontracts/deleted', [ServiceContractController::class, 'getDeleted']);
    Route::put('/servicecontracts/{id}/restore', [ServiceContractController::class, 'restore']);
    Route::apiResource('/tickets', TicketController::class);
    Route::get('/tickets/deleted', [TicketController::class, 'getDeleted']);
    Route::put('/tickets/{id}/restore', [TicketController::class, 'restore']);
    Route::apiResource('/messages', MessageController::class);
    Route::apiResource('/users', UserController::class);
    Route::get('/user', [UserController::class, 'getAuthenticatedUser']);
    Route::get('/users/deleted', [UserController::class, 'getDeleted']);
    Route::put('/users/{id}/restore', [UserController::class, 'restore']);
    Route::apiResource('/emails', EmailController::class);
    Route::get('/emails/deleted', [EmailController::class, 'getDeleted']);
    Route::put('/emails/{id}/restore', [EmailController::class, 'restore']);
    Route::apiResource('/intervals', IntervalController::class);
    Route::get('/intervals/deleted', [IntervalController::class, 'getDeleted']);
    Route::put('/intervals/{id}/restore', [IntervalController::class, 'restore']);
    Route::post('/tickets/close/{id}', [TicketController::class, 'closeTicket']);
    Route::post('/tickets/reassign/{id}', [TicketController::class, 'assignTicket']);
    Route::post('/tickets/open/{id}', [TicketController::class, 'openTicket']);
    Route::get('/users/byrole/{role}', [UserController::class, 'getUsersByRole']);
    Route::apiResource('/roles', RolesController::class);
    Route::get('/permissions', [RolesController::class, 'listPermissions']);
    Route::get('/tickets/history/{id}', [TicketController::class, 'retrieveTicketHistory']);
    Route::get('/reports/tickets-solved', [ReportController::class, 'getTicketsSolved']);
    Route::get('/reports/average-time-to-close', [ReportController::class, 'getAverageTimeToClose']);
    Route::get('/reports/tickets-escalations', [ReportController::class, 'getTicketsEscalations']);
    Route::get('/reports/tickets-per-complexity', [ReportController::class, 'getTicketsPerComplexity']);
    Route::get('/reports/tickets-human-interaction', [ReportController::class, 'getTicketsHumanInteraction']);
    Route::get('/reports/technician/{user_id}/tickets-solved', [ReportController::class, 'getTechnicianTicketsSolved']);
    Route::get('/reports/technician/{user_id}/average-time-to-solve', [ReportController::class, 'getTechnicianAverageTimeToSolve']);
    Route::get('/reports/technician/{user_id}/tickets-assigned-reassigned', [ReportController::class, 'getTechnicianTicketsAssignedAndReassigned']);
    Route::get('/reports/technician/{user_id}/current-tickets', [ReportController::class, 'getTechnicianCurrentTickets']);
    Route::get('/reports/technician/{user_id}/weekly-comparison', [ReportController::class, 'getTechnicianWeeklyComparison']);
    Route::get('/companies/{id}/users', [CompanyController::class, 'getUsersByCompanyId']);
});