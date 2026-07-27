<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\TrainingClass;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PortalController extends Controller
{
    /** داشبورد ورزشکار */
    public function athlete(Request $request): View
    {
        $user = $request->user();

        $enrollments = Enrollment::query()
            ->where(fn ($q) => $q->where('user_id', $user->id)->orWhere('national_code', $user->national_code))
            ->with(['trainingClass.coach', 'trainingClass.sessions', 'transactions', 'documents'])
            ->latest()
            ->get();

        $active = $enrollments->firstWhere(
            fn (Enrollment $e) => in_array($e->status, [EnrollmentStatus::Paid, EnrollmentStatus::Approved], true),
        );

        return view('portal.athlete', [
            'user' => $user,
            'profile' => $user->athlete()->with(['belt', 'coach', 'achievements'])->first(),
            'enrollments' => $enrollments,
            'activeClass' => $active?->trainingClass,
            'payments' => $enrollments->flatMap->transactions->sortByDesc('created_at'),
        ]);
    }

    /** داشبورد مربی */
    public function coach(Request $request): View
    {
        $user = $request->user();
        $coach = $user->coach()->with('belt')->first();

        $classes = $coach
            ? TrainingClass::query()
                ->where('coach_id', $coach->id)
                ->with(['sessions', 'enrollments' => fn ($q) => $q->whereIn('status', [
                    EnrollmentStatus::Paid->value,
                    EnrollmentStatus::Approved->value,
                ])])
                ->ordered()
                ->get()
            : collect();

        return view('portal.coach', [
            'user' => $user,
            'coach' => $coach,
            'classes' => $classes,
            'studentCount' => $classes->sum(fn (TrainingClass $c) => $c->enrollments->count()),
            'pending' => Enrollment::query()
                ->whereIn('training_class_id', $classes->pluck('id'))
                ->where('status', EnrollmentStatus::Pending)
                ->with('trainingClass')
                ->latest()
                ->get(),
        ]);
    }
}
