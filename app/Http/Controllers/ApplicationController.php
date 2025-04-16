<?php
namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\ApplicationService;
use App\Http\Requests\ApplicationRequest;
use App\Http\Requests\ApproveApplicationRequest;
use App\Http\Requests\ApplicationStatusChangeRequest;
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
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $applications = $this->applicationService->getPaginatedApplications(search: $search, orderBy: $orderBy, orderDirection: $orderDirection);

        return responseWithData(
            $applications->isEmpty() ? 'Aplikasi tidak ditemukan' : 'Aplikasi berhasil ditemukan',
            ['applications' => $applications]
        );
    }

    public function getApproveData(Request $request)
    {
        $search = $request->query('search', '');
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $applications = $this->applicationService->getPaginatedApprove(search: $search, orderBy: $orderBy, orderDirection: $orderDirection);

        return responseWithData(
            $applications->isEmpty() ? 'Aplikasi tidak ditemukan' : 'Aplikasi berhasil ditemukan',
            ['applications' => $applications]
        );
    }

    //  Method untuk menyimpan data aplikasi
    public function store(ApplicationRequest $request)
    {
        $validated = $request->validated();

        // if ($this->applicationService->checkApplicationExists($validated['name'])) {
        //     return errorResponse('Aplikasi sudah terdaftar', 422);
        // }

        if ($this->applicationService->checkApplicationExists($validated['name'])) {
            return response()->json(['errors' => ['name' => ['Aplikasi sudah terdaftar']]], 422);
        }

        $application = $this->applicationService->createApplication($validated);


        // return redirect()->back()->with('message', 'Aplikasi berhasil didaftarkan');
        // return responseWithData(
        //     'Application registered successfully',
        //     $this->applicationService->formatApplicationResponse($application, $application->secret_key)
        // )->setStatusCode(201);

        return response()->json([
            'message'=> 'Aplikasi sukses teregistrasi',
            'application' => $application,
        ], 201);    
    }
    //Method untuk menampilkan detail aplikasi berdasarkan id
    public function show(Application $application)
    {
        return responseWithData(
            'Detail aplikasi berhasil ditemukan',
            $this->applicationService->formatApplicationResponse($application)
        );
    }

    //Method untuk menghapus aplikasi
    public function delete(Application $application, Request $request)
    {
        $request->validate(['ids' => 'required']);

        $this->applicationService->deleteApplications($request->ids);
        return response()->json(['success' => true, 'message' => 'Aplikasi berhasil dihapus']);
    }

    public function approveApplication(ApproveApplicationRequest $request)
    {
        // Panggil service untuk memproses regenerasi secret key
        $result = $this->applicationService->approveGenerateSecretKey($request->id);

        // Kembalikan response sesuai hasil dari service
        return response()->json([
            'message' => 'Aplikasi berhasil disetujui',
            'result' => $result
        ]);
    }

    public function handleApplicationStatusChange(ApplicationStatusChangeRequest $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $result = $this->applicationService->handleApplicationStatusChange($id, $status);
        
       return $result;
    }
}