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
            'event_color' => 'required',
            'event_border_color' => 'required',
        ]);

        // 登録処理
        $event->event_name = $request->input('event_name');
        $event->start_date = date('Y-m-d', $request->input('start_date') / 1000); // 日付変換（JSのタイムスタンプはミリ秒なので秒に変換）
        $event->end_date = date('Y-m-d', $request->input('end_date') / 1000);
        $event->event_color = $request->input('event_color');
        $event->event_border_color = $request->input('event_border_color');
        $event->save();

        return;
    }

    // event取得
    public function get(Request $request, Event $event){
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // 現在カレンダーが表示している日付の期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000);
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);

        // 表示処理
        return $event->query()
            // DBから取得する際にFullCalendarの形式にカラム名を変更する
            ->select(
                'event_name as title',
                'start_date as start',
                'end_date as end',
                'event_color as backgroundColor',
                'event_border_color as borderColor'
            )
            // 表示されているカレンダーのeventのみをDBから検索して表示
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date) // AND条件
            ->get();
    }
}
