<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StateSpecificClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

class StateSpecificClauseController extends Controller
{
    public function index(Request $request)
    {
        $state = $request->get('state', 'all');
        $clauseType = $request->get('clause_type', 'all');

        $query = StateSpecificClause::query();
        if ($state !== 'all') {
            $query->forState($state);
        }

        if ($clauseType !== 'all') {
            $query->where('clause_type', $clauseType);
        }

        $clauses = $query->orderBy('state')->orderBy('title')->paginate(20);
        $states  = StateSpecificClause::getStates();

        return view('admin.documents.state_specific_clauses', compact('clauses', 'states', 'state', 'clauseType'));
    }

    public function create()
    {
        $states = StateSpecificClause::getStates();
        return view('admin.state-specific-clauses.create', compact('states'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'text' => 'required|string',
            'clause_type' => 'required|string|in:national,state_specific',
            'states' => 'required_if:clause_type,state_specific|array|min:1',
            'states.*' => 'string',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.type'  => 'required_with:questions|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'description', 'text']);
        $data['clause_type'] = $request->input('clause_type');

        $questions = $request->input('questions', []);
        $placeholders = $this->extractPlaceholders($request->text);

        foreach ($questions as $index => &$question) {
            $question['placeholder'] = $placeholders[$index] ?? null;
        }

        $data['questions'] = array_values(array_filter($questions, function ($q) {
            return !empty($q['question']) && !empty($q['placeholder']);
        }));

        if ($data['clause_type'] === 'national') {
            StateSpecificClause::create(array_merge($data, ['state' => null]));
        } else {
            foreach ($request->input('states', []) as $state) {
                StateSpecificClause::create(array_merge($data, ['state' => $state]));
            }
        }

        return redirect()->route('index')
            ->with('success', 'State-specific clauses created successfully!');
    }

    public function edit($id)
    {
        $clause         = StateSpecificClause::findOrFail($id);
        $states         = StateSpecificClause::getStates();
        $clauseVersions = StateSpecificClause::where('title', $clause->title)
            ->orderBy('state')
            ->get();

        return view('admin.state-specific-clauses.edit', compact('clause', 'states', 'clauseVersions'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'text' => 'required|string',
            'clause_type' => 'required|string|in:national,state_specific',
            'state' => 'required_if:clause_type,state_specific|nullable|string',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.type'  => 'required_with:questions|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $clause              = StateSpecificClause::findOrFail($id);
        $data                = $request->only(['title', 'description', 'text']);
        $data['clause_type'] = $request->input('clause_type');

        $questions = $request->input('questions', []);

        $data['questions'] = !empty($questions)
            ? array_values(array_filter($questions, fn($q) => !empty($q['question'])))
            : null;

        $data['state'] = $data['clause_type'] === 'national'
            ? null
            : $request->input('state');

        $clause->update($data);

        return redirect()->route('index')->with('success', 'State-specific clause updated successfully!');
    }

    public function destroy($id)
    {
        StateSpecificClause::findOrFail($id)->delete();

        return redirect()->route('index')
            ->with('success', 'State-specific clause deleted successfully!');
    }

    public function destroyAll()
    {
        StateSpecificClause::truncate();

        return redirect()->route('index')
            ->with('success', 'All state-specific clauses deleted successfully!');
    }

    public function getByState(Request $request)
    {
        $state = $request->get('state');
        if (!$state) {
            return response()->json(['success' => false, 'message' => 'State parameter is required'], 400);
        }

        //  National rows  + state-specific rows matching this state
        $clauses = StateSpecificClause::where('is_active', true)
            ->where(function ($query) use ($state) {
                $query->where('clause_type', 'national')
                      ->orWhere(function ($q) use ($state) {
                          $q->where('clause_type', 'state_specific')
                            ->where('state', $state);
                      });
            })
            ->get(['id', 'title', 'description', 'text', 'questions', 'clause_type', 'state']);

        return response()->json($clauses);
    }

    public function toggleActive($id)
    {
        $clause = StateSpecificClause::findOrFail($id);
        $clause->is_active = !$clause->is_active;
        $clause->save();

        return redirect()->back()->with('success', 'Clause status updated successfully!');
    }

    public function getForAI(Request $request)
    {
        $state = $request->get('state');
        if (!$state) {
            return response()->json(['success' => false, 'message' => 'State parameter is required'], 400);
        }

        //  National rows + matching state-specific rows, no 'states' column referenced
        $clauses = StateSpecificClause::active()
            ->where(function ($query) use ($state) {
                $query->where('clause_type', 'national')
                      ->orWhere(function ($q) use ($state) {
                          $q->where('clause_type', 'state_specific')
                            ->forState($state);
                      });
            })
            ->get()
            ->map(fn($clause) => [
                'id'          => $clause->id,
                'title'       => $clause->title,
                'description' => $clause->description,
                'placeholder' => $clause->getPlaceholder(),
                'clause_type' => $clause->clause_type,
            ]);

        return response()->json($clauses);
    }

    public function aiAutoFill(Request $request)
    {
        try {
            $clauseType        = $request->input('clause_type');
            $states            = $request->input('states', []);
            $additionalContext = $request->input('context', '');

            if (empty($clauseType)) {
                return response()->json(['success' => false, 'message' => 'Please provide a clause type or description.'], 400);
            }

            $aiService  = new AIService();
            $aiResponse = $aiService->generateText($this->buildClausePrompt($clauseType, $states, $additionalContext));

            return response()->json(['success' => true, 'data' => $this->parseAIResponse($aiResponse)]);

        } catch (\Exception $e) {
            Log::error('AI Auto-fill error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate clause content. Please try again.'], 500);
        }
    }

    private function buildClausePrompt($clauseType, $states, $additionalContext)
    {
        $statesList = !empty($states) ? implode(', ', $states) : 'all US states';

        return <<<PROMPT
            Generate a comprehensive state-specific legal clause with the following requirements:

            **Clause Type:** {$clauseType}
            **Target States:** {$statesList}
            **Additional Context:** {$additionalContext}

            Please provide the following in a structured format:

            1. **Title:** A clear, professional title for this clause (max 100 characters)
            2. **Description:** A brief description (2-3 sentences) explaining when and why this clause should be used.
            3. **Clause Text:** The complete legal text. Include placeholders (e.g., [PARTY_NAME], [EFFECTIVE_DATE]).
            4. **Recommended Questions:** Suggest 4-6 questions. For each: Question text, Answer type, Purpose.

            Format:
            TITLE:
            [title]

            DESCRIPTION:
            [description]

            CLAUSE_TEXT:
            [clause text]

            QUESTIONS:
            1. Question: [text]
            Type: [type]
            Purpose: [purpose]
            PROMPT;
    }

    private function parseAIResponse($aiResponse)
    {
        $data = ['title' => '', 'description' => '', 'text' => '', 'questions' => []];

        if (preg_match('/TITLE:\s*(.+?)(?=DESCRIPTION:|$)/s', $aiResponse, $m))   $data['title']       = trim($m[1]);
        if (preg_match('/DESCRIPTION:\s*(.+?)(?=CLAUSE_TEXT:|$)/s', $aiResponse, $m)) $data['description'] = trim($m[1]);
        if (preg_match('/CLAUSE_TEXT:\s*(.+?)(?=QUESTIONS:|$)/s', $aiResponse, $m))   $data['text']        = trim($m[1]);

        if (preg_match('/QUESTIONS:\s*(.+)$/s', $aiResponse, $m)) {
            preg_match_all('/\d+\.\s*Question:\s*(.+?)\s*Type:\s*(.+?)\s*Purpose:/s', $m[1], $qm, PREG_SET_ORDER);
            foreach ($qm as $match) {
                $data['questions'][] = ['question' => trim($match[1]), 'type' => strtolower(trim($match[2]))];
            }
        }

        return $data;
    }

    private function extractPlaceholders(string $text): array
    {
        preg_match_all('/\[([A-Z0-9_]+)\]/', $text, $matches);
        return array_unique($matches[1]);
    }
}