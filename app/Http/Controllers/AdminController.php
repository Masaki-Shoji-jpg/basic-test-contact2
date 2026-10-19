<?php

namespace App\Http\Controllers;


use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;

use App\Http\Requests\IndexContactRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexContactRequest $request)
    {
        $query = Contact::with(['category', 'tags'])
           ->filter($request->validated());

        $contacts = $query
            ->latest()
            ->paginate(7)
            ->appends($request->query());

        return view('admin.index', [
            'contacts' => $contacts,
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', [
        'contact' => $contact]);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.index');
    }
}
