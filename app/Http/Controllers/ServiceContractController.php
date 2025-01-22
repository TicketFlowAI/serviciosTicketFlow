<?php

namespace App\Http\Controllers;

use App\Models\ServiceContract;
use App\Http\Requests\StoreServiceContractRequest;
use App\Http\Requests\UpdateServiceContractRequest;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceContractResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Service; // Add this import
use App\Models\ServiceTerm; // Add this import
use App\Models\Email; // Add this import
use Carbon\Carbon; // Add this import
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // Add this import
use App\Mail\ServiceRequestMail; // Add this import
use App\Mail\ServiceCancellationMail; // Add this import

/**
 * @OA\Tag(
 *     name="Service Contracts",
 *     description="API Endpoints for managing service contracts"
 * )
 */
class ServiceContractController extends Controller
{
    private ServiceContractRepositoryInterface $serviceContractRepositoryInterface;

    public function __construct(ServiceContractRepositoryInterface $serviceContractRepositoryInterface)
    {
        $this->serviceContractRepositoryInterface = $serviceContractRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/service-contracts",
     *     summary="Get a list of service contracts",
     *     tags={"Service Contracts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of service contracts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="service_id", type="integer", example=10),
     *                 @OA\Property(property="service_term_id", type="integer", example=2),
     *                 @OA\Property(property="price", type="number", format="float", example=100.0),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="expiration_date", type="string", format="date-time", example="2023-07-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if ($user->hasRole('client')) {
                $data = $this->serviceContractRepositoryInterface->getContractsByCompany($user->company_id);
            } else {
                $data = $this->serviceContractRepositoryInterface->index();
            }

            $data->load('company:id,name', 'service:id,description,price', 'serviceterm:id,months,term');

            foreach ($data as $serviceContract) {
                $serviceContract->price = ($serviceContract->service->price / 12) * $serviceContract->serviceterm->months;
                $serviceContract->expiration_date = $serviceContract->created_at->addMonths($serviceContract->serviceTerm->months);
            }

            return ApiResponseClass::sendResponse(ServiceContractResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve service contracts', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/service-contracts",
     *     summary="Create a new service contract",
     *     tags={"Service Contracts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "service_id", "service_term_id"},
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=10),
     *             @OA\Property(property="service_term_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service contract created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceContractResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreServiceContractRequest $request)
    {
        $user = Auth::user();

        $service = Service::find($request->service_id);
        $serviceTerm = ServiceTerm::find($request->service_term_id);
        if ($service->category_id == 1 && $serviceTerm->months != 12) {
            return ApiResponseClass::sendResponse([], 'Domains must be billed annually', 400);
        }

        $details = [
            'company_id' => $user->hasRole('client') ? $user->company_id : $request->company_id,
            'service_id' => $request->service_id,
            'service_term_id' => $request->service_term_id,
        ];

        DB::beginTransaction();
        try {
            $serviceContract = $this->serviceContractRepositoryInterface->store($details);

            DB::commit();
            $serviceContract->expiration_date = "En proceso";
            return ApiResponseClass::sendResponse(new ServiceContractResource($serviceContract), 'ServiceContract Create Successful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create service contract', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/service-contracts/{id}",
     *     summary="Get details of a specific service contract",
     *     tags={"Service Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service contract",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service contract details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=10),
     *             @OA\Property(property="service_term_id", type="integer", example=2),
     *             @OA\Property(property="price", type="number", format="float", example=100.0),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *             @OA\Property(property="expiration_date", type="string", format="date-time", example="2023-07-01T00:00:00Z")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Service contract not found")
     * )
     */
    public function show($id)
    {
        try {
            $serviceContract = $this->serviceContractRepositoryInterface->getById($id);
            $serviceContract->load('company:id,name', 'service:id,description', 'serviceterm:id,months,term');
            $serviceContract->price = ($serviceContract->service->price / 12) * $serviceContract->serviceterm->months;
            $serviceContract->expiration_date = $serviceContract->created_at->addMonths($serviceContract->serviceterm->months);

            return ApiResponseClass::sendResponse(new ServiceContractResource($serviceContract), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve service contract', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/service-contracts/{id}",
     *     summary="Update a service contract",
     *     tags={"Service Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service contract",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "service_id", "service_term_id"},
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=10),
     *             @OA\Property(property="service_term_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Service contract updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateServiceContractRequest $request, $id)
    {
        $service = Service::find($request->service_id);
        $serviceTerm = ServiceTerm::find($request->service_term_id);
        if ($service->category_id == 1 && $serviceTerm->months != 12) {
            return ApiResponseClass::sendResponse([], 'Domains must be billed annually', 400);
        }

        $updateDetails = [
            'company_id' => $request->company_id,
            'service_id' => $request->service_id,
            'service_term_id' => $request->service_term_id,
        ];

        DB::beginTransaction();
        try {
            $this->serviceContractRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('ServiceContract Update Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update service contract', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/service-contracts/{id}",
     *     summary="Delete a service contract",
     *     tags={"Service Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service contract",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Service contract deleted successfully"),
     *     @OA\Response(response=404, description="Service contract not found"),
     *     @OA\Response(response=400, description="Cannot delete, tickets associated")
     * )
     */
    public function destroy($id)
    {
        try {
            // Check for associated tickets
            $serviceContract = $this->serviceContractRepositoryInterface->getById($id);
            if ($serviceContract->tickets()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete, tickets associated', 400);
            }

            $this->serviceContractRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('ServiceContract Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete service contract', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/service-contracts/company/{id}",
     *     summary="Get service contracts for a specific company",
     *     tags={"Service Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of service contracts for the company",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceContractResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Company not found")
     * )
     */
    public function getContractsByCompany($id)
    {
        try {
            $user = Auth::user();

            if ($user->hasRole('client') && $user->company_id != $id) {
                abort(403, 'Unauthorized access to resource.');
            }

            $data = $this->serviceContractRepositoryInterface->getContractsByCompany($id);

            $data->load('company:id,name', 'service:id,description,price', 'serviceterm:id,months,term');

            foreach ($data as $serviceContract) {
                $serviceContract->price = ($serviceContract->service->price / 12) * $serviceContract->serviceterm->months;
                $serviceContract->expiration_date = $serviceContract->created_at->addMonths($serviceContract->serviceterm->months);
            }

            return ApiResponseClass::sendResponse(ServiceContractResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve service contracts by company', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/service-contracts/expiring",
     *     summary="Get a list of service contracts expiring in the next month",
     *     tags={"Service Contracts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of expiring service contracts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceContractResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getExpiringContracts()
    {
        try {
            $user = Auth::user();
            $nextMonth = Carbon::now()->addMonth();

            if ($user->hasRole('client')) {
                $data = ServiceContract::where('company_id', $user->company_id)
                    ->whereHas('serviceterm', function ($query) {
                        $query->where('months', '!=', 1);
                    })
                    ->where('expiration_date', '<=', $nextMonth)
                    ->get();
            } else {
                $data = ServiceContract::whereHas('serviceterm', function ($query) {
                    $query->where('months', '!=', 1);
                })
                ->where('expiration_date', '<=', $nextMonth)
                ->get();
            }

            $data->load('company:id,name', 'service:id,description,price', 'serviceterm:id,months,term');

            foreach ($data as $serviceContract) {
                $serviceContract->price = ($serviceContract->service->price / 12) * $serviceContract->serviceterm->months;
                $serviceContract->expiration_date = $serviceContract->created_at->addMonths($serviceContract->serviceterm->months);
            }

            return ApiResponseClass::sendResponse(ServiceContractResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve expiring service contracts', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/service-contracts/deleted",
     *     summary="Get a list of deleted service contracts",
     *     tags={"Service Contracts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted service contracts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceContractResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $data = $this->serviceContractRepositoryInterface->getDeleted();
            return ApiResponseClass::sendResponse(ServiceContractResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted service contracts', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/service-contracts/{id}/restore",
     *     summary="Restore a deleted service contract",
     *     tags={"Service Contracts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service contract",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Service contract restored successfully"),
     *     @OA\Response(response=404, description="Service contract not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->serviceContractRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('ServiceContract Restore Successful', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore service contract', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/service-contracts/request",
     *     summary="Request a new service",
     *     tags={"Service Contracts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "service_id", "service_term_id"},
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=10),
     *             @OA\Property(property="service_term_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service request sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceContractResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function requestService(Request $request)
    {
        try {
            $user = Auth::user();
            $user->load('company'); // Load the company relation
            $serviceData = [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_last_name' => $user->lastname,
                'company'=> $user->company->name,
                'service'=> Service::find($request->service_id)->description,
                'term'=> ServiceTerm::find($request->service_term_id)->term,
            ];

            $emailTemplate = Email::where('template_name', 'Solicitud de servicios')->first();
            $subjectLine = str_replace(
                ['{company}', '{service}', '{term}'],
                [$user->company->name, $serviceData['service'], $serviceData['term']],
                $emailTemplate->subject
            );
            $emailBody = $emailTemplate->body;

            foreach ($serviceData as $key => $value) {
                $emailBody = str_replace('{{ $serviceData[\'' . $key . '\'] }}', $value, $emailBody);
            }
            $recipient = env('MAIL_NOTIFICATIONS', 'info@mindsoft.biz');
            Mail::to($recipient)->send(new ServiceRequestMail($serviceData, 'emails.custom_template', $subjectLine, $emailBody));

            return ApiResponseClass::sendResponse(null, 'Service request sent successfully', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to send service request', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/service-contracts/cancel",
     *     summary="Request service cancellation",
     *     tags={"Service Contracts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"contract_id"},
     *             @OA\Property(property="contract_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service cancellation request sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceContractResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function requestCancellation(Request $request)
    {
        try {
            $user = Auth::user();
            $user->load('company'); // Load the company relation
            $serviceData = [
                'contract_id' => $request->contract_id,
                'service' => ServiceContract::with('service')->find($request->contract_id)->service->description,
                'company' => $user->company->name,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_last_name' => $user->lastname,
                
            ];

            $emailTemplate = Email::where('template_name', 'Solicitud de cancelación de servicios')->first();
            $subjectLine = str_replace(
                ['{service}', '{company}'],
                [$serviceData['company'], $user->company->name], // Use the company's name
                $emailTemplate->subject
            );

            $emailBody = $emailTemplate->body;

            foreach ($serviceData as $key => $value) {
                $emailBody = str_replace('{{ $serviceData[\'' . $key . '\'] }}', $value, $emailBody);
            }

            $recipient = env('MAIL_NOTIFICATIONS', 'info@mindsoft.biz');
            Mail::to($recipient)->send(new ServiceCancellationMail($serviceData, 'emails.custom_template', $subjectLine, $emailBody));

            return ApiResponseClass::sendResponse(null, 'Service cancellation request sent successfully', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to send service cancellation request', 500);
        }
    }
}
