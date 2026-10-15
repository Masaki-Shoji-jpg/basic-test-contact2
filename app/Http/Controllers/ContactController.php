<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('contact.index',[
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }



    public function confirm(ContactRequest $request)
    {
        $validated = $request->validated();
        $category = Category::findOrFail($validated['category_id']);
        $tagIds = $validated['tag_ids'] ?? [];
        $tags = Tag::whereIn('id', $tagIds)->get();
        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request)
    {
        $contact = Contact::create(
            $request->validated()
        );
        if ($request->filled('tag_ids')){
            $contact->tags()->attach($request->tag_ids);
        }
        return redirect()->route('contact.thanks');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
