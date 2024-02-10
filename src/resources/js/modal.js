// viteでjsから関数をhtmlに読み込むときはwindows.〜と書く

// 現在の時間を15分単位で丸め込み
function setDefaultTime(dt) {
    dt.setMinutes(Math.round(dt.getMinutes() / 15) * 15);
    return dt.getHours().toString().padStart(2, '0') + ':' + dt.getMinutes().toString().padStart(2, '0') + ':00';
}

// モーダルを閉じる処理
window.closeAddModal = function(){
    document.getElementById("error_new-event_title").textContent = "";
    document.getElementById("error_new-event_date").textContent = "";
    document.getElementById('modal-add').style.display = 'none';
}
window.closeUpdateModal = function(){
    document.getElementById("error_event_title").textContent = "";
    document.getElementById("error_event_date").textContent = "";
    document.getElementById('modal-update').style.display = 'none';
}

// 削除処理
window.deleteEvent = function(){
    'use strict'

    if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
        document.getElementById('delete-form').submit();
    }
}

// 終日チェック管理
window.changeDateMode = function(){
    const is_allday = document.getElementById('new-is_allday').checked;

    if(is_allday){
        document.getElementById('new-event_time').style.display = 'none';
        window.is_first_clicked_allday = false;
    }else{
        document.getElementById('new-event_time').style.display = 'block';
        if(window.is_first_clicked_allday){ // 初めて終日のチェックを外したときは、現在時刻を表示する
            const now = new Date();
            document.getElementById("new-start_time").value = setDefaultTime(now);
            now.setMinutes(now.getMinutes() + 30);
            document.getElementById("new-end_time").value = setDefaultTime(now);
            window.is_first_clicked_allday = false;
        }
    }
}
window.changeUpdateDateMode = function(){
    console.log(window.is_first_clicked_allday);
    const is_allday = document.getElementById('is_allday').checked;

    if(is_allday){
        document.getElementById('event_time').style.display = 'none';
        window.is_first_clicked_allday = false;
    }else{
        document.getElementById('event_time').style.display = 'block';
        if(window.is_first_clicked_allday){ // 初めて終日のチェックを外したときは、現在時刻を表示する
            const now = new Date();
            document.getElementById("start_time").value = setDefaultTime(now);
            now.setMinutes(now.getMinutes() + 30);
            document.getElementById("end_time").value = setDefaultTime(now);
            window.is_first_clicked_allday = false;
        }
    }
}

// イベント新規追加処理
window.AddEvent = function(){
    let can_submit = true;

    const is_allDay = document.getElementById('new-is_allday').checked;
    const title = document.getElementById('new-event_title').value;
    let start_date = document.getElementById("new-start_date").value;
    let end_date = document.getElementById("new-end_date").value;
    if(is_allDay){
        start_date = new Date(start_date);
        end_date = new Date(end_date);
    }else{ // 終日でないならば日時で比較
        const start_time = document.getElementById("new-start_time").value;
        const end_time = document.getElementById("new-end_time").value;
        start_date = new Date(start_date + " " + start_time);
        end_date = new Date(end_date + " " + end_time);
    }

    // titleが空文字でないか
    if(!title){
        can_submit = false;
        document.getElementById("error_new-event_title").textContent = "※ タイトルを入力してください。";
    }else{
        document.getElementById("error_new-event_title").textContent = "";
    }
    // 開始時刻 < 終了時刻 かどうか
    if(start_date > end_date){
        can_submit = false;
        document.getElementById("error_new-event_date").textContent = "※ 開始日時は終了日時よりも前に設定してください。";
    }else{
        document.getElementById("error_new-event_date").textContent = "";
    }

    // 1つでもバリデーションに引っかかったらsubmitしない
    if(!can_submit) document.getElementById('add-form').onsubmit = () => { return false };
    else document.getElementById('add-form').onsubmit = () => { return true };
}

// イベント更新処理
window.updateEvent = function(){
    let can_submit = true;

    const is_allDay = document.getElementById('is_allday').checked;
    const title = document.getElementById('event_title').value;
    let start_date = document.getElementById("start_date").value;
    let end_date = document.getElementById("end_date").value;
    if(is_allDay){
        start_date = new Date(start_date);
        end_date = new Date(end_date);
    }
    if(!is_allDay){ // 終日でないならば日時で比較
        const start_time = document.getElementById("start_time").value;
        const end_time = document.getElementById("end_time").value;
        start_date = new Date(start_date + " " + start_time);
        end_date = new Date(end_date + " " + end_time);
    }

    // titleが空文字でないか
    if(!title){
        can_submit = false;
        document.getElementById("error_event_title").textContent = "※ タイトルを入力してください。";
    }else{
        document.getElementById("error_event_title").textContent = "";
    }
    // 開始時刻 < 終了時刻 かどうか
    if(start_date > end_date){
        can_submit = false;
        document.getElementById("error_event_date").textContent = "※ 開始日時は終了日時よりも前に設定してください。";
    }else{
        document.getElementById("error_event_date").textContent = "";
    }

    // 1つでもバリデーションに引っかかったらsubmitしない
    if(!can_submit) document.getElementById('update-form').onsubmit = () => { return false };
    else document.getElementById('update-form').onsubmit = () => { return true };
}
