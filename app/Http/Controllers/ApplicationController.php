<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationService;
use App\Http\Requests\ApplicationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    protected $applicationService;

    // Constructor untuk memanggil ApplicationService
    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    //Method untuk menampilkan semua data aplikasi
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $applications = $this->applicationService->getPaginatedApplications(search: $search);

        return Inertia::render('Application', [
            'data' => $applications
        ]);

        // return responseWithData(
        //     $applications->isEmpty() ? 'No applications found' : 'Applications retrieved successfully',
        //     ['applications' => $applications]
        // );
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $applications = $this->applicationService->getPaginatedApplications(search: $search, orderBy: $orderBy, orderDirection: $orderDirection);

        return responseWithData(
            $applications->isEmpty() ? 'No applications found' : 'Applications retrieved successfully',
            ['applications' => $applications]
        );
    }

    //  Method untuk menyimpan data aplikasi
    public function store(ApplicationRequest $request)
    {
        $validated = $request->validated();

        if ($this->applicationService->checkApplicationExists($validated['name'])) {
            return errorResponse('Aplikasi sudah terdaftar', 422);
        }

        $application = $this->applicationService->createApplication($validated);


        // return redirect()->back()->with('message', 'Aplikasi berhasil didaftarkan');
        return responseWithData(
            'Application registered successfully',
            $this->applicationService->formatApplicationResponse($application, $application->secret_key)
        )->setStatusCode(201);
    }
    //Method untuk menampilkan detail aplikasi berdasarkan id
    public function show(Application $application)
    {
        return responseWithData(
            'Application details retrieved successfully',
            $this->applicationService->formatApplicationResponse($application)
        );
    }
    //Method untuk regenerate secret key aplikasi
    public function regenerateSecret(Request $request, Application $application)
    {
        $request->validate(['password' => 'required']);

        $newSecretKey = $this->applicationService->regenerateSecretKey(
            $application,
            $request->password
        );

        if (!$newSecretKey) {
            return redirect()->back()->with('error', 'Invalid password');
            // return errorResponse('Invalid password', 401);
        }

        return redirect()->back()->with('message', 'Secret key regenerated successfully');
        // return responseWithData(
        //     'Secret key regenerated successfully',
        //     ['secret_key' => $newSecretKey]
        // );
    }

    //Method untuk menghapus aplikasi
    public function delete(Application $application, Request $request)
    {
        $request->validate(['ids' => 'required']);

        $this->applicationService->deleteApplications($request->ids);

        return redirect()->back()->with('message', 'Applications deleted successfully');

        // return responseWithData(
        //     'Applications deleted successfully',
        //     []
        // )->setStatusCode(200);
    }
}