<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\GeneralAppNotification;
// 🟢 تم استدعاء ملفات التحقق الجديدة هنا 🟢
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function index()
    {
        $user = auth()->user();
        $query = Client::with('assignedUsers');
        
        // فلتر الرؤية للموظف العادي
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('visibility', 'all')
                  ->orWhere(function($sq) use ($user) {
                      $sq->where('visibility', 'specific')
                         ->whereHas('assignedUsers', function($q2) use ($user) {
                             $q2->where('users.id', $user->id);
                         });
                  });
            });
        }

        $activeClients = (clone $query)->where('status', 'active')->orderBy('created_at', 'desc')->get();
        $inactiveClients = (clone $query)->where('status', 'inactive')->orderBy('created_at', 'desc')->get();

        return view('clients.index', compact('activeClients', 'inactiveClients'));
    }

    /**
     * إضافة عميل جديد
     */
    public function store(StoreClientRequest $request) // 🟢 استخدام الـ Request الجديد
    {
        try {
            // حساب تاريخ الانتهاء
            $endDate = null;
            if ($request->sub_start_date && $request->subscription_duration) {
                $start = \Carbon\Carbon::parse($request->sub_start_date);
                $endDate = match($request->subscription_duration) {
                    'monthly' => $start->addMonth()->format('Y-m-d'),
                    'quarterly' => $start->addMonths(3)->format('Y-m-d'),
                    'semi_annual' => $start->addMonths(6)->format('Y-m-d'),
                    'annual' => $start->addYear()->format('Y-m-d'),
                    default => null,
                };
            }

            // إنشاء العميل
            $client = Client::create([
                'name' => $request->name,
                'package_type' => $request->package_type,
                'status' => $request->status,
                'sub_start_date' => $request->sub_start_date,
                'subscription_duration' => $request->subscription_duration,
                'sub_end_date' => $endDate,
                'is_active' => $request->status == 'active',
                'tax_number' => $request->tax_number,
                'commercial_register' => $request->commercial_register,
                'subscription_amount' => $request->subscription_amount,
                'visibility' => $request->visibility,
            ]);

            // ربط الموظفين (للرؤية المخصصة فقط)
            if ($request->visibility === 'specific' && $request->has('assigned_users')) {
                $client->assignedUsers()->attach($request->assigned_users);
                
                // إرسال إشعار للموظفين المخصصين
                $users = \App\Models\User::whereIn('id', $request->assigned_users)
                            ->where('id', '!=', auth()->id())
                            ->get();
                
                foreach ($users as $user) {
                    $user->notify(new GeneralAppNotification(
                        'عميل جديد 🏢',
                        'تم إضافتك لفريق العمل الخاص بالعميل: ' . $client->name,
                        'system', 
                        route('clients.show', $client->id)
                    ));
                }
            }

            return back()->with('success', 'تم إضافة العميل بنجاح! ✅');
            
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة عميل: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * تعديل بيانات العميل
     */
    public function update(UpdateClientRequest $request, Client $client) // 🟢 استخدام الـ Request الجديد
    {
        try {
            $endDate = $client->sub_end_date; 
            if ($request->sub_start_date && $request->subscription_duration) {
                $start = \Carbon\Carbon::parse($request->sub_start_date);
                $endDate = match($request->subscription_duration) {
                    'monthly' => $start->addMonth()->format('Y-m-d'),
                    'quarterly' => $start->addMonths(3)->format('Y-m-d'),
                    'semi_annual' => $start->addMonths(6)->format('Y-m-d'),
                    'annual' => $start->addYear()->format('Y-m-d'),
                    default => null,
                };
            } elseif (empty($request->subscription_duration)) {
                $endDate = null; 
            }

            $client->update([
                'name' => $request->name,
                'status' => $request->status,
                'package_type' => $request->package_type,
                'subscription_amount' => $request->subscription_amount,
                'visibility' => $request->visibility,
                'tax_number' => $request->tax_number,
                'commercial_register' => $request->commercial_register,
                'sub_start_date' => $request->sub_start_date,
                'subscription_duration' => $request->subscription_duration,
                'sub_end_date' => $endDate, 
            ]);

            // تحديث الموظفين وإرسال الإشعارات
            if ($request->visibility === 'specific' && $request->has('assigned_users')) {
                $client->assignedUsers()->sync($request->assigned_users);
                
                $users = \App\Models\User::whereIn('id', $request->assigned_users)
                            ->where('id', '!=', auth()->id())
                            ->get();
                
                foreach ($users as $user) {
                    $user->notify(new GeneralAppNotification(
                        'تحديث بيانات عميل 🔄',
                        'تم تحديث بيانات العميل: ' . $client->name,
                        'system', 
                        route('clients.show', $client->id)
                    ));
                }
            } else {
                $client->assignedUsers()->detach();
            }

            return back()->with('success', 'تم تحديث بيانات العميل بنجاح ✅');
            
        } catch (\Exception $e) {
            Log::error('خطأ في تعديل عميل: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء التعديل. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * جلب موظفي عميل محدد (AJAX)
     */
    public function getAssignedUsers(Client $client)
    {
        $admins = \App\Models\User::where('role', 'admin')->get();
        $assignedUsers = collect();

        if ($client->visibility === 'all') {
            $assignedUsers = \App\Models\User::where('role', '!=', 'admin')->get();
        } elseif ($client->visibility === 'specific') {
            $assignedUsers = $client->assignedUsers()->where('role', '!=', 'admin')->get();
        }

        $allUsers = $admins->merge($assignedUsers)->unique('id');
        return response()->json($allUsers);
    }

    /**
     * عرض ملف عميل محدد
     */
    public function show(Client $client)
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';
        $isWorkspace = (bool) $client->is_workspace; // 🟢 تم التعديل لمنع الـ Hardcoding

        // Eager Loading
        $client->load(['assignedUsers', 'documents', 'contacts', 'notes.user', 'tasks.assignedUsers']);

        // فحص الصلاحيات
        $isAssigned = false;
        if ($client->visibility === 'all') {
            $isAssigned = true;
        } elseif ($client->visibility === 'specific') {
            $isAssigned = $client->assignedUsers->pluck('id')->contains($user->id);
        }

        if (!$isAdmin && !$isWorkspace && !$isAssigned) {
            abort(403, 'ليس لديك صلاحية الدخول لملف هذا العميل.');
        }

        if ($client->status === 'inactive' && !$isAdmin) {
            abort(403, 'هذا العميل معطل أو مؤرشف.');
        }

        $allUsers = \App\Models\User::all();
        $employeesOnly = $allUsers->where('role', '!=', 'admin');
        $operations = $client->operations()->with(['user', 'reactions.user', 'replyTo.user'])->orderBy('created_at', 'asc')->get();

        // فلترة الملاحظات
        if ($isAdmin) {
            $clientNotes = $client->notes()->with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $clientNotes = $client->notes()->with('user')->where('type', 'alert')->orderBy('created_at', 'desc')->get();
        }

        return view('clients.show', compact(
            'client', 'isAdmin', 'isWorkspace', 'allUsers', 'employeesOnly', 
            'operations', 'clientNotes'
        ));
    }

    /**
     * حذف عميل
     */
    public function destroy(Client $client)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بحذف العملاء.');
        }

        try {
            $client->delete(); // Soft Delete
            return back()->with('success', 'تم حذف العميل بنجاح ✅');
            
        } catch (\Exception $e) {
            Log::error('خطأ في حذف عميل: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الحذف. يرجى المحاولة مرة أخرى.');
        }
    }
}