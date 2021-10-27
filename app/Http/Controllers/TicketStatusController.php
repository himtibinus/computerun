<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TicketStatusController extends Controller
{
    /**
     * Converts Event IDs to names
     */
    private function resolveEventId($id){
        try {
            return DB::table('events')->where('id', $id)->get()[0]->name;
        } catch (Exception $e){
            return null;
        }
    }
    /**
     * Get the event details
     */
    private function resolveEventDetails($id){
        try {
            return DB::table('events')->where('id', $id)->get()[0];
        } catch (Exception $e){
            return null;
        }
    }
    /**
     * Converts Status IDs to names
     */
    private $status = array("Pending", "Rejected", "Approved", "Cancelled", "Attending", "Attended");
    private function resolveStatusId($id){
        if ($id >= 0 && $id < count($this->status)) return $this->status[$id];
    }

    private function getTicketNumber(Request $request){
        if (Session::get('ticket_number') != null && Session::get('ticket_number') != '') return Session::get('ticket_number');
        else return $request->input('ticketnumber');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = new Request;
        $ticketnumber = Session::get('ticket_number', null);
        $nim = Session::get('nim', null);
        if ($ticketnumber != null && $ticketnumber != '' && $nim != null && $nim != '') return $this->store($request);
        else  return view("dashboard.login", ['error' => null, 'action' => '/login', 'message' => "Check your registration status and e-ticket"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("dashboard.login", ['error' => null, 'action' => '/login', 'message' => "Check your registration status and e-ticket"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request1)
    {
        $ticketnumber = $this->getTicketNumber($request1);
        $nim = Session::get('nim', $request1->input('nim'));

        $request = DB::table("tickets")->where('id', $ticketnumber)->get();

        if (count($request) == 0 || $request[0]->nim != $nim){
            return view("dashboard.login", ['error' => 'Wrong Ticket Number or NIM', 'action' => '/login', 'message' => "Check your registration status and e-ticket"]);
        };

        foreach($request1->all() as $key => $value) {
            switch ($key){
                case "action-change-email":
                    if ($value != '') DB::table('tickets')->where('id', $ticketnumber)->update(['email' => $value]);
                break;
                case "action-change-phone":
                    if ($value != '') DB::table('tickets')->where('id', $ticketnumber)->update(['phone' => $value]);
                break;
                case "action-change-line":
                    if ($value != '') DB::table('tickets')->where('id', $ticketnumber)->update(['line' => $value]);
                break;
                case "action-change-whatsapp":
                    if ($value != '') DB::table('tickets')->where('id', $ticketnumber)->update(['whatsapp' => $value]);
                break;
            }
        };

        $events = DB::table('registration')->where('ticket_id', $ticketnumber)->get();
        $name = $request[0]->name;

        $account_details = [
            "name" => $name,
            "nim" => $nim,
            "ticketnumber" => $ticketnumber,
            "binusian" => ($request[0]->binusian == 1) ? true : false,
            "contact" => [
                "email" => $request[0]->email,
                "phone" => $request[0]->phone,
                "line" => $request[0]->line,
                "whatsapp" => $request[0]->whatsapp
            ]
        ];

        for ($i = 0; $i < count($events); $i++){
            $event_details = $this->resolveEventDetails($events[$i]->event_id);
            $events[$i]->event_id = $event_details->id;
            $events[$i]->event_name = $event_details->name;
            $events[$i]->attendance_opened = ($event_details->attendance_opened == 1) ? true : false;
            $events[$i]->attendance_is_exit = ($event_details->attendance_is_exit == 1) ? true : false;
            $events[$i]->status_code = $events[$i]->status;
            $events[$i]->status = $this->resolveStatusId($events[$i]->status);
            $events[$i]->url_link = $event_details->url_link;
            $events[$i]->totp_key = $event_details->totp_key;
        };

        // Set Session Cookie
        Session::put('name', $name);
        Session::put('nim', $nim);
        Session::put('ticket_number', $ticketnumber);

        return view("dashboard.tickets", ['error' => null, 'account_details' => $account_details, 'events' => $events]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect('/login');
    }
}
