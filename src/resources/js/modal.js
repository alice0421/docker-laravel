// viteでjsから関数をhtmlに読み込むときはwindows.〜と書く
window.closeAddModal = function(){
    document.getElementById('modal-add').style.display = 'none';
}

window.closeUpdateModal = function(){
    document.getElementById('modal-update').style.display = 'none';
}

window.deleteEvent = function(){
    'use strict'

    if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
        document.getElementById('delete-form').submit();
    }
}

// 終日チェック管理
window.changeDateMode = function(){
    const is_allday = document.getElementById('new-date_mode').checked;

    if(is_allday){
        document.getElementById('new-event_time').style.display = 'none';
    }else{
        document.getElementById('new-event_time').style.display = 'block';
    }
}

// 終日チェック管理
window.changeUpdateDateMode = function(){
    const is_allday = document.getElementById('date_mode').checked;

    if(is_allday){
        document.getElementById('event_time').style.display = 'none';
    }else{
        document.getElementById('event_time').style.display = 'block';
    }
}
