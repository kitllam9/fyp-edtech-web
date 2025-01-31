<?php

namespace App\Http\Controllers;

use App\Enum\BadgeType;
use App\Http\Requests\StoreBadgeRequest;
use App\Http\Requests\UpdateBadgeRequest;
use App\Models\Badge;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('badge.index', ['badges' => Badge::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $_badgeTypes = BadgeType::cases();
        $badgeTypes = array();

        $badgeTypes = collect($_badgeTypes)->mapWithKeys(function ($bT) {
            return [$bT->value => Str::title($bT->name)];
        })->all();

        return view('badge.create', data: [
            'badge_type' => $badgeTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBadgeRequest $request)
    {
        Badge::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'target' => $request->input('target'),
        ]);

        return redirect()->route('badges');
    }



    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        $_badgeTypes = BadgeType::cases();
        $badgeTypes = array();

        $badgeTypes = collect($_badgeTypes)->mapWithKeys(function ($bT) {
            return [$bT->value => Str::title($bT->name)];
        })->all();

        return view('badge.edit', [
            'badge' => $badge,
            'badge_type' => $badgeTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBadgeRequest $request, Badge $badge)
    {

        $badge->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'target' => $request->input('target'),
        ]);

        return redirect()->route('badges');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('badges');
    }
}
