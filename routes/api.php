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
    ReportController,
    SurveyController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Company routes
    Route::get('/companies', [CompanyController::class, 'index'])->middleware('permission:view-companies');
    Route::post('/companies', [CompanyController::class, 'store'])->middleware('permission:create-companies');
    Route::get('/companies/{company}', [CompanyController::class, 'show'])->middleware('permission:view-companies');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->middleware('permission:edit-companies');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->middleware('permission:delete-companies');
    Route::get('/companies/deleted', [CompanyController::class, 'getDeleted'])->middleware('permission:view-deleted-companies');
    Route::put('/companies/{id}/restore', [CompanyController::class, 'restore'])->middleware('permission:restore-companies');
    Route::get('/companies/{id}/users', [CompanyController::class, 'getUsersByCompanyId'])->middleware('permission:view-company-users');

    // Service routes
    Route::get('/services', [ServiceController::class, 'index'])->middleware('permission:view-services');
    Route::post('/services', [ServiceController::class, 'store'])->middleware('permission:create-services');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->middleware('permission:view-services');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->middleware('permission:edit-services');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->middleware('permission:delete-services');
    Route::get('/services/deleted', [ServiceController::class, 'getDeleted'])->middleware('permission:view-deleted-services');
    Route::put('/services/{id}/restore', [ServiceController::class, 'restore'])->middleware('permission:restore-services');

    // Tax routes
    Route::get('/taxes', [TaxController::class, 'index'])->middleware('permission:view-taxes');
    Route::post('/taxes', [TaxController::class, 'store'])->middleware('permission:create-taxes');
    Route::get('/taxes/{tax}', [TaxController::class, 'show'])->middleware('permission:view-taxes');
    Route::put('/taxes/{tax}', [TaxController::class, 'update'])->middleware('permission:edit-taxes');
    Route::delete('/taxes/{tax}', [TaxController::class, 'destroy'])->middleware('permission:delete-taxes');
    Route::get('/taxes/deleted', [TaxController::class, 'getDeleted'])->middleware('permission:view-deleted-taxes');
    Route::put('/taxes/{id}/restore', [TaxController::class, 'restore'])->middleware('permission:restore-taxes');

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:view-categories');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:create-categories');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->middleware('permission:view-categories');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:edit-categories');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-categories');
    Route::get('/categories/deleted', [CategoryController::class, 'getDeleted'])->middleware('permission:view-deleted-categories');
    Route::put('/categories/{id}/restore', [CategoryController::class, 'restore'])->middleware('permission:restore-categories');

    // Service Term routes
    Route::get('/serviceTerms', [ServiceTermController::class, 'index'])->middleware('permission:view-service-terms');
    Route::post('/serviceTerms', [ServiceTermController::class, 'store'])->middleware('permission:create-service-terms');
    Route::get('/serviceTerms/{serviceTerm}', [ServiceTermController::class, 'show'])->middleware('permission:view-service-terms');
    Route::put('/serviceTerms/{serviceTerm}', [ServiceTermController::class, 'update'])->middleware('permission:edit-service-terms');
    Route::delete('/serviceTerms/{serviceTerm}', [ServiceTermController::class, 'destroy'])->middleware('permission:delete-service-terms');
    Route::get('/serviceTerms/deleted', [ServiceTermController::class, 'getDeleted'])->middleware('permission:view-deleted-service-terms');
    Route::put('/serviceTerms/{id}/restore', [ServiceTermController::class, 'restore'])->middleware('permission:restore-service-terms');

    // Service Contract routes
    Route::get('/servicecontracts', [ServiceContractController::class, 'index'])->middleware('permission:view-service-contracts');
    Route::post('/servicecontracts', [ServiceContractController::class, 'store'])->middleware('permission:create-service-contracts');
    Route::get('/servicecontracts/{servicecontract}', [ServiceContractController::class, 'show'])->middleware('permission:view-service-contracts');
    Route::put('/servicecontracts/{servicecontract}', [ServiceContractController::class, 'update'])->middleware('permission:edit-service-contracts');
    Route::delete('/servicecontracts/{servicecontract}', [ServiceContractController::class, 'destroy'])->middleware('permission:delete-service-contracts');
    Route::get('/servicecontracts/bycompany/{id}', [ServiceContractController::class, 'getContractsByCompany'])->middleware('permission:view-service-contracts');
    Route::get('/servicecontracts/expiring', [ServiceContractController::class, 'getExpiringContracts'])->middleware('permission:view-service-contracts');
    Route::get('/servicecontracts/deleted', [ServiceContractController::class, 'getDeleted'])->middleware('permission:view-deleted-service-contracts');
    Route::put('/servicecontracts/{id}/restore', [ServiceContractController::class, 'restore'])->middleware('permission:restore-service-contracts');

    // Ticket routes
    Route::get('/tickets', [TicketController::class, 'index'])->middleware('permission:view-tickets');
    Route::post('/tickets', [TicketController::class, 'store'])->middleware('permission:create-tickets');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->middleware('permission:view-tickets');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->middleware('permission:edit-tickets');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->middleware('permission:delete-tickets');
    Route::get('/tickets/deleted', [TicketController::class, 'getDeleted'])->middleware('permission:view-deleted-tickets');
    Route::put('/tickets/{id}/restore', [TicketController::class, 'restore'])->middleware('permission:restore-tickets');
    Route::post('/tickets/close/{id}', [TicketController::class, 'closeTicket'])->middleware('permission:close-tickets');
    Route::post('/tickets/reassign/{id}', [TicketController::class, 'assignTicket'])->middleware('permission:reassign-tickets');
    Route::post('/tickets/open/{id}', [TicketController::class, 'openTicket'])->middleware('permission:open-tickets');
    Route::post('/tickets/{id}/needs-human-interaction', [TicketController::class, 'markNeedsHumanInteractionAndAssign'])->middleware('permission:mark-needs-human-interaction');
    Route::get('/tickets/history/{id}', [TicketController::class, 'retrieveTicketHistory'])->middleware('permission:view-ticket-history');

    // Message routes
    Route::get('/messages', [MessageController::class, 'index'])->middleware('permission:view-messages');
    Route::post('/messages', [MessageController::class, 'store'])->middleware('permission:create-messages');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->middleware('permission:view-messages');
    Route::put('/messages/{message}', [MessageController::class, 'update'])->middleware('permission:edit-messages');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->middleware('permission:delete-messages');

    // User routes
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:view-users');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:create-users');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:view-users');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:edit-users');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-users');
    Route::get('/user', [UserController::class, 'getAuthenticatedUser'])->middleware('permission:view-authenticated-user');
    Route::get('/users/deleted', [UserController::class, 'getDeleted'])->middleware('permission:view-deleted-users');
    Route::put('/users/{id}/restore', [UserController::class, 'restore'])->middleware('permission:restore-users');
    Route::get('/users/byrole/{role}', [UserController::class, 'getUsersByRole'])->middleware('permission:view-users-by-role');

    // Email routes
    Route::get('/emails', [EmailController::class, 'index'])->middleware('permission:view-emails');
    Route::post('/emails', [EmailController::class, 'store'])->middleware('permission:create-emails');
    Route::get('/emails/{email}', [EmailController::class, 'show'])->middleware('permission:view-emails');
    Route::put('/emails/{email}', [EmailController::class, 'update'])->middleware('permission:edit-emails');
    Route::delete('/emails/{email}', [EmailController::class, 'destroy'])->middleware('permission:delete-emails');
    Route::get('/emails/deleted', [EmailController::class, 'getDeleted'])->middleware('permission:view-deleted-emails');
    Route::put('/emails/{id}/restore', [EmailController::class, 'restore'])->middleware('permission:restore-emails');

    // Interval routes
    Route::get('/intervals', [IntervalController::class, 'index'])->middleware('permission:view-intervals');
    Route::post('/intervals', [IntervalController::class, 'store'])->middleware('permission:create-intervals');
    Route::get('/intervals/{interval}', [IntervalController::class, 'show'])->middleware('permission:view-intervals');
    Route::put('/intervals/{interval}', [IntervalController::class, 'update'])->middleware('permission:edit-intervals');
    Route::delete('/intervals/{interval}', [IntervalController::class, 'destroy'])->middleware('permission:delete-intervals');
    Route::get('/intervals/deleted', [IntervalController::class, 'getDeleted'])->middleware('permission:view-deleted-intervals');
    Route::put('/intervals/{id}/restore', [IntervalController::class, 'restore'])->middleware('permission:restore-intervals');

    // Role routes
    Route::get('/roles', [RolesController::class, 'index'])->middleware('permission:view-roles');
    Route::post('/roles', [RolesController::class, 'store'])->middleware('permission:create-roles');
    Route::get('/roles/{role}', [RolesController::class, 'show'])->middleware('permission:view-roles');
    Route::put('/roles/{role}', [RolesController::class, 'update'])->middleware('permission:edit-roles');
    Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->middleware('permission:delete-roles');
    Route::get('/permissions', [RolesController::class, 'listPermissions'])->middleware('permission:view-permissions');

    // Report routes
    Route::get('/reports/tickets-solved', [ReportController::class, 'getTicketsSolved'])->middleware('permission:view-reports');
    Route::get('/reports/average-time-to-close', [ReportController::class, 'getAverageTimeToClose'])->middleware('permission:view-reports');
    Route::get('/reports/tickets-escalations', [ReportController::class, 'getTicketsEscalations'])->middleware('permission:view-reports');
    Route::get('/reports/tickets-per-complexity', [ReportController::class, 'getTicketsPerComplexity'])->middleware('permission:view-reports');
    Route::get('/reports/tickets-human-interaction', [ReportController::class, 'getTicketsHumanInteraction'])->middleware('permission:view-reports');
    Route::get('/reports/technician/{user_id}/tickets-solved', [ReportController::class, 'getTechnicianTicketsSolved'])->middleware('permission:view-technician-reports');
    Route::get('/reports/technician/{user_id}/average-time-to-solve', [ReportController::class, 'getTechnicianAverageTimeToSolve'])->middleware('permission:view-technician-reports');
    Route::get('/reports/technician/{user_id}/tickets-assigned-reassigned', [ReportController::class, 'getTechnicianTicketsAssignedAndReassigned'])->middleware('permission:view-technician-reports');
    Route::get('/reports/technician/{user_id}/current-tickets', [ReportController::class, 'getTechnicianCurrentTickets'])->middleware('permission:view-technician-reports');
    Route::get('/reports/technician/{user_id}/weekly-comparison', [ReportController::class, 'getTechnicianWeeklyComparison'])->middleware('permission:view-technician-reports');

    // Survey routes
    Route::get('/surveys', [SurveyController::class, 'index'])->middleware('permission:view-surveys');
    Route::post('/surveys', [SurveyController::class, 'store'])->middleware('permission:create-surveys');
    Route::get('/surveys/{id}', [SurveyController::class, 'show'])->middleware('permission:view-surveys');
    Route::put('/surveys/{id}', [SurveyController::class, 'update'])->middleware('permission:edit-surveys');
    Route::delete('/surveys/{id}', [SurveyController::class, 'destroy'])->middleware('permission:delete-surveys');
});