<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Fan;
use App\Models\Creator;

class TopFans extends Controller
{
    public function __invoke(Creator $creator)
    {
        // select count(*) as view_count, fan_id from views 
        // inner join posts on views.post_id=posts.id where posts.creator_id=1 group by fan_id;
        $viewCounts = DB::table('views')
            ->select('fan_id', DB::raw('count(*) as view_count'))
            ->join('posts', function($join) use ($creator) {
                $join->on('posts.id', '=', 'views.post_id')
                     ->where('posts.creator_id', '=', $creator->id);
            })
            ->groupBy('fan_id');
        
        $fans = DB::table('fans')
            ->joinSub($viewCounts, 'view_counts', function ($join) {
                $join->on('fans.id', '=', 'view_counts.fan_id');
            })
            ->orderByDesc('view_count')
            ->get();
        
        var_dump($fans);die();

        // Append Code for Share, Like, Comments.


        return view('top_fans', [
            'fans' => $fans,
            'creator' => $creator
        ]);
    }
}
