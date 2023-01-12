<?php

namespace App\Http\Controllers;

use App\Models\Event; // Model追加忘れずに
use Illuminate\Http\Request;

class EventController extends Controller
{   
    // カレンダー表示
    public function show(){
        return view("calendars/calendar");
    }

    //　event追加
    public function create(Request $request, Event $event){
        // バリデーション
        $request->validate([
            'event_name' => 'required|max:32', // 最大32文字
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
        ]);

        // 登録処理
        $event->event_name = $request->input('event_name');
        $event->start_date = date('Y-m-d', $request->input('start_date') / 1000); // 日付変換（JavaScriptのタイムスタンプはミリ秒なので秒に変換）
        $event->end_date = date('Y-m-d', $request->input('end_date') / 1000);
        $event->save();

        return;
    }
}
