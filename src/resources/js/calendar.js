//import '@fullcalendar/core/vdom'; // (for vite) ver.6で不要になった（エラー発生）？
import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';

window.is_first_clicked_allday = true;

// 日付を-1してYYYY-MM-DDの書式で返すメソッド
function formatDate(date, pos) {
    var dt = new Date(date);
    if(pos === "end_allday"){ // 終日のときだけ終了日を1日減らす
        dt.setDate(dt.getDate() - 1);
    }
    return dt.getFullYear() + '-' + (dt.getMonth() + 1).toString().padStart(2, '0') + '-' +  dt.getDate().toString().padStart(2, '0');
}

// 時間の調整メソッド
function formatTime(date) {
    var dt = new Date(date);
    return dt.getHours().toString().padStart(2, '0') + ':' + dt.getMinutes().toString().padStart(2, '0') + ':00';
}

// カレンダーを表示させたいタグのidを取得
var calendarEl = document.getElementById("calendar");

// new Calender(カレンダーを表示させたいタグのid, {各種カレンダーの設定});
let calendar = new Calendar(calendarEl, {
    // プラグインの導入(import忘れずに)
    plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin],

    // カレンダー表示(dayGridPlugin)
    initialView: "dayGridMonth", // 最初に表示させるページの形式
    customButtons: { // カスタムボタン
        eventAddButton: { // event新規追加ボタン
            text: '予定を追加',
            click: function() {
                // 初期化
                const now = new Date();
                window.is_first_clicked_allday = true;
                document.getElementById("new-id").value = "";
                document.getElementById("new-event_title").value = "";
                document.getElementById("new-start_date").value = formatDate(now);
                document.getElementById("new-end_date").value = formatDate(now);
                document.getElementById('new-event_time').style.display = 'none';
                document.getElementById("new-start_time").value = "";
                document.getElementById("new-end_time").value = "";
                document.getElementById("new-is_allday").checked = true;
                document.getElementById("new-event_body").value = "";
                document.getElementById("new-event_color").value = "blue";

                //　モーダルを開く
                document.getElementById('modal-add').style.display = 'flex';
            }
        }
    },
    headerToolbar: { // ヘッダーの設定（コンマで区切ると間が空かない、半角スペースで区切ると間が空く）
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "eventAddButton dayGridMonth,timeGridWeek", // ヘッダー右（event新規追加、月形式、時間形式）
    },
    height: "auto", // 高さをウィンドウサイズに揃える

    // カレンダーで日程を指定してevent登録(interactionPlugin)
    selectable: true, // selectを可能にする
    select: function (info) { // selectした後に行う処理を記述
        // 選択した日程を反映（のこりは初期化）
        window.is_first_clicked_allday = true;
        document.getElementById("new-id").value = "";
        document.getElementById("new-event_title").value = "";
        document.getElementById("new-start_date").value = formatDate(info.start);
        if(info.allDay){
            document.getElementById("new-end_date").value = formatDate(info.end, "end_allday");
            document.getElementById('new-event_time').style.display = 'none';
            document.getElementById("new-start_time").value = "";
            document.getElementById("new-end_time").value = "";
            document.getElementById("new-is_allday").checked = true;
        }else{
            document.getElementById("new-end_date").value = formatDate(info.end);
            document.getElementById('new-event_time').style.display = 'block';
            document.getElementById("new-start_time").value = formatTime(info.start);
            document.getElementById("new-end_time").value = formatTime(info.end);
            document.getElementById("new-is_allday").checked = false;
        }
        document.getElementById("new-event_body").value = "";
        document.getElementById("new-event_color").value = "blue";

        //　モーダルを開く
        document.getElementById('modal-add').style.display = 'flex';
    },

    // DBに登録したevent表示
    events: function (info, successCallback, failureCallback) { // eventsは表示カレンダー表示が切り替わるたびに実行される
        // Laravelのevent取得処理（get）の呼び出し
        axios
            .post("/calendar/get", {
                // 現在カレンダーが表示している日付の期間
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // 既に表示されているイベントを削除（重複防止）
                calendar.removeAllEvents(); // ver.6でもどうやら使える（ドキュメントにはない）
                // カレンダーに読み込み
                successCallback(response.data); // successCallbackにeventをオブジェクト型で入れるとカレンダーに表示できる
            })
            .catch((error) => {
                // バリデーションエラーなど
                alert("登録に失敗しました\nerror: ", error);
            });
    },

    // event詳細のポップアップ
    eventClick: function(info) {
        window.is_first_clicked_allday = true;
        document.getElementById("id").value = info.event.id;
        document.getElementById("delete-id").value = info.event.id;
        document.getElementById("event_title").value = info.event.title;
        document.getElementById("start_date").value = formatDate(info.event.start);
        if(info.event.allDay){
            document.getElementById("end_date").value = formatDate(info.event.end, "end_allday");
            document.getElementById("start_time").value = "";
            document.getElementById("end_time").value = "";
            document.getElementById('event_time').style.display = 'none';
        }else{
            document.getElementById("end_date").value = formatDate(info.event.end);
            document.getElementById("start_time").value = formatTime(info.event.start);
            document.getElementById("end_time").value = formatTime(info.event.end);
            document.getElementById('event_time').style.display = 'block';
        }
        document.getElementById("is_allday").checked = info.event.allDay;
        document.getElementById("event_body").value = info.event.extendedProps.description;
        document.getElementById("event_color").value = info.event.backgroundColor;

        //　モーダルを開く
        document.getElementById('modal-update').style.display = 'flex';
    },
});

// カレンダーのレンダリング
calendar.render();
