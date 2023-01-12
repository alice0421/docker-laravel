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
            'event_title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'event_color' => 'required',
        ]);

        // 登録処理
        $event->event_title = $request->input('event_title');
        $event->event_body = $request->input('event_body');
        $event->start_date = $request->input('start_date');
        $event->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day"));
        $event->event_color = $request->input('event_color');
        $event->event_border_color = $request->input('event_color');
        $event->save();

        return redirect(route("show"));
    }

    // event取得
    public function get(Request $request, Event $event){
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // 現在カレンダーが表示している日付の期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000); // 日付変換（JSのタイムスタンプはミリ秒なので秒に変換）
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);

        // 表示処理
        return $event->query()
            // DBから取得する際にFullCalendarの形式にカラム名を変更する
            ->select(
                'event_title as title',
                'event_body as description',
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
