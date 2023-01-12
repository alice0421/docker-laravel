// import '@fullcalendar/core/vdom'; // (for vite) ver.6で不要になった（エラー発生）？
import { Calendar } from "@fullcalendar/core";
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';

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

    // イベント登録(interactionPlugin)
    selectable: true, // selectを可能にする
    select: function (info) { // selectした後に行う処理を記述
        // 入力ダイアログ
        const eventName = prompt("イベントを入力してください");

        // イベントの追加（タイトルが空なら追加しない）
        if (eventName) {
            calendar.addEvent({
                // 登録するevent要素
                title: eventName, // カレンダーに見えるタイトル
                start: info.start, // eventの始まり
                end: info.end, // eventの終わり
                allDay: true, // 常に終日
            });
        }
    },
});

// カレンダーのレンダリング
calendar.render();