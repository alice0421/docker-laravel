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