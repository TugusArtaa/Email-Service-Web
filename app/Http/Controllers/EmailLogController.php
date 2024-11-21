<?php

namespace App\Http\Controllers;

use App\Services\EmailLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailLogController extends Controller
{
    protected $emailLogService;

    public function __construct(EmailLogService $emailLogService)
    {
        $this->emailLogService = $emailLogService;
    }

    // Method untuk menampilkan data email yang tersimpan dalam log database
    public function index()
    {
        // Ambil semua data EmailLog dari service
        $emailLogs = $this->emailLogService->getAllEmailLogs();

        // Render halaman 'Home' menggunakan Inertia.js dan mengirimkan data emailLogs sebagai 'data'
        return Inertia::render('Home', [
            'data' => $emailLogs,
        ]);
    }
    public function integrasi(Request $request)
    {
        $search = $request->query('search', '');
        $orderBy = $request->query('orderBy', 'desc');
        // Ambil semua data EmailLog dari service
        $emailLogs = $this->emailLogService->getEmailLogs(search: $search, orderBy: $orderBy);

        // Render halaman 'Home' menggunakan Inertia.js dan mengirimkan data emailLogs sebagai 'data'
        return responseWithData(
            $emailLogs->isEmpty() ? 'No email logs found' : 'Email logs retrieved successfully',
            ['emailLogs' => $emailLogs]
        );
    }
    // Method untuk bulk delete email logs berdasarkan filter tanggal dan application_id
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'application_id' => 'nullable|integer',
        ]);
    
        $startDate = $request->input('start_date') ?? now()->startOfDay()->toDateString();
        $endDate = $request->input('end_date') 
            ? \Carbon\Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString()
            : \Carbon\Carbon::parse($startDate)->endOfDay()->toDateTimeString();
        // dd($startDate, $endDate);
        try {
            $deletedCount = $this->emailLogService->deleteEmailLogs(
                $startDate,
                $endDate,
            );
    
            if ($deletedCount === 0) {
                // return errorResponse("No email logs found for the given criteria.", 404);
                return redirect()->back()->with('error', 'No email logs found for the given criteria.');
            }
            
            return redirect()->back()->with('success', "Deleted $deletedCount email logs successfully.");
            // return responseSuccess("Deleted $deletedCount email logs successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "An error occurred while deleting email logs: " . $e->getMessage());
            // return errorResponse("An error occurred while deleting email logs: " . $e->getMessage(), 500);
        }
    }

    // Method untuk bulk delete email logs berdasarkan checkbox selection
    public function deleteAll(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->input('ids');
        $this->emailLogService->deleteEmailLogsByIds($ids);
        return redirect()->back()->with('success', 'Selected email logs deleted successfully.');
        // return responseSuccess('Selected email logs deleted successfully.');
   }
}