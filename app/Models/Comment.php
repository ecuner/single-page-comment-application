<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Comment extends Model
{
    use HasFactory, HasRecursiveRelationships;

    protected $appends = ['time_past'];
    protected $fillable = ['name', 'content', 'parent_id', 'created_at'];

    /**
     * Accessor for human readable time difference since comment is created.
     * 
     * Appended to model's array and JSON representation.
     * Like "one day ago", "one week ago" etc.
     *
     * @return string
     */
    public function getTimePastAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Returns the paginator object for root comments, to be used in frontend.
     * 
     * Pagination is done by $commentsPerPage root comments per page,
     * ordered by created_at, in descending order if $descending is 1.
     * Descendants of a comment located in "descendants" relation of respective
     * model in flattened fashion, because of the way laravel-adjacency-list works.
     * 
     * paginate() respects "page" input string from request.
     * 
     * If you don't need number of pages, you can pass true to $dontCountRecords.
     *
     * @param int $commentsPerPage
     * @param int $descending
     * @param bool $dontCountRecords
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function allForFrontend($commentsPerPage, $descending, $dontCountRecords = false) {
        $builder = self::isRoot()->with('descendants')
            ->orderBy('created_at',$descending ? 'desc' : 'asc');
        
        return $dontCountRecords ? $builder->simplePaginate($commentsPerPage) : $builder->paginate($commentsPerPage);
    }

    /**
     * Returns the last page when comments are paginated by $commentPerPage.
     *
     * @param int $commentsPerPage
     * @return int
     */
    public static function lastPage($commentsPerPage) {
        return self::isRoot()->paginate($commentsPerPage)->lastPage();
    }
}
