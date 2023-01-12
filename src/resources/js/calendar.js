// import '@fullcalendar/core/vdom'; // (for vite) ver.6で不要になった（エラー発生）？
import MicroModal from 'micromodal';
import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';

// 日付をYYYY-MM-DDの書式で返すメソッド
function formatDate(date) {
    var y = date.getFullYear();
    var m = ('00' + (date.getMonth()+1)).slice(-2);
    var d = ('00' + date.getDate()).slice(-2);
    return (y + '-' + m + '-' + d);
}

// カレンダーを表示させたいタグのidを取得
var calendarEl = document.getElementById("calendar");

// new Calender(カレンダーを表示させたいタグのid, {各種カレンダーの設定});
let calendar = new Calendar(calendarEl, {
    // プラグインの導入(import忘れずに)
    plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin],

    // カレンダー表示(dayGridPlugin)
    initialView: "dayGridMonth", // 最初に表示させるページの形式
    headerToolbar: { // ヘッダーの設定（コンマで区切ると間が空かない、半角スペースで区切ると間が空く）
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "dayGridMonth,timeGridWeek", // ヘッダー右（月形式、時間形式）
    },
    height: "auto",

    // event登録(interactionPlugin)
    selectable: true, // selectを可能にする
    select: function (info) { // selectした後に行う処理を記述
        // 入力ダイアログ
        const eventName = prompt("イベントを入力してください");

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
        console.log(info.event);
        document.getElementById("id").value = info.event.id;
        document.getElementById("edit_title").value = info.event.title;
        document.getElementById("edit_start").value = formatDate(info.event.start);
        document.getElementById("edit_end").value = formatDate(info.event.end);
        document.getElementById("edit_body").value = info.event.extendedProps.description;
        MicroModal.show('modal-1');
    },
});

// カレンダーのレンダリング
calendar.render();