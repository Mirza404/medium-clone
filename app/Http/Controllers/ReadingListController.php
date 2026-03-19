<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingListRequest;
use App\Http\Requests\UpdateReadingListRequest;
use App\Models\ReadingList;

class ReadingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $readingLists = ReadingList::query()
            ->whereBelongsTo($user)
            ->withCount('posts')
            ->latest()
            ->get();

        return view('reading-list.index', ['readingLists' => $readingLists]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reading-list.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReadingListRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = $request->user()->id;

        $data['slug'] = Str::slug($data['title']);

        $data['slug'] = $this->makeUniqueSlug($data['slug']);

        $reading_list = $request->user()->readingLists()->create($data);

        return redirect()->route('reading-lists.show', ['readingList' => $reading_list]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ReadingList $readingList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadingList $readingList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReadingListRequest $request, ReadingList $readingList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadingList $readingList)
    {
        //
    }
}
