<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use App\Notifications\InvoiceIssuedNotification;

class RoomController extends Controller
{
    public function index(Request $req)
    {
        $rooms = Room::all();

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "room" => $rooms
            ]
        ]);
    }

    public function create(Request $req)
    {
        $req->validate([
            "images" => "required",
            "name" => "required",
            "desc" => "required",
            "price" => "required|integer",
        ]);

        $req['created_at'] = date("Y-m-d h:i:s");

        Room::insert($req->all());
        $rooms = Room::all();

        return response([
            "msg" => "Insert success",
            "code" => 200,
            "data" => [
                "room" => $rooms
            ]
        ]);
    }

    public function update(Request $req)
    {
        $req->validate([
            "room_id" => "required|exists:rooms,id",
            "images" => "required",
            "name" => "required",
            "desc" => "required",
            "price" => "required|integer",
        ]);

        $req['updated_at'] = date("Y-m-d h:i:s");
        $roomId = $req->room_id;
        unset($req["room_id"]);

        Room::where("id", $roomId)->update($req->all());
        $rooms = Room::all();

        return response([
            "msg" => "Update success",
            "code" => 200,
            "data" => [
                "room" => $rooms
            ]
        ]);
    }

    public function delete(Request $req)
    {
        $req->validate([
            "room_id" => "required|exists:rooms,id"
        ]);

        Room::find($req->room_id)->delete();
        $rooms = Room::all();

        return response([
            "msg" => "Delete success",
            "code" => 200,
            "data" => [
                "room" => $rooms
            ]
        ]);
    }

    public function detail(Request $req)
    {
        $req->validate([
            "room_id" => "required|exists:rooms,id"
        ]);

        $room = Room::find($req->room_id);

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "room" => $room
            ]
        ]);
    }

    public function reservationList(Request $req)
    {
        $isAdmin = $req->user->role_id == 1;
        if ($isAdmin) {
            $reservations = Reservation::select([
                                "reservations.*",
                                "rooms.name as room_name",
                                "users.name as guest_name"
                            ])
                            ->leftJoin("rooms","reservations.room_id","rooms.id")
                            ->leftJoin("users","reservations.user_id","users.id")
                            ->get();
        } else {
            $reservations = Reservation::select([
                                "reservations.*",
                                "rooms.name as room_name",
                                "users.name as guest_name"
                            ])
                            ->leftJoin("rooms","reservations.room_id","rooms.id")
                            ->leftJoin("users","reservations.user_id","users.id")
                            ->where("user_id", $req->user->id)
                            ->get();
        }

        return response([
            "msg" => "Success",
            "code" => 200,
            "data" => [
                "reservations" => $reservations
            ]
        ]);
    }

    public function book(Request $req)
    {
        $req->validate([
            "room_id" => "required|exists:rooms,id",
            "duration_in_day" => "required|integer",
            "check_in" => "required|date",
            "check_out" => "required|date"
        ]);

        $room = Room::find($req->room_id);

        $req["grand_total"] = ((int) $req->duration_in_day) * $room->price;
        $req["user_id"] = $req->user_id ?? $req->user->id;

        Reservation::insert($req->all());

        $user = User::find($req["user_id"]);
        $user->price = $req["grand_total"];
        $user->date = now()->addDay(1);
        $user->notify(new InvoiceIssuedNotification($user));

        return $this->reservationList($req);
    }

    public function cancelReservation(Request $req)
    {
        $req->validate([
            "reservation_id" => "required|exists:reservations,id"
        ]);

        Reservation::where("id", $req->reservation_id)->update([
            "is_cancelled" => 1
        ]);

        return $this->reservationList($req);
    }

    public function confirmPaymentReservation(Request $req)
    {
        $req->validate([
            "reservation_id" => "required|exists:reservations,id"
        ]);

        Reservation::where("id", $req->reservation_id)->update([
            "is_paid" => 1
        ]);

        return $this->reservationList($req);
    }
}
