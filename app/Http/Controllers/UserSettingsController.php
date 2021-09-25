<?php

namespace App\Http\Controllers;

use App\Mail\SendInvoice;
use App\Mail\SendNewTeamNotification;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Mail, Session as facadeSession};

class UserSettingsController extends Controller
{
    public function attendEvent($eventId, $eventToken){

    }

    // Module to get user details
    public function getUserDetails(Request $request){
        // Ensure that the user has logged in
        if (!Auth::check()) return response()->json(['error' => 'You are not authenticated']);
        // Ensure that the user has complete payload
        if (!$request->has('email')) return response()->json(['error' => 'Incomplete Request']);
        if ($request->input('email') == Auth::user()->email){
            if ($request->input('allowSelf') == false) return response()->json(['error' => 'You should not add yourself as a member']);
                $user_data = Auth::user();
            return response()->json([
                "name" => $user_data->name,
                "id_mobile_legends" => $user_data->id_mobile_legends,
                "id_pubg_mobile" => $user_data->id_pubg_mobile,
                "id_valorant" => $user_data->id_valorant
            ]);
        } else {
            // Search on database
            $user_data = DB::table('users')->where('email', $request->input('email'))->first();
            if (!$user_data) return response()->json(['error' => 'User not found']);
            return response()->json([
                "name" => $user_data->name,
                "id_mobile_legends" => $user_data->id_mobile_legends,
                "id_pubg_mobile" => $user_data->id_pubg_mobile,
                "id_valorant" => $user_data->id_valorant
            ]);
        }
    }

    // Module to generate competition page
    public function competitionIndex(Request $request, $teamid) {
        if(!Auth::check()){
            $request->session()->put('error','You will need to log in first');
            return redirect("/home");
        }

        $requests = DB::table("registration")
            ->join("events", "events.id", "=", "registration.event_id")
            ->join("users", "users.id", "=", "registration.ticket_id")
            ->join("teams","teams.id", "=", "registration.team_id")
            ->join("files","files.id", "=", "registration.file_id")
            ->where("team_id", $teamid)
            ->where("attendance_opened",1)
            ->select("registration.*", "files.*", DB::raw('events.name AS event_name'), DB::raw('users.name AS user_name'), DB::raw('teams.name AS team_name'), DB::raw('events.attendance_opened AS event_opened'))
            ->get();

//         dd($requests);
        if (count($requests) == 0){
            $request->session()->put('error', 'Team ID not found');
            return redirect("/home");
        }
//        dd($requests);
//        dd(count($requests));

        $currentId = Auth::user()->id;
//        dd($request[]);
        $isAuthorized = false;
        for ($i = 0; $i < count($requests); $i++){
            if ($requests[$i]->ticket_id == $currentId && $requests[$i]->status >= 2){
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized){
            $request->session()->put('error', 'Only the approved team can access');
            return redirect("/home");
        }

        return view("account.competition", ["requests" => $requests, "teamid" => $teamid]);
    }

    // Module to handle competition page
    public function competitionHandler(Request $request, $teamid) {
        if(!Auth::check()){
            $request->session()->put('error','You will need to log in first');
            return redirect("/home");
        }

        // Ensure that the payment code exists
        $requests = DB::table("registration")
            ->where("team_id", $teamid)
            ->first();

//        dd($requests);

        if ($requests == null){
            $request->session()->put('error', 'TeamID not found');
            return redirect("/home");
        }

        // Validate the inputs
        $request->validate([
            'file' => 'mimes:jpeg,png,pdf,zip'
        ]);

        // Save to storage and add to Database
        $path = $request->file('file')->store('competitions/answer');

        DB::table('files')->where('id',$requests->file_id)->update([
            "answer_path" => $path
        ]);

        $request->session()->put('status', 'Your files have been uploaded');

        return redirect('/home');
    }

    // Module to generate payment page
    public function paymentIndex(Request $request, $paymentcode){
        if (!Auth::check()){
            $request->session()->put('error', 'You will need to log in first');
            return redirect("/home");
        }

        // Ensure that the payment code exists
        $requests = DB::table("registration")
            ->where("payment_code", $paymentcode)
            ->join("events", "events.id", "=", "registration.event_id")
            ->join("users", "users.id", "=", "registration.ticket_id")
            ->select("registration.*", "events.price", DB::raw('events.name AS event_name'), DB::raw('users.name AS user_name'),"status","ticket_id")
            ->get();

        if (count($requests) == 0){
            $request->session()->put('error', 'Payment code not found');
            return redirect("/home");
        }

        // Check whether the user is one of the participants
        $currentId = Auth::user()->id;
        $isAuthorized = false;
        for ($i = 0; $i < count($requests); $i++){
            if ($requests[$i]->ticket_id == $currentId){
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized){
            $request->session()->put('error', 'Only the ticket holder can upload the registration document');
            return redirect("/home");
        }

        return view("account.payment", ["requests" => $requests, "paymentcode" => $paymentcode]);
    }

    // Module to upload payment receipts
    public function paymentHandler(Request $request, $paymentcode){
        if (!Auth::check()){
            $request->session()->put('error', 'You will need to log in first');
            return redirect("/home");
        }

        // Ensure that the payment code exists
        $requests = DB::table("registration")
            ->where("payment_code", $paymentcode)
            ->join("events", "events.id", "=", "registration.event_id")
            ->join("users", "users.id", "=", "registration.ticket_id")
            ->select("registration.*", "events.price", DB::raw('events.name AS event_name'), DB::raw('users.name AS user_name')) 
            ->get();

        if (count($requests) == 0){
            $request->session()->put('error', 'Payment code not found');
            return redirect("/home");
        }

        // Check whether the user is one of the participants
        $currentId = Auth::user()->id;
        $isAuthorized = false;
        for ($i = 0; $i < count($requests); $i++){
            if ($requests[$i]->ticket_id == $currentId){
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized){
            $request->session()->put('error', 'Invalid Payment Code');
            return redirect("/home");
        }

        // Validate the inputs
        $request->validate([
            'file' => 'mimes:jpeg,png,pdf,zip'
        ]);

        // Save to storage and add to Database
        $path = $request->file('file')->store('uploads');

        $fileId = DB::table('files')->insertGetId([
            "name" => $path
        ]);

        DB::table("registration")
            ->where("payment_code", $paymentcode)
            ->where("status", '<=',2)
            ->update(["file_id" => $fileId]);

        $request->session()->put('status', 'Your files have been uploaded');

        return redirect('/home');
    }

    // Module to register to certain events
    public function registerEvent(Request $request){
        if (!Auth::check()) return redirect("/home");

        // Get event ID
        $event_id = $request->input("event_id");
        $team_required = false;

        // Get the slot number
        $slots = ($request->has("slots") && $request->input("slots") > 1) ? $request->input("slots") : 1;

        // Set the Payment Code
        $payment_code = uniqid();

        // Check on database whether the event exists
        $event = DB::table("events")->where("id", $event_id)->first();
        if (!$event){
            $request->session()->put('error', "Event not found.");
            return redirect('/home');
        } else if ($event->opened == 0) {
            $request->session()->put('error', "The registration period for " . $event->name . " has been closed.");
            return redirect('/home');
        } else if ($event->team_members + $event->team_members_reserve > 0) $team_required = true;

        // Get whether teams are needed
        if ($team_required == true){
            if (!$request->has("create_team") || !$request->has("team_name") || $request->input("team_name") == ""){
                $request->session()->put('error', "You will need to create a team for " . $event->name . ".");
                return redirect('/home');
            }
            // Create a new team
            $team_id = DB::table("teams")->insertGetId(["name" => $request->input("team_name"), "event_id" => $event_id]);

            $requires_account = null;
            // Detect game-specific Account ID
            if (preg_match("/Mobile Legends/i", $event->name) == 1){
                $requires_account = "mobile_legends";
            } else if (preg_match("/PUBG Mobile/i", $event->name) == 1){
                $requires_account = "pubg_mobile";
            } else if (preg_match("/Valorant/i", $event->name) == 1){
                $requires_account = "valorant";
            }

            // Assign the database template
            $query = [];
            $draft = ["event_id" => $event_id, "status" => 0, "payment_code" => $payment_code, "team_id" => $team_id, "ticket_id" => null, "remarks" => null];

            // Assign the User ID of the team leader
            $tempdetails = json_decode(json_encode(Auth::user()), true);
            for ($j = 0; $j < $slots; $j++){
                $temp = $draft;
                $temp["ticket_id"] = $tempdetails["id"];
                if ($requires_account != null){
                    $temp["remarks"] = "Team Leader, ID: " . $tempdetails["id_" . $requires_account];
                } else {
                    $temp["remarks"] = "Team Leader";
                }
                if ($slots > 1) $temp["remarks"] = $temp["remarks"] . ", Slot " . ($j + 1);
                array_push($query, $temp);
            }

            // Find the User ID of team members
            for ($i = 0; $i < $event->team_members; $i++){
                $tempdetails = json_decode(json_encode(DB::table("users")->where("email", $request->input("team_member_" . ($i + 1)))->first()), true);
                for ($j = 0; $j < $slots; $j++){
                    $temp = $draft;
                    echo(print_r($tempdetails));
                    $temp["ticket_id"] = $tempdetails["id"];
                    if ($requires_account != null){
                        $temp["remarks"] = "Team Member, ID: " . $tempdetails["id_" . $requires_account];
                    } else {
                        $temp["remarks"] = "Team Member";
                    }
                    if ($slots > 1) $temp["remarks"] = $temp["remarks"] . ", Slot " . ($j + 1);
                    array_push($query, $temp);
                }
                // Send Email
                Mail::to($request->input("team_member_" . ($i + 1)))->send(new SendNewTeamNotification(["name" => $tempdetails["name"], "team_name" => $request->input("team_name"), "team_id" => $team_id, "team_leader_name" => Auth::user()->name, "team_leader_email" => Auth::user()->email, "role" => "Main Player " . ($i + 1), "event_name" => $event->name]));
            }

            // Find the User ID of reseve team members
            for ($i = 0; $i < $event->team_members_reserve; $i++){
                if (!$request->has("team_member_reserve_" . ($i + 1)) || $request->input("team_member_reserve_" . ($i + 1)) == "") continue;
                $tempdetails = json_decode(json_encode(DB::table("users")->where("email", $request->input("team_member_reserve_" .($i + 1)))->first()), true);
                for ($j = 0; $j < $slots; $j++){
                    if ($request->has("team_member_reserve_" . ($i + 1))){
                        $temp = $draft;
                        $temp["ticket_id"] = $tempdetails["id"];
                        if ($requires_account != null){
                            $temp["remarks"] = "Team Member (Reserve), ID: " . $tempdetails["id_" . $requires_account];
                        } else {
                            $temp["remarks"] = "Team Member (Reserve)";
                        }
                    }
                    if ($slots > 1) $temp["remarks"] = $temp["remarks"] . ", Slot " . ($j + 1);
                    array_push($query, $temp);
                }
                // Send Email
                Mail::to($request->input("team_member_reserve_" . ($i + 1)))->send(new SendNewTeamNotification(["name" => $tempdetails["name"], "team_name" => $request->input("team_name"), "team_id" => $team_id, "team_leader_name" => Auth::user()->name, "team_leader_email" => Auth::user()->email, "role" => "Reserve Player " . ($i + 1), "event_name" => $event->name]));
            }

            // Insert into the database
            DB::table("registration")->insert($query);
        } else {
            // Assign the participant
            DB::table("registration")->insert(["ticket_id" => Auth::user()->id, "event_id" => $event_id, "status" => 0, "payment_code" => $payment_code]);
        }

        // Send Email for Payment
        if($event->price > 0) Mail::to(Auth::user()->email)->send(new SendInvoice(["name" => Auth::user()->name, "event_name" => $event->name, "payment_code" => $payment_code, "total_price" => $event->price * $slots]));

        // Return Response
        $request->session()->put('status', "Your registration request has been sent. Please check your email for payment instructions.");
        return redirect('/home');
    }

    // Module to Unified Registration Flow
    public function registrationRedirectHandler(Request $request, $id){
        // Unregisterd Users -> Go to Login Page
        // Registered Users -> Go to Profile and trigger modal
        if($id > 0) $request->session()->put('register', $id);
        return redirect("/home");
    }

    // Module to update contacts
    public function updateContacts(Request $request){
        // Ensure that the user has logged in
        if (!Auth::check()) return redirect("/home");

        // Set the User ID
        $userid = Auth::user()->id;

        // Create Draft
        $draft = [];

        foreach($request->all() as $key => $value) {
            switch ($key){
                case "action-change-phone":
                    if ($value != '') DB::table('user_properties')->where('user_id', $userid)->where('field_id', 'contacts.phone')->update(['value' => $value]);
                break;
                case "action-change-line":
                    if ($value != '') DB::table('user_properties')->where('user_id', $userid)->where('field_id', 'contacts.line')->update(['value' => $value]);
                break;
                case "action-change-whatsapp":
                    if ($value != '') DB::table('user_properties')->where('user_id', $userid)->where('field_id', 'contacts.whatsapp')->update(['value' => $value]);
                break;
            }
        };

        // Save "updated_at"
        $draft["updated_at"] = now();

        DB::table('users')->where('id', $userid)->update($draft);
        $request->session()->put('status', "Your Account Settings has been updated.");
        return redirect('/home');
    }

    public function downloadFileCompetition($teamid){
        if(!Auth::check()){
            facadeSession::put('error', 'User: Please Login First');
            return redirect('login');
        }

        $check = DB::table("registration")
            ->join("events", "events.id", "=", "registration.event_id")
            ->where("team_id", $teamid)
            ->where("attendance_opened",1)
            ->first();

        if($check == null){
            facadeSession::put('error', 'User: Not Authorized or File not found');
            return redirect('home');
        }
        try {
            if($check->event_id == 1 && $check->attendance_opened == 1) return response()->download(storage_path("app/competitions/BCase.pdf"));
            else if($check->event_id == 2 && $check->attendance_opened == 1) return response()->download(storage_path("app/competitions/Moapps.pdf"));
        } catch (\Exception $e){
            facadeSession::put('error', 'Alert: Internal Server Error');
            return redirect('home');
        }
    }

    public function downloadFileUser($type ,$paymentcode ,$fileid){
        if(!Auth::check()){
            facadeSession::put('error', 'User: Please Login First');
            return redirect('login');
        }

        if($type == 0) $check = DB::table("registration")
            ->where("file_id", $fileid)
            ->where("ticket_id", Auth::user()->id)
            ->where("payment_code", $paymentcode)
            ->join("files","files.id","=","registration.file_id")
            ->first();
        elseif($type == 1) $check = DB::table("registration")
            ->where("file_id", $fileid)
            ->where("ticket_id", Auth::user()->id)
            ->where("team_id", $paymentcode)
            ->join("files","files.id","=","registration.file_id")
            ->first();

        if($check == null){
            facadeSession::put('error', 'User: Not Authorized or File ID not found');
            return redirect('home');
        }

        if ($type == 0) {
            try {
                return response()->download(storage_path("app/" . $check->name));
            } catch (\Exception $e){
                facadeSession::put('error', 'Alert: Internal Server Error');
                return redirect('home');
            }
        }else if ($type == 1){
            try {
                return response()->download(storage_path("app/" . $check->answer_path));
            } catch (\Exception $e){
                facadeSession::put('error', 'Alert: Internal Server Error');
                return redirect('home');
            }
        }
    }

}
