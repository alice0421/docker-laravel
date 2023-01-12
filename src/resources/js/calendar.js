// import '@fullcalendar/core/vdom'; // (for vite) ver.6で不要になった（エラー発生）？
import MicroModal from 'micromodal';
import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';

// 日付を-1してYYYY-MM-DDの書式で返すメソッド
function formatDate(date, pos) {
    var dt = new Date(date);
    if(pos==="end"){
        dt.setDate(dt.getDate() - 1);
    }
    return dt.getFullYear() + '-' +('0' + (dt.getMonth()+1)).slice(-2)+ '-' +  ('0' + dt.getDate()).slice(-2);
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
                document.getElementById("id").value = "";
                document.getElementById("event_title").value = "";
                document.getElementById("start_date").value = "";
                document.getElementById("end_date").value = "";
                document.getElementById("event_body").value = "";
                document.getElementById("event_color").value = "blue";
                MicroModal.show('modal-1');
            }
        }
    },
    headerToolbar: { // ヘッダーの設定（コンマで区切ると間が空かない、半角スペースで区切ると間が空く）
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "eventAddButton dayGridMonth,timeGridWeek", // ヘッダー右（event新規追加、月形式、時間形式）
    },
    height: "auto",

    // event登録(interactionPlugin)
    // selectable: true, // selectを可能にする
    select: function (info) { // selectした後に行う処理を記述
        // 入力ダイアログ
        const eventName = prompt("タイトルを入力");

        // eventの追加（タイトルが空なら追加しない）
        if (eventName) {
            // axiosでevent登録処理
            axios.post("/calendar/create", {
                    // 送信する値
                    event_title: eventName,
                    event_body: "body",
                    start_date: info.start.valueOf(), // プリミティブな値にする（JavaScriptのDateオブジェクト(info.start)をプリミティブにしてD変更されないよう固定）
                    end_date: info.end.valueOf(),
                    event_color: 'green',
                    event_border_color: 'green',
                })
                .then((response) => {
                    // event表示
                    calendar.addEvent({
                        title: eventName, // eventタイトル
                        description: "body", // event内容
                        start: info.start, // event開始日
                        end: info.end, // event終了日
                        allDay: true, //　常に終日
                        backgroundColor: 'green',
                        borderColor: 'green',
                    });
                })
                .catch((error) => {
                    // バリデーションエラーなど
                    alert("登録に失敗しました\nerror: ", error);
                });
        }
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
        document.getElementById("id").value = info.event.id;
        document.getElementById("event_title").value = info.event.title;
        document.getElementById("start_date").value = formatDate(info.event.start);
        document.getElementById("end_date").value = formatDate(info.event.end, "end");
        document.getElementById("event_body").value = info.event.extendedProps.description;
        document.getElementById("event_color").value = info.event.backgroundColor;
        MicroModal.show('modal-1');
    },
});

// カレンダーのレンダリング
calendar.render();