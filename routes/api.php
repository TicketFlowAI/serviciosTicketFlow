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

Route::middleware(['auth:sanctum'])->group(function () {
    // Company routes
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::get('/companies/{company}', [CompanyController::class, 'show']);
    Route::put('/companies/{company}', [CompanyController::class, 'update']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
    Route::get('/companies/deleted', [CompanyController::class, 'getDeleted']);
    Route::put('/companies/{id}/restore', [CompanyController::class, 'restore']);
    Route::get('/companies/{id}/users', [CompanyController::class, 'getUsersByCompanyId']);

    // Service routes
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
    Route::get('/services/deleted', [ServiceController::class, 'getDeleted']);
    Route::put('/services/{id}/restore', [ServiceController::class, 'restore']);

    // Tax routes
    Route::get('/taxes', [TaxController::class, 'index']);
    Route::post('/taxes', [TaxController::class, 'store']);
    Route::get('/taxes/{tax}', [TaxController::class, 'show']);
    Route::put('/taxes/{tax}', [TaxController::class, 'update']);
    Route::delete('/taxes/{tax}', [TaxController::class, 'destroy']);
    Route::get('/taxes/deleted', [TaxController::class, 'getDeleted']);
    Route::put('/taxes/{id}/restore', [TaxController::class, 'restore']);

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('/categories/deleted', [CategoryController::class, 'getDeleted']);
    Route::put('/categories/{id}/restore', [CategoryController::class, 'restore']);

    // Service Term routes
    Route::get('/serviceTerms', [ServiceTermController::class, 'index']);
    Route::post('/serviceTerms', [ServiceTermController::class, 'store']);
    Route::get('/serviceTerms/{serviceTerm}', [ServiceTermController::class, 'show']);
    Route::put('/serviceTerms/{serviceTerm}', [ServiceTermController::class, 'update']);
    Route::delete('/serviceTerms/{serviceTerm}', [ServiceTermController::class, 'destroy']);
    Route::get('/serviceTerms/deleted', [ServiceTermController::class, 'getDeleted']);
    Route::put('/serviceTerms/{id}/restore', [ServiceTermController::class, 'restore']);

    // Service Contract routes
    Route::get('/servicecontracts', [ServiceContractController::class, 'index']);
    Route::post('/servicecontracts', [ServiceContractController::class, 'store']);
    Route::get('/servicecontracts/{servicecontract}', [ServiceContractController::class, 'show']);
    Route::put('/servicecontracts/{servicecontract}', [ServiceContractController::class, 'update']);
    Route::delete('/servicecontracts/{servicecontract}', [ServiceContractController::class, 'destroy']);
    Route::get('/servicecontracts/bycompany/{id}', [ServiceContractController::class, 'getContractsByCompany']);
    Route::get('/servicecontracts/expiring', [ServiceContractController::class, 'getExpiringContracts']);
    Route::get('/servicecontracts/deleted', [ServiceContractController::class, 'getDeleted']);
    Route::put('/servicecontracts/{id}/restore', [ServiceContractController::class, 'restore']);

    // Ticket routes
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
    Route::get('/tickets/deleted', [TicketController::class, 'getDeleted']);
    Route::put('/tickets/{id}/restore', [TicketController::class, 'restore']);
    Route::post('/tickets/close/{id}', [TicketController::class, 'closeTicket']);
    Route::post('/tickets/reassign/{id}', [TicketController::class, 'assignTicket']);
    Route::post('/tickets/open/{id}', [TicketController::class, 'openTicket']);
    Route::post('/tickets/{id}/needs-human-interaction', [TicketController::class, 'markNeedsHumanInteractionAndAssign']);
    Route::get('/tickets/history/{id}', [TicketController::class, 'retrieveTicketHistory']);

    // Message routes
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{message}', [MessageController::class, 'show']);
    Route::put('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // User routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/user', [UserController::class, 'getAuthenticatedUser']);
    Route::get('/users/deleted', [UserController::class, 'getDeleted']);
    Route::put('/users/{id}/restore', [UserController::class, 'restore']);
    Route::get('/users/byrole/{role}', [UserController::class, 'getUsersByRole']);

    // Email routes
    Route::get('/emails', [EmailController::class, 'index']);
    Route::post('/emails', [EmailController::class, 'store']);
    Route::get('/emails/{email}', [EmailController::class, 'show']);
    Route::put('/emails/{email}', [EmailController::class, 'update']);
    Route::delete('/emails/{email}', [EmailController::class, 'destroy']);
    Route::get('/emails/deleted', [EmailController::class, 'getDeleted']);
    Route::put('/emails/{id}/restore', [EmailController::class, 'restore']);

    // Interval routes
    Route::get('/intervals', [IntervalController::class, 'index']);
    Route::post('/intervals', [IntervalController::class, 'store']);
    Route::get('/intervals/{interval}', [IntervalController::class, 'show']);
    Route::put('/intervals/{interval}', [IntervalController::class, 'update']);
    Route::delete('/intervals/{interval}', [IntervalController::class, 'destroy']);
    Route::get('/intervals/deleted', [IntervalController::class, 'getDeleted']);
    Route::put('/intervals/{id}/restore', [IntervalController::class, 'restore']);

    // Role routes
    Route::get('/roles', [RolesController::class, 'index']);
    Route::post('/roles', [RolesController::class, 'store']);
    Route::get('/roles/{role}', [RolesController::class, 'show']);
    Route::put('/roles/{role}', [RolesController::class, 'update']);
    Route::delete('/roles/{role}', [RolesController::class, 'destroy']);
    Route::get('/permissions', [RolesController::class, 'listPermissions']);

    // Report routes
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
});