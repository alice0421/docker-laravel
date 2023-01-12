// import '@fullcalendar/core/vdom'; // ver6でなくなったぽい？
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";

// カレンダーを表示させたいタグのidを取得
var calendarEl = document.getElementById("calendar");

// new Calender(カレンダーを表示させたいタグのid, {各種カレンダーの設定});
let calendar = new Calendar(calendarEl, {
    // 各種カレンダーの設定をここに列挙
    plugins: [dayGridPlugin], // プラグインの導入
    initialView: "dayGridMonth", // 最初に表示させるページの形式
    headerToolbar: { // ヘッダーの設定（コンマで区切ると間が空かない、半角スペースで区切ると間が空く）
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "prevYear,nextYear", // ヘッダー右（前年、来年）
    },
});

// カレンダーのレンダリング
calendar.render();