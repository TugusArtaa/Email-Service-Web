<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationService;
use App\Http\Requests\ApplicationRequest;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function index()
    {
        $applications = $this->applicationService->getAllApplications();

        return responseWithData(
            $applications->isEmpty() ? 'No applications found' : 'Applications retrieved successfully',
            ['applications' => $applications]
        );
    }

    public function store(ApplicationRequest $request)
    {
        $validated = $request->validated();

        if ($this->applicationService->checkApplicationExists($validated['name'])) {
            return errorResponse('Aplikasi sudah terdaftar', 422);
        }

        $application = $this->applicationService->createApplication($validated);
        
        return responseWithData(
            'Application registered successfully',
            $this->applicationService->formatApplicationResponse($application, $application->secret_key)
        )->setStatusCode(201);
    }

    public function show(Application $application)
    {
        return responseWithData(
            'Application details retrieved successfully',
            $this->applicationService->formatApplicationResponse($application)
        );
    }

    public function regenerateSecret(Request $request, Application $application)
    {
        $request->validate(['password' => 'required']);

        $newSecretKey = $this->applicationService->regenerateSecretKey(
            $application,
            $request->password
        );

        if (!$newSecretKey) {
            return errorResponse('Invalid password', 401);
        }

        return responseWithData(
            'Secret key regenerated successfully',
            ['secret_key' => $newSecretKey]
        );
    }
}