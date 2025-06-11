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

    //Function untuk menampilkan semua data aplikasi
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $applications = $this->applicationService->getPaginatedApplications(search: $search);

        return Inertia::render('Application', [
            'data' => $applications
        ]);
    }

    //Function untuk ambil data aplikasi yang sudah terdaftar
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

    //Function untuk ambil data aplikasi yang perlu disetujui
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

    //Function untuk menyimpan data aplikasi yang diregistrasi
    public function store(ApplicationRequest $request)
    {
        $validated = $request->validated();

        $application = $this->applicationService->createApplication($validated);

        return response()->json([
            'message' => 'Aplikasi sukses teregistrasi',
            'application' => $application,
        ], 201);
    }

    //Function untuk menampilkan detail aplikasi berdasarkan id
    public function show(Application $application)
    {
        return responseWithData(
            'Detail aplikasi berhasil ditemukan',
            $this->applicationService->formatApplicationResponse($application)
        );
    }

    //Function untuk menghapus aplikasi
    public function delete(Application $application, Request $request)
    {
        $request->validate(['ids' => 'required']);

        $this->applicationService->deleteApplications($request->ids);
        return response()->json(['success' => true, 'message' => 'Aplikasi berhasil dihapus']);
    }

    //Function untuk approve aplikasi
    public function approveApplication(ApproveApplicationRequest $request)
    {
        $result = $this->applicationService->approveGenerateSecretKey($request->id);

        return response()->json([
            'message' => 'Aplikasi berhasil disetujui',
            'result' => $result
        ]);
    }

    //Function untuk handle perubahan status aplikasi
    public function handleApplicationStatusChange(ApplicationStatusChangeRequest $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $result = $this->applicationService->handleApplicationStatusChange($id, $status);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['status']);
    }
}