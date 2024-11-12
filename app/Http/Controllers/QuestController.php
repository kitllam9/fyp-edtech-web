<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestRequest;
use App\Http\Requests\UpdateQuestRequest;
use App\Models\Quest;

class QuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('quest.index', ['quests' => Quest::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Quest $quest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quest $quest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestRequest $request, Quest $quest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quest $quest)
    {
        //
    }
}
