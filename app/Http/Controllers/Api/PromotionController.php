<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index(Request $req)
    {
        $promotion = Promotion::all();

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "promotion" => $promotion
            ]
        ]);
    }

    public function create(Request $req)
    {
        $req->validate([
            "title" => "required",
            "subtitle" => "required"
        ]);

        $req['created_at'] = date("Y-m-d h:i:s");
        $req["user_id"] = $req->user->id;
        Promotion::insert($req->all());

        return $this->index($req);
    }

    public function update(Request $req)
    {
        $req->validate([
            "promotion_id" => "required|exists:promotions,id",
            "title" => "required",
            "subtitle" => "required"
        ]);

        $req['updated_at'] = date("Y-m-d h:i:s");
        $dataId = $req->promotion_id;
        unset($req["promotion_id"]);

        Promotion::where("id", $dataId)->update($req->all());

        return $this->index($req);
    }

    public function delete(Request $req)
    {
        $req->validate([
            "promotion_id" => "required|exists:promotions,id"
        ]);

        Promotion::find($req->promotion_id)->delete();
        
        return $this->index($req);
    }

    public function detail(Request $req)
    {
        $req->validate([
            "promotion_id" => "required|exists:promotions,id"
        ]);

        $data = Promotion::find($req->promotion_id);

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "promotion" => $data
            ]
        ]);
    }
}
