<?php

namespace App\Http\Controllers;

use App\Models\Event; // Model追加忘れずに
use Illuminate\Http\Request;
use DateTime;

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
            'is_allday' => 'required',
            'event_color' => 'required',
        ]);

        // 登録処理
        $event->event_title = $request->input('event_title');
        $event->event_body = $request->input('event_body');
        $event->is_allday = $request->input('is_allday') == 'true';
        if($event->is_allday){ // 終日ならば日付のみ保存
            $event->start_date = $request->input('start_date');
            $event->end_date = $request->input('end_date');
            $event->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day"));
        }else{ // 終日でないならば日時を保存
            $event->start_date = new DateTime($request->input('start_date'). " " . $request->input('start_time'));
            $event->end_date = new DateTime($request->input('end_date'). " " . $request->input('end_time'));
            if($input->end_date == $input->start_date) $input->end_date->modify('+1 second'); // 同一時刻のバグ防ぎ
        }
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
                'id',
                'event_title as title',
                'event_body as description',
                'start_date as start',
                'end_date as end',
                'is_allday as allDay',
                'event_color as backgroundColor',
                'event_border_color as borderColor'
            )
            // 表示されているカレンダーのeventのみをDBから検索して表示
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date) // AND条件
            ->get();
    }

    // event更新
    public function update(Request $request, Event $event){
        $input = new Event();

        $input->event_title = $request->input('event_title');
        $input->event_body = $request->input('event_body');
        $input->is_allday = $request->input('is_allday') == 'true';
        if($input->is_allday){ // 終日ならば日付のみ保存
            $input->start_date = $request->input('start_date');
            $input->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day"));
        }else{ // 終日でないならば日時を保存
            $input->start_date = new DateTime($request->input('start_date'). " " . $request->input('start_time'));
            $input->end_date = new DateTime($request->input('end_date'). " " . $request->input('end_time'));
            if($input->end_date == $input->start_date) $input->end_date->modify('+1 second'); // 同一時刻のバグ防ぎ
        }
        $input->event_color = $request->input('event_color');
        $input->event_border_color = $request->input('event_color');

        // fill()の中身はArray型が必要だが、$inputのままではコレクションが返ってきてしまうため、Array型に変換
        $event->find($request->input('id'))->fill($input->attributesToArray())->save();

        return redirect(route("show"));
    }

    // event削除
    public function delete(Request $request, Event $event){
        $event->find($request->input('id'))->delete();

        return redirect(route("show"));
    }
}
