<?php

// File: app/Services/ContractStateClauseService.php

namespace App\Services;

use App\Models\StateSpecificClause;
use App\Models\Contract;
use Illuminate\Support\Str;

class ContractStateClauseService
{
    /**
     * Get all state-specific clauses formatted for AI
     */
    public function getClausesForAI(string $state): array
    {
        $clauses = StateSpecificClause::active()
            ->forState($state)
            ->get();

        return $clauses->map(function ($clause) {
            return [
                'id' => $clause->id,
                'title' => $clause->title,
                'description' => $clause->description,
                'placeholder' => $clause->getPlaceholder(),
            ];
        })->toArray();
    }

    /**
     * Prepare AI prompt with state-specific clauses information
     */
    public function prepareAIPrompt(Contract $contract, string $state): string
    {
        $clauses = $this->getClausesForAI($state);

        if (empty($clauses)) {
            return '';
        }

        $prompt = "\n\nAVAILABLE STATE-SPECIFIC CLAUSES FOR {$state}:\n";
        $prompt .= "You may insert the following state-specific clauses using their placeholders:\n\n";

        foreach ($clauses as $clause) {
            $prompt .= "- Placeholder: {$clause['placeholder']}\n";
            $prompt .= "  Title: {$clause['title']}\n";
            $prompt .= "  When to use: {$clause['description']}\n\n";
        }

        $prompt .= "IMPORTANT RULES:\n";
        $prompt .= "1. Only use placeholders from the list above\n";
        $prompt .= "2. Place state-specific clauses AFTER the main contract body\n";
        $prompt .= "3. Place state-specific clauses BEFORE standard clauses\n";
        $prompt .= "4. Do NOT include the full text of the clause, only the placeholder\n";
        $prompt .= "5. Format placeholders exactly as shown (e.g., {{STATE_CLAUSE_123}})\n";

        return $prompt;
    }

    /**
     * Validate placeholders in contract text
     */
    public function validatePlaceholders(string $contractText, string $state): array
    {
        // Find all state clause placeholders
        preg_match_all('/\{\{STATE_CLAUSE_(\d+)\}\}/', $contractText, $matches);
        
        $errors = [];
        $foundIds = $matches[1] ?? [];

        if (empty($foundIds)) {
            return []; // No placeholders found, validation passes
        }

        // Get valid clause IDs for this state
        $validClauseIds = StateSpecificClause::active()
            ->forState($state)
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        // Check each placeholder
        foreach ($foundIds as $clauseId) {
            if (!in_array($clauseId, $validClauseIds)) {
                $errors[] = "Invalid state-specific clause placeholder: {{STATE_CLAUSE_{$clauseId}}}. This clause does not exist or is not available for {$state}.";
            }
        }

        return $errors;
    }

    /**
     * Replace placeholders with actual clause text
     */
    public function replacePlaceholders(string $contractText, string $state): string
    {
        // Find all state clause placeholders
        preg_match_all('/\{\{STATE_CLAUSE_(\d+)\}\}/', $contractText, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $clauseId = $match[1];
            $placeholder = $match[0];

            // Get the clause
            $clause = StateSpecificClause::active()
                ->forState($state)
                ->find($clauseId);

            if ($clause) {
                // Replace placeholder with actual clause text
                $contractText = str_replace($placeholder, $clause->text, $contractText);
            }
        }

        return $contractText;
    }

    /**
     * Get questions from assigned state-specific clauses
     */
    public function getQuestionsFromClauses(Contract $contract, string $state): array
    {
        $questions = [];

        // Get assigned state-specific clauses for this contract
        $clauseIds = $contract->stateSpecificClauses()->pluck('state_specific_clauses.id')->toArray();

        if (empty($clauseIds)) {
            return $questions;
        }

        // Get clauses with questions for the specified state
        $clauses = StateSpecificClause::whereIn('id', $clauseIds)
            ->forState($state)
            ->whereNotNull('questions')
            ->get();

        foreach ($clauses as $clause) {
            if ($clause->questions && is_array($clause->questions)) {
                foreach ($clause->questions as $question) {
                    $questions[] = array_merge($question, [
                        'clause_id' => $clause->id,
                        'clause_title' => $clause->title,
                    ]);
                }
            }
        }

        return $questions;
    }

    /**
     * Attach state-specific clauses to contract
     */
    public function attachClausesToContract(Contract $contract, array $clauseIds): void
    {
        $contract->stateSpecificClauses()->sync($clauseIds);
    }

    /**
     * Get contract structure order
     */
    public function getContractStructure(): array
    {
        return [
            1 => 'Main Contract Body',
            2 => 'State-Specific Clauses',
            3 => 'Standard Clauses',
            4 => 'Signature Block',
        ];
    }

    /**
     * Build complete contract with proper ordering
     */
    public function buildCompleteContract(
        string $mainBody,
        array $stateSpecificClauses,
        array $standardClauses,
        string $signatureBlock,
        string $state
    ): string {
        $contract = $mainBody . "\n\n";

        // Add state-specific clauses
        if (!empty($stateSpecificClauses)) {
            $contract .= "## STATE-SPECIFIC PROVISIONS\n\n";
            foreach ($stateSpecificClauses as $clause) {
                $stateClause = StateSpecificClause::forState($state)->find($clause['id']);
                if ($stateClause) {
                    $contract .= "### {$stateClause->title}\n\n";
                    $contract .= $stateClause->text . "\n\n";
                }
            }
        }

        // Add standard clauses
        if (!empty($standardClauses)) {
            $contract .= "## STANDARD CLAUSES\n\n";
            foreach ($standardClauses as $clause) {
                $contract .= "### {$clause['title']}\n\n";
                $contract .= $clause['text'] . "\n\n";
            }
        }

        // Add signature block
        $contract .= $signatureBlock;

        return $contract;
    }

    /**
     * Extract clause IDs from contract text
     */
    public function extractClauseIds(string $contractText): array
    {
        preg_match_all('/\{\{STATE_CLAUSE_(\d+)\}\}/', $contractText, $matches);
        return array_map('intval', $matches[1] ?? []);
    }
}