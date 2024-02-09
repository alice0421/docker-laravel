<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FullCalendar</title>
        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <!-- カレンダー表示 -->
        <div id='calendar'></div>

        <!-- カレンダー新規追加モーダル -->
        <div id="modal-add" class="modal">
            <div class="modal-contents">
                <form method="POST" action="{{ route('create') }}">
                    @csrf
                    <input id="new-id" type="hidden" name="id" value="" />
                    <label for="event_title">タイトル</label>
                    <input id="new-event_title" class="input-title" type="text" name="event_title" value="" />
                    <div id="new-event_date">
                        <label for="start_date">開始日程</label>
                        <input id="new-start_date" class="input-date" type="date" name="start_date" value="" />
                        <label for="end_date">終了日程</label>
                        <input id="new-end_date" class="input-date" type="date" name="end_date" value="" />
                    </div>
                    <div id="new-event_time" style="display: none;">
                        <label for="start_time">開始時間</label>
                        <input id="new-start_time" class="input-date" type="time" name="start_time" value="" />
                        <label for="end_time">終了時間</label>
                        <input id="new-end_time" class="input-date" type="time" name="end_time" value="" />
                    </div>
                    <label for="is_allday">
                        <input type="hidden" name="is_allday" value="false">
                        <input id="new-date_mode" class="input-date_mode" type="checkbox" name="is_allday" value="true" onclick="changeDateMode()">
                        終日
                    </label>
                    <label for="event_body" style="display: block">内容</label>
                    <textarea id="new-event_body" name="event_body" rows="3" value=""></textarea>
                    <label for="event_color">背景色</label>
                    <select id="new-event_color" name="event_color">
                        <option value="blue" selected>青</option>
                        <option value="green">緑</option>
                    </select>
                    <button type="button" onclick="closeAddModal()">キャンセル</button>
                    <button type="submit">決定</button>
                </form>
            </div>
        </div>

        <!-- カレンダー詳細表示モーダル（event更新） -->
        <!-- 別ページなら、$eventでの取得が容易にできる -->
        <div id="modal-update" class="modal">
            <div class="modal-contents">
                <form method="POST" action="{{ route('update') }}" >
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="id" name="id" value="" />
                    <label for="event_title">タイトル</label>
                    <input class="input-title" type="text" id="event_title" name="event_title" value="" />
                    <div id="event_date">
                        <label for="start_date">開始日程</label>
                        <input id="start_date" class="input-date" type="date" name="start_date" value="" />
                        <label for="end_date">終了日程</label>
                        <input id="end_date" class="input-date" type="date" name="end_date" value="" />
                    </div>
                    <div id="event_time">
                        <label for="start_time">開始時間</label>
                        <input id="start_time" class="input-date" type="time" name="start_time" value="" />
                        <label for="end_time">終了時間</label>
                        <input id="end_time" class="input-date" type="time" name="end_time" value="" />
                    </div>
                    <label for="is_allday">
                        <input type="hidden" name="is_allday" value="false">
                        <input id="date_mode" class="input-date_mode" type="checkbox" name="is_allday" value="true" onclick="changeUpdateDateMode()">
                        終日
                    </label>
                    <label for="event_body" style="display: block">内容</label>
                    <textarea id="event_body" name="event_body" rows="3" value=""></textarea>
                    <label for="event_color">背景色</label>
                    <select id="event_color" name="event_color">
                        <option value="blue">青</option>
                        <option value="green">緑</option>
                    </select>
                    <button type="button" onclick="closeUpdateModal()">キャンセル</button>
                    <button type="submit">決定</button>
                </form>
                <form id="delete-form" method="post" action="{{ route('delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete-id" name="id" value="" />
                    <button class="delete" type="button" onclick="deleteEvent()">削除</button>
                </form>
            </div>
        </div>
    </body>
</html>

<style scoped>
/* event上はマウスがポインターになる */
.fc-event-title-container{
    cursor: pointer;
}

/* モーダル */
.modal{
    display: none; /* モーダル開くときにflexに変更 */
    justify-content: center;
    align-items: center;
    position: absolute;
    z-index: 10; /* カレンダーの曜日表示がz-index=2 */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    background-color: rgba(0,0,0,0.5);
}
.modal-contents{
    background-color: white;
    height: 480px;
    width: 600px;
    padding: 20px;
}

/* その他 */
input{
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
}
.input-title{
    display: block;
    width: 80%;
    margin: 0 0 20px;
}
.input-date{
    width: 27%;
    margin: 0 5px 5px 0;
}
.input-date_mode{
    margin-bottom: 20px;
}
textarea{
    display: block;
    width: 80%;
    margin: 0 0 20px;
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
    resize: none;
}
select{
    display: block;
    width: 20%;
    margin: 0 0 20px;
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
}
</style>
