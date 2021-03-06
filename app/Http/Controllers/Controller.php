<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function multiply() {
        $n = request('n');
        if (trim($n) == '' || !is_numeric($n)) {
            return response()->json([
                'status' => 400,
                'error' => 'Please specify n integer param'
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'result' => $n * $n
        ], 200);
    }

    public function blacklist() {
        $clientIp = Request::ip();
        $ipExists = $this->ipExists($clientIp);
        if (!$ipExists) {
            DB::insert(
                'insert into user_log(path, create_date, ip) VALUES(?, current_timestamp, ?)',
                [Request::fullUrl(), $clientIp]);
        }

        Mail::send('mail', ['ipAddress' => $clientIp], function($message) {
            $message->to(env('BLOCKED_IP_EMAIL'))->subject('Attention! IP address blocked!');
            $message->from(env('MAIL_FROM_ADDRESS'), 'WebApp');
        });

        return response()->json([
            'status' => 444,
            'error' => 'Access denied'
        ], 444);
    }

    private function ipExists($ip) {
        $exists = DB::select('select count(*) c from user_log where ip = ?', [$ip]);

        return count($exists) > 0 && $exists[0]->c > 0;
    }
}
