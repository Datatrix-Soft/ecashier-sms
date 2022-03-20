<?php

namespace App\Http\Controllers;

use App\Models\SmsBalance;
use App\Models\SmsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    public function index(Request $request)
    {
        //validation rules
        $rules = [
            'user_id' => 'required',
            'subscription_id' => 'required',
            'app_secret_key' => 'required'
        ];

        //validation check
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $sms_details = SmsDetail::where('subscription_id', $request->input('subscription_id'))->where('sender_id', $request->input('user_id'))->get();

        if ($sms_details->isNotEmpty()) {
            $output = [
                'status' => true,
                'sms_details' => $sms_details->toArray(),
                'message' => 'Success!'
            ];
        } else {
            $output = [
                'status' => false,
                'sms_details' => null,
                'message' => 'No data found!'
            ];
        }

        return response()->json($output);

    }

    public function store(Request $request)
    {
        //validation rules
        $rules = [
            'app_secret_key' => 'required',
            'subscription_id' => 'required',
            'user_id' => 'required',
            'sender_phone' => 'required',
            'receiver_id' => 'required',
            'receiver_phone' => 'required',
            'content' => 'required',
        ];

        //validation check
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        try {
            DB::beginTransaction();
            //Send sms
            $sms_content = $request->input('content');
            $reciever_phone = $request->input('receiver_phone');
            Http::get('http://brandsms.mimsms.com/smsapi?api_key=C20060975e8b01b7ecb3a2.33368011&type=text&contacts='.$reciever_phone.'&senderid=8809601000100&msg='.$sms_content);

            $sms = new SmsDetail();
            $sms->subscription_id = $request->input('subscription_id');
            $sms->sender_id = $request->input('user_id');
            $sms->sender_phone = $request->input('sender_phone');
            $sms->receiver_id = $request->input('receiver_id');
            $sms->receiver_phone = $request->input('receiver_phone');
            $sms->receiver_type = $request->input('receiver_type');
            $sms->sms_type = $request->input('sms_type');
            $sms->content = $request->input('content');
            $sms->sms_count = $request->input('sms_count');
            $sms->is_sent = 1;
//            $sms->save();
            if($sms->save()){
                $balance= SmsBalance::where('subscription_id', $request->input('subscription_id'))->where('user_id', $request->input('user_id'))->first();
                $balance->balance = ($balance->balance-$request->input('sms_count')) > 0 ? $balance->balance-$request->input('sms_count') :0;
                $balance->save();
            }
            DB::commit();

            $sms_details = SmsDetail::where('subscription_id', $request->input('subscription_id'))->where('sender_id', $request->input('user_id'))->latest()->first();


            return response()->json([
                'status' => true,
                'message' => 'Sms sent successfully',
                'data' => $sms_details
            ]);

        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred.' .$e->getMessage().$e->getLine()
            ]);
        }
    }

    public function bulkStore(Request $request)
    {
        //validation rules
        $rules = [
            'app_secret_key' => 'required',
            'subscription_id' => 'required',
            'user_id' => 'required',
            'sender_phone' => 'required',
            'receiver_ids' => 'required',
            'receiver_phones' => 'required',
            'content' => 'required',
        ];

        //validation check
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        try {
            DB::beginTransaction();
            //Send sms
            $sms_content = $request->input('content');
            $receiver_phones = implode(" ",$request->input('receiver_phones'));

            Http::get('http://brandsms.mimsms.com/smsapi?api_key=C20060975e8b01b7ecb3a2.33368011&type=text&contacts='.$receiver_phones.'&senderid=8809601000100&msg='.$sms_content);
            foreach ($request->receiver_ids as $key=>$receiver){
                $sms = new SmsDetail();
                $sms->subscription_id = $request->input('subscription_id');
                $sms->sender_id = $request->input('user_id');
                $sms->sender_phone = $request->input('sender_phone');
                $sms->receiver_id = $receiver;
                $sms->receiver_phone = $request->input('receiver_phones')[$key];
                $sms->receiver_type = $request->input('receiver_type');
                $sms->sms_type = $request->input('sms_type');
                $sms->content = $request->input('content');
                $sms->sms_count = $request->input('sms_count');
                $sms->is_sent = 1;
            //            $sms->save();
                if($sms->save()){
                    $balance= SmsBalance::where('subscription_id', $request->input('subscription_id'))->where('user_id', $request->input('user_id'))->first();
                    $balance->balance = ($balance->balance-$request->input('sms_count')) > 0 ? $balance->balance-$request->input('sms_count') :0;
                    $balance->save();
                }
            }
            DB::commit();

//            $sms_details = SmsDetail::where('subscription_id', $request->input('subscription_id'))->where('sender_id', $request->input('user_id'))->latest()->first();


            return response()->json([
                'status' => true,
                'message' => 'Sms sent successfully',
//                'data' => $sms_details
            ]);

        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred.' .$e->getMessage().$e->getLine()
            ]);
        }
    }

    public function getBalance(Request $request)
    {
        //validation rules
        $rules = [
            'user_id' => 'required',
            'subscription_id' => 'required',
            'app_secret_key' => 'required'
        ];

        //validation check
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $sms_balance = SmsBalance::where('subscription_id', $request->input('subscription_id'))->where('user_id', $request->input('user_id'))->first();

        if ($sms_balance) {
            $output = [
                'status' => true,
                'sms_balance' => $sms_balance,
                'message' => 'Success!'
            ];
        } else {
            $output = [
                'status' => false,
                'sms_balance' => null,
                'message' => 'No data found!'
            ];
        }

        return response()->json($output);

    }
}
