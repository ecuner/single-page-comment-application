<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Http\Controllers\CommentController;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commentNum = 50;
        for ($i = 0; $i < $commentNum; $i++) {
            $builder = Comment::factory();

            $subCommentLayers = rand(0, CommentController::NESTED_COMMENTS_LAYER_LIMIT);
            // Add from leaf to root
            for ($j = 0; $j < $subCommentLayers; $j++) {
                $subCommentNum = rand(1, 4);
                $highestSub = Comment::factory()->count($subCommentNum);
                if (isset($prev)) {
                    $highestSub = $highestSub->has($prev, 'children');
                }
                $prev = $highestSub;
            }
            if (isset($highestSub)) {
                $builder = $builder->has($highestSub, 'children');
            }
            $builder->create();
            unset($highestSub);
            unset($prev);
        }
    }
}
