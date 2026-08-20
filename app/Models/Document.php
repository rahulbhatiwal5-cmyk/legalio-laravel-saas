<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug'];

    public function documentAgreement(){
        return $this->hasMany(DocumentAgreement::class,'document_id','id');
    }

    public function documentGuide(){
        return $this->hasMany(DocumentGuide::class,'document_id','id');
    }

    public function documentField(){
        return $this->hasMany(DocumentsField::class, 'document_id', 'id');
    }

    public function documentFaq(){
        return $this->hasMany(DocumentFaq::class, 'document_id', 'id')->orderBy('is_ai', 'asc');;
    }

    public function relatedDocuments(){
        return $this->belongsToMany(Document::class,  'document_related', 'document_id', 'related_document_id')->where('published', 1);
    }

    public function reviews(){
        return $this->hasMany(Review::class,'document_id','id');
    }

    public function orders(){
        return $this->hasMany(Order::class,'document_id','id');
    }

    public function categories(){
        return $this->belongsToMany(DocumentCategory::class,'document_with_categories', 'document_id', 'category_id');
    }

    public function getavgRating()
    {
        $minNumReviews=(int) web_setting('min_num_reviews_for_publish' , true );

        $approvedReviews = $this->reviews()
        ->where('status', 1)
        ->where('rating',5)
        ->get();
       
        // Step 3: Check if the total number of approved reviews is less than required
        if ($approvedReviews->count() < $minNumReviews) {
            return false;
        }

        // Step 4: Calculate and return average rating
        $averageRating = round($approvedReviews->avg('rating'), 2); // Rounded to 2 decimals
        return $averageRating;
      
    }

    public static function getAllDocumentAvgRating(){
        $averageRating = Review::where('status', 1)
        ->whereHas('document', function ($query) {
            $query->where('published', 1);
        })
        ->avg('rating');
        // dd($averageRating);
        return round($averageRating, 2);
    }




    public function shortDescription($limit = 70)
    {
        return Str::limit($this->short_description, $limit, '...');
    }


    public function getDocumentImageAttribute($value)
    {
        return $value ? asset($value) : null;
    }


    public function getSimilarDocuments()
    {
        // Get all published documents except the current one
        $documents = self::where('id', '!=', $this->id)
            ->where('published', 1)
            ->get();

        $similarities = [];

        foreach ($documents as $doc) {
            // Compute Levenshtein distance (lower is better)
            $titleDistance = levenshtein($this->title, $doc->title);
            $descDistance = levenshtein($this->short_description, $doc->short_description);

            // Normalize scores (invert since lower distance = higher similarity)
            $titleSimilarity = 100 - ($titleDistance / max(strlen($this->title), 1) * 100);
            $descSimilarity = 100 - ($descDistance / max(strlen($this->short_description), 1) * 100);

            // Check if categories match (1 if same category, 0 if different)
            $categoryMatch = ($this->category_id == $doc->category_id) ? 100 : 0;

            // Weighted score: title (50%), description (20%), category (30%)
            $totalScore = ($titleSimilarity * 0.5) + ($descSimilarity * 0.2) + ($categoryMatch * 0.3);

            $similarities[$doc->id] = $totalScore;
        }

        // Sort documents by highest similarity
        arsort($similarities);

        // Get the top 5 most similar documents
        $recommendedIds = array_keys(array_slice($similarities, 0, 5, true));

        // If fewer than 5, fill the gap with random documents
        if (count($recommendedIds) < 5) {
            $extraDocs = self::whereNotIn('id', array_merge([$this->id], $recommendedIds))
                ->inRandomOrder()
                ->limit(5 - count($recommendedIds))
                ->pluck('id')
                ->toArray();

            $recommendedIds = array_merge($recommendedIds, $extraDocs);
        }

        return array_slice($recommendedIds, 0, 5);
    }

    public function scopeUnreviewedPaidDocuments($query)
    {
        $userId = Auth::id();

        return $query->whereHas('orders', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status' ,1);
            })
            ->whereDoesntHave('reviews', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
    }

    public function relatedQuestions()
    {
        return $this->hasMany(Question::class, 'document_id', 'id');
    }

    public function relatedTexts()
    {
        return $this->hasMany(DocumentRightSection::class, 'document_id', 'id');
    }
}


