<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feed;

class NewsFeedController extends Controller
{
    public function index(Request $req)
    {
        $newsfeed = Feed::all();

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "newsfeed" => $newsfeed
            ]
        ]);
    }

    public function create(Request $req)
    {
        $req->validate([
            "title" => "required",
            "content" => "required"
        ]);

        $req['created_at'] = date("Y-m-d h:i:s");
        $req["user_id"] = $req->user->id;
        Feed::insert($req->all());

        return $this->index($req);
    }

    public function update(Request $req)
    {
        $req->validate([
            "feed_id" => "required|exists:feeds,id",
            "title" => "required",
            "content" => "required"
        ]);

        $req['updated_at'] = date("Y-m-d h:i:s");
        $feedId = $req->feed_id;
        unset($req["feed_id"]);

        Feed::where("id", $feedId)->update($req->all());

        return $this->index($req);
    }

    public function delete(Request $req)
    {
        $req->validate([
            "feed_id" => "required|exists:feeds,id"
        ]);

        Feed::find($req->feed_id)->delete();
        
        return $this->index($req);
    }

    public function detail(Request $req)
    {
        $req->validate([
            "feed_id" => "required|exists:feeds,id"
        ]);

        $data = Feed::find($req->feed_id);

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "newsfeed" => $data
            ]
        ]);
    }
}
