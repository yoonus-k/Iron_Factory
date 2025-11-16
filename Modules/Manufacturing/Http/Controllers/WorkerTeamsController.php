<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WorkerTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkerTeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = WorkerTeam::latest()->paginate(15);
        return view('manufacturing::worker-teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $workers = User::where('is_active', true)->get();
        return view('manufacturing::worker-teams.create', compact('workers'));
    }

    /**
     * Generate unique team code
     */
    public function generateTeamCode()
    {
        $prefix = 'TEAM';
        $date = now()->format('Ymd');
        
        // Get the last team code for today
        $lastTeam = WorkerTeam::whereDate('created_at', today())
            ->where('team_code', 'like', "$prefix-$date-%")
            ->orderBy('team_code', 'desc')
            ->first();
        
        if ($lastTeam) {
            $lastNumber = (int) substr($lastTeam->team_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        $teamCode = "$prefix-$date-$newNumber";
        
        return response()->json(['team_code' => $teamCode]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_code' => 'required|string|max:50|unique:worker_teams,team_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'workers' => 'required|array|min:1',
            'workers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $workerIds = $request->input('workers', []);
            
            $team = WorkerTeam::create([
                'team_code' => $request->team_code,
                'name' => $request->name,
                'description' => $request->description,
                'worker_ids' => $workerIds,
                'workers_count' => count($workerIds),
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('manufacturing.worker-teams.show', $team->id)
                ->with('success', 'تم إنشاء المجموعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $team = WorkerTeam::findOrFail($id);
        $workers = $team->workers();

        return response()
            ->view('manufacturing::worker-teams.show', compact('team', 'workers'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $team = WorkerTeam::findOrFail($id);
        $workers = User::where('is_active', true)->get();

        return view('manufacturing::worker-teams.edit', compact('team', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $team = WorkerTeam::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'team_code' => 'required|string|max:50|unique:worker_teams,team_code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'workers' => 'required|array|min:1',
            'workers.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $workerIds = $request->input('workers', []);

            $team->update([
                'team_code' => $request->team_code,
                'name' => $request->name,
                'description' => $request->description,
                'worker_ids' => $workerIds,
                'workers_count' => count($workerIds),
            ]);

            DB::commit();

            return redirect()->route('manufacturing.worker-teams.show', $team->id)
                ->with('success', 'تم تحديث المجموعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Toggle team status
     */
    public function toggleStatus($id)
    {
        try {
            $team = WorkerTeam::findOrFail($id);
            $team->is_active = !$team->is_active;
            $team->save();

            $status = $team->is_active ? 'تفعيل' : 'تعطيل';

            return redirect()->back()
                ->with('success', "تم {$status} المجموعة بنجاح");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة المجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $team = WorkerTeam::findOrFail($id);
            $team->delete();

            return redirect()->route('manufacturing.worker-teams.index')
                ->with('success', 'تم حذف المجموعة بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Get team workers (API endpoint)
     */
    public function getTeamWorkers($id)
    {
        try {
            $team = WorkerTeam::findOrFail($id);
            return response()->json([
                'success' => true,
                'worker_ids' => $team->worker_ids ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'المجموعة غير موجودة'
            ], 404);
        }
    }
}
