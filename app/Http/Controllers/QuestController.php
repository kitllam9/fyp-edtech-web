<?php

namespace App\Http\Controllers;

use App\Enum\QuestType;
use App\Http\Requests\StoreQuestRequest;
use App\Http\Requests\UpdateQuestRequest;
use App\Models\Quest;
use Illuminate\Support\Str;

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
        $_types = QuestType::cases();
        $types = array();

        $types = collect($_types)->mapWithKeys(function ($t) {
            return [$t->value => Str::title($t->name)];
        })->all();

        return view('quest.create', data: [
            'type' => $types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestRequest $request)
    {
        Quest::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'target' => $request->input('target'),
            'multiple_percentage_amount' => $request->input('multiple_percentage_amount'),
            'reward' => $request->input('reward'),
        ]);

        return redirect()->route('quests');
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
        $_types = QuestType::cases();
        $types = array();

        $types = collect($_types)->mapWithKeys(function ($t) {
            return [$t->value => Str::title($t->name)];
        })->all();

        return view('quest.edit', data: [
            'quest' => $quest,
            'type' => $types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestRequest $request, Quest $quest)
    {
        $quest->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'target' => $request->input('target'),
            'multiple_percentage_amount' => $request->input('multiple_percentage_amount'),
            'reward' => $request->input('reward'),
        ]);

        return redirect()->route('quests');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quest $quest)
    {
        $quest->delete();
        return redirect()->route('quests');
    }
}
