<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartiesSectionTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PartiesSectionTemplateController extends Controller
{
    public function index()
    {
        $templates = PartiesSectionTemplate::orderBy('parties_type')->paginate(20);
        return view('admin.parties_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.parties_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'parties_type'         => 'required|string|unique:parties_section_templates,parties_type',
            'name'                 => 'required|string|max:255',
            'party_a_count'        => 'required|integer|min:1',
            'party_b_count'        => 'required|integer|min:1',
            'parties_section_text' => 'nullable',
            'questions'            => 'nullable',
        ]);

        try {
            $questions = null;
            if ($request->filled('questions')) {
                $decoded   = json_decode($request->questions, true);
                $questions = is_array($decoded) ? $decoded : null;
            }

            $partiesSectionText = null;
            if ($request->filled('parties_section_text')) {
                $decoded            = json_decode($request->parties_section_text, true);
                $partiesSectionText = is_array($decoded) ? $decoded : null;
            }

            PartiesSectionTemplate::create([
                'parties_type'         => $request->parties_type,
                'name'                 => $request->name,
                'party_a_count'        => $request->party_a_count,
                'party_b_count'        => $request->party_b_count,
                'parties_section_text' => $partiesSectionText,
                'questions'            => $questions,
                'is_active'            => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.parties-templates')
                ->with('success', 'Parties template created successfully.');
        } catch (\Exception $e) {
            Log::error('PartiesSectionTemplate store error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create template: ' . $e->getMessage());
        }
    }

    public function edit(PartiesSectionTemplate $partiesTemplate)
    {
        return view('admin.parties_templates.edit', compact('partiesTemplate'));
    }

    public function update(Request $request, PartiesSectionTemplate $partiesTemplate)
    {
        $request->validate([
            'parties_type'         => 'required|string|unique:parties_section_templates,parties_type,' . $partiesTemplate->id,
            'name'                 => 'required|string|max:255',
            'party_a_count'        => 'required|integer|min:1',
            'party_b_count'        => 'required|integer|min:1',
            'parties_section_text' => 'nullable',
            'questions'            => 'nullable',
        ]);

        try {
            $questions = null;
            if ($request->filled('questions')) {
                $decoded   = json_decode($request->questions, true);
                $questions = is_array($decoded) ? $decoded : null;
            }

            $partiesSectionText = null;
            if ($request->filled('parties_section_text')) {
                $decoded            = json_decode($request->parties_section_text, true);
                $partiesSectionText = is_array($decoded) ? $decoded : null;
            }

            $partiesTemplate->update([
                'parties_type'         => $request->parties_type,
                'name'                 => $request->name,
                'party_a_count'        => $request->party_a_count,
                'party_b_count'        => $request->party_b_count,
                'parties_section_text' => $partiesSectionText,
                'questions'            => $questions,
                'is_active'            => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.parties-templates')
                ->with('success', 'Parties template updated successfully.');
        } catch (Exception $e) {
            Log::error('PartiesSectionTemplate update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update template: ' . $e->getMessage());
        }
    }

    public function destroy(PartiesSectionTemplate $partiesTemplate)
    {
        try {
            $partiesTemplate->delete();
            return redirect()->route('admin.parties-templates')
                ->with('success', 'Template deleted.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
}