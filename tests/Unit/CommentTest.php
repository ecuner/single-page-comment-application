<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Comment;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected $seed = true;
    protected $seeder = \Database\Seeders\CommentSeeder::class;

    public function testPage()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Comments');
    }

    public function testCommentListing()
    {
        $commentsPerPage = min(Comment::isRoot()->count(), CommentController::COMMENTS_PER_PAGE);

        $this->get(route('get_comments_json'))
            ->assertStatus(200)
            ->assertJsonCount($commentsPerPage, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                         'id', 'name', 'content', 'parent_id', 'time_past', 'descendants'
                    ]
                ]
            ]);
    }

    public function testPostComment()
    {
        // Add subcomment
        $randomCommentToReply = Comment::inRandomOrder()->first();
        $response = $this->post(route('post_comment'), [
            'name' => $this->faker->name,
            'content' => $this->faker->paragraph,
            'parent_id' => $randomCommentToReply->id]);
 
        $response->assertStatus(200)
            ->assertJsonStructure(['new_comment_id']);

        // Add comment
        $response = $this->post(route('post_comment'), [
            'name' => $this->faker->name,
            'content' => $this->faker->paragraph,
            'parent_id' => null]);
 
        $response->assertStatus(200)
            ->assertJsonStructure(['new_comment_id']);
    }

}
