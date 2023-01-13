//eventの削除（jsのほうがなぜか反応しないので直書き）
function deleteEvent(){
    'use strict'

    if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
        document.getElementById('delete-form').submit();
    }
}