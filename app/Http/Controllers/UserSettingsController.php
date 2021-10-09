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
        if (!$request->has('email') || !$request->has('eventId')) return response()->json(['error' => 'Incomplete Request']);
        if ($request->input('email') == Auth::user()->email){
            if ($request->input('allowSelf') == false) return response()->json(['error' => 'You should not add yourself as a member']);
            $user_data = Auth::user();
        } else {
            // Search on database
            $user_data = DB::table('users')->where('email', $request->input('email'))->first();
            if (!$user_data) return response()->json(['error' => 'User not found']);
        }
        if (!DB::table('events')->where('id', $request->input('eventId'))->first()) return response()->json(['error' => 'Event not found']);

        // Check user_properties
        $event_permissions = DB::table('event_permissions')->join('fields', 'event_permissions.field_id', 'fields.id')->where('event_id', $request->input('eventId'))->get();
        $incomplete = []; $invalid = [];

        $response = [
            "name" => $user_data->name,
            "incomplete" => $incomplete,
            "invalid" => $invalid
        ];

        if (!Auth::guest() && (Auth::user()->university_id == 2 || Auth::user()->university_id == 3)) $response['eventPermissions'] = [];

        foreach ($event_permissions as $permission){
            $user_properties = DB::table('user_properties')->where('user_id', $user_data->id)->where('field_id', $permission->field_id)->first();
            if (!$user_properties){
                array_push($incomplete, $permission->name . ' (' . $permission->validation_description . ')');
                return;
            }
            if (!Auth::guest() && (Auth::user()->university_id == 2 || Auth::user()->university_id == 3)) $response['eventPermissions'][] = [
                'name' => $permission->name,
                'current_value' => $user_properties->value
            ];
            if (strlen($permission->validation_rule) > 0 && !preg_match($permission->validation_rule, $user_properties->value)) array_push($invalid, $permission->name . ' (' . $permission->validation_description . ')');
        }

        return response()->json($response);
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
    public function registerToEvent(Request $request){
        if (!Auth::check()) return redirect("/home");
        $redirect_to = '/home';
        if ($request->has('redirect_to')) $redirect_to = $request->input('redirect_to');
        // Get event ID
        $event_id = $request->input("event_id");
        $team_required = false;

        // Get the slot number
        $slots = ($request->has("slots") && $request->input("slots") > 1) ? $request->input("slots") : 1;

        // Set the Payment Code
        $payment_code = null;

        // Check on database whether the event exists
        $event = DB::table("events")->where("id", $event_id)->first();
        $currentTickets = DB::table('registration')->selectRaw('count(*) as total')->where('event_id', $event->id)->where('status', '!=', 1)->first();

        if (!$event){
            $request->session()->put('error', "Event not found.");
            return redirect($redirect_to);
        } else if ($currentTickets->total >= $event->seats) {
            $request->session()->put('error', "Unable to register to " . $event->name . " due to full capacity.");
            return redirect($redirect_to);
        } else if ($event->opened == 0) {
            $request->session()->put('error', "The registration period for " . $event->name . " has been closed.");
            return redirect($redirect_to);
        } else if ($event->team_members + $event->team_members_reserve > 0) $team_required = true;

        if ($event->price > 0) $payment_code = uniqid();

        // Create an array of users to be validated
        $leader = Auth::user();
        $members = [];
        $reserve = [];

        // Create an email draft
        $event_title = (strlen($event->kicker) > 0 ? ($event->kicker . ': ') : '') . $event->name;
        $email_template = [
            'message_type' => 'MARKDOWN',
            'sender_name' => 'COMPUTERUN 2.0 - ' . (strlen($event->kicker) > 0 ? $event->kicker : $event->name),
            'created_at' => date("Y-m-d H:i:s")
        ];

        // Get whether teams are needed
        if ($team_required == true){
            if (!$request->has("create_team") || !$request->has("team_name") || $request->input("team_name") == ""){
                $request->session()->put('error', "You will need to create a team for " . $event->name . ".");
                return redirect($redirect_to);
            }

            // Team members
            for ($i = 1; $i <= $event->team_members; $i++){
                if (!$request->has('team_member_' . $i)){
                    $request->session()->put('error', "Incomplete team members");
                    return redirect($redirect_to);
                }
                $members[] = DB::table('users')->where('email', $request->input('team_member_' . $i))->first();
            }

            // Reserve members
            for ($i = 1; $i <= $event->team_members_reserve; $i++){
                if ($request->has('team_member_reserve_' . $i)){
                    $reserve[] = DB::table('users')->where('email', $request->input('team_member_reserve_' . $i))->first();
                }
            }
        }

        // Validate users
        $queue = [$leader];
        $queue = array_merge($queue, $members, $reserve);
        $event_permissions = DB::table('event_permissions')->where('event_id', $event->id)->get();

        $validation_failed = 0;

        foreach ($queue as $user){
            $user_properties = DB::table('user_properties')->where('user_id', $user->id)->get();
            $registrations = DB::table('registration')->where('event_id', $event->id)->where('ticket_id', $user->id)->where('status', '!=', 1)->get();
            $validation = parent::validateFields($event_permissions, $user_properties);
            if ($event->slots - count($registrations) <= 0) $validation->eligible_to_register = false;

            if (!$validation->eligible_to_register){
                $validation_failed++;
            }
        }

        if ($validation_failed > 0){
            $request->session()->put('error', "You or your team members are not eligible to register to this event.");
            return redirect($redirect_to);
        }

        if ($team_required == true){
            // Create a new team
            $team_id = DB::table("teams")->insertGetId(["name" => $request->input("team_name"), "event_id" => $event_id]);

            // Assign the database template
            $query = [];
            $draft = ["event_id" => $event_id, "status" => (($event->auto_accept == true) ? 2 : 0), "payment_code" => $payment_code, "team_id" => $team_id, "ticket_id" => null, "remarks" => null];

            // Assign the User ID of the team leader
            $tempdetails = Auth::user();
            for ($j = 0; $j < $slots; $j++){
                $temp = $draft;
                $temp["ticket_id"] = $tempdetails->id;
                $temp["remarks"] = "Team Leader";
                if ($slots > 1) $temp["remarks"] = $temp["remarks"] . ", Slot " . ($j + 1);
                array_push($query, $temp);
            }

            // Find the User ID of team members
            for ($i = 1; $i <= $event->team_members; $i++){
                $tempdetails = DB::table("users")->where("email", $request->input("team_member_" . $i))->first();
                for ($j = 0; $j < $slots; $j++){
                    $temp = $draft;
                    echo(print_r($tempdetails));
                    $temp["ticket_id"] = $tempdetails->id;
                    $temp["remarks"] = "Team Member";
                    if ($slots > 1) $temp["remarks"] = $temp["remarks"] . ", Slot " . ($j + 1);
                    array_push($query, $temp);
                }
                // Send Email
                // Mail::to($request->input("team_member_" . ($i + 1)))->send(new SendNewTeamNotification(["name" => $tempdetails["name"], "team_name" => $request->input("team_name"), "team_id" => $team_id, "team_leader_name" => Auth::user()->name, "team_leader_email" => Auth::user()->email, "role" => "Main Player/Member " . ($i + 1), "event_name" => $event->name, "event_kicker" => $event->kicker]));
                $email_draft = $email_template;
                $email_draft['subject'] = 'You have been invited to join ' . $event_title . ' by ' . $leader->name;
                $email_draft['message'] = 'You have been invited by ' . $leader->name . ' (' . $leader->email . ') to join as a member of "' . $request->input("team_name") . '" to join ' . $event_title . PHP_EOL . PHP_EOL . 'Your team and ticket details can be found on [https://computerun.id/profile/](https://computerun.id/profile/).' . PHP_EOL . PHP_EOL . 'If you are being added by mistake, please contact the respective event committees.';
                if ($event->price == 0 && $event->auto_accept == true && strlen($event->description_private) > 0) $email_template['message'] .= PHP_EOL . PHP_EOL . '## Important Information for Event/Attendance' . PHP_EOL . PHP_EOL . $event->description_private;
                else if (strlen($event->description_pending) > 0) $email_template['message'] .= PHP_EOL . PHP_EOL . '## Important Information for Event/Attendance' . PHP_EOL . PHP_EOL . $event->description_pending;
                $email_draft['email'] = $tempdetails->email;
                DB::table('email_queue')->insert($email_draft);
            }

            // Find the User ID of reseve team members
            for ($i = 1; $i <= $event->team_members_reserve; $i++){
                if (!$request->has("team_member_reserve_" . $i) || $request->input("team_member_reserve_" . $i) == "") continue;
                $tempdetails = DB::table("users")->where("email", $request->input("team_member_reserve_" . $i))->first();
                for ($j = 0; $j < $slots; $j++){
                    $temp = $draft;
                    echo(print_r($tempdetails));
                    $temp["ticket_id"] = $tempdetails->id;
                    $temp["remarks"] = "Reserve Team Member";
                    if ($slots > 1) $temp["remarks"] = $temp["remarks"] . ", Slot " . ($j + 1);
                    array_push($query, $temp);
                }
                // Send Email
                // Mail::to($request->input("team_member_reserve_" . ($i + 1)))->send(new SendNewTeamNotification(["name" => $tempdetails->name, "team_name" => $request->input("team_name"), "team_id" => $team_id, "team_leader_name" => Auth::user()->name, "team_leader_email" => Auth::user()->email, "role" => "Reserve Player/Member " . ($i + 1), "event_name" => $event->name, "event_kicker" => $event->kicker]));
                $email_draft = $email_template;
                $email_draft['subject'] = 'You have been invited to join ' . $event_title . ' by ' . $leader->name;
                $email_draft['message'] = 'You have been invited by ' . $leader->name . ' (' . $leader->email . ') to join as a reserve member of "' . $request->input("team_name") . '" to join ' . $event_title . PHP_EOL . PHP_EOL . 'Your team and ticket details can be found on [https://computerun.id/profile/](https://computerun.id/profile/).' . PHP_EOL . PHP_EOL . 'If you are being added by mistake, please contact the respective event committees.';
                if ($event->price == 0 && $event->auto_accept == true && strlen($event->description_private) > 0) $email_template['message'] .= PHP_EOL . PHP_EOL . '## Important Information for Event/Attendance' . PHP_EOL . PHP_EOL . $event->description_private;
                else if (strlen($event->description_pending) > 0) $email_template['message'] .= PHP_EOL . PHP_EOL . '## Important Information for Event/Attendance' . PHP_EOL . PHP_EOL . $event->description_pending;
                $email_draft['email'] = $tempdetails->email;
                DB::table('email_queue')->insert($email_draft);
            }
            var_dump($query);
            // Insert into the database
            DB::table("registration")->insert($query);
        } else {
            // Assign the participant
            DB::table("registration")->insert(["ticket_id" => Auth::user()->id, "event_id" => $event_id, "status" => (($event->auto_accept == true) ? 2 : 0), "payment_code" => $payment_code]);
        }

        // Send Email for Payment
        // if($event->price > 0) Mail::to(Auth::user()->email)->send(SendInvoice::createEmail((object) ["name" => Auth::user()->name, "event_id" => $event->id, "user_id" => Auth::user()->id, "event_name" => $event->name, "payment_code" => $payment_code, "total_price" => $event->price * $slots]));

        // Send Email for Payment
        $email_template['subject'] = 'Welcome to ' . $event_title . '!';
        $email_template['message'] = 'Thank you for registering to ' . $event_title . '.';
        $email_template['email'] = $leader->email;

        if ($event->price == 0 && $event->auto_accept == true){
            $email_template['message'] .= ' Your registration has been approved by our team.' . PHP_EOL . PHP_EOL . 'Your ticket and team (if any) details can be found on https://computerun.id/profile/.' . PHP_EOL . PHP_EOL . 'If you are being registered by mistake, please contact the respective event committees.';
            if (strlen($event->description_private) > 0) $email_template['message'] .= PHP_EOL . PHP_EOL . '## Important Information for Event/Attendance' . PHP_EOL . PHP_EOL . $event->description_private;
        } else {
            $email_template['message'] .= ' Please finish your payment (if any) and wait while our team verifies and approves your registration.' . PHP_EOL . PHP_EOL . 'You may check your ticket status regularly on https://computerun.id/profile/.' . PHP_EOL . PHP_EOL . 'If you are being registered by mistake, please contact the respective event committees.';
            if (strlen($event->description_pending) > 0) $email_template['message'] .= PHP_EOL . PHP_EOL . '## Important Information for Event/Attendance' . PHP_EOL . PHP_EOL . $event->description_pending;
        }

        DB::table('email_queue')->insert($email_template);

        // Return Response
        if ($event->price > 0){
            if (strlen($event->payment_link) > 0) return redirect($this->getPaymentLink($event, (object) ['payment_code' => $payment_code]));
            else return redirect('/pay/' . $payment_code);
        }

        return redirect($redirect_to);
    }
    // Module to register to certain events
    public function registerEvent(Request $request){
        if (!Auth::check()) return redirect("/home");

        $redirect_to = '/home';
        if ($request->has('redirect_to')) $redirect_to = $request->input('redirect_to');

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
            return redirect($redirect_to);
        } else if ($event->opened == 0) {
            $request->session()->put('error', "The registration period for " . $event->name . " has been closed.");
            return redirect($redirect_to);
        } else if ($event->team_members + $event->team_members_reserve > 0) $team_required = true;

        // Get whether teams are needed
        if ($team_required == true){
            if (!$request->has("create_team") || !$request->has("team_name") || $request->input("team_name") == ""){
                $request->session()->put('error', "You will need to create a team for " . $event->name . ".");
                return redirect($redirect_to);
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
                $temp["ticket_id"] = $tempdetails->id;
                $temp["remarks"] = "Team Leader";
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
        return redirect($redirect_to);
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
            if (str_starts_with($key, 'action-change-')){
                $field_id = substr($key, 14);
                $res = DB::table('fields')->select('id', DB::raw('replace(id, \'.\', \'_\') as id_mod'))->where('editable', true)->having('id_mod', $field_id)->first();
                if ($res !== null && strlen($value) > 0) DB::table('user_properties')->upsert(['user_id' => Auth::user()->id, 'field_id' => $res->id, 'value' => $value], ['user_id', 'field_id']);
            }
        };

        // Save "updated_at"
        $draft["updated_at"] = now();

        DB::table('users')->where('id', $userid)->update($draft);
        $request->session()->put('status', "Your Account Settings has been updated.");
        if ($request->has('redirect_to')) return redirect($request->input('redirect_to'));
        return redirect($redirect_to);
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
