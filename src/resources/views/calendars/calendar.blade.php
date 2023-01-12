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
        <div id="modal-add" class="modal" aria-hidden="true" data-micromodal-close>
            <div class="overlay">
                <form class="elements" method="POST" action="{{ route('create') }}" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                    @csrf
                    <div id="modal-1-content">
                        <input type="hidden" name="id" value="" />
                        <label for="event_title">タイトル</label>
                        <input class="input-title" type="text" name="event_title" value="" />
                        <label for="start_date">開始日時</label>
                        <input class="input-date" type="date" name="start_date" value="" />
                        <label for="end_date">終了日時</label>
                        <input class="input-date" type="date" name="end_date" value="" />
                        <label for="event_body" style="display: block">内容</label>
                        <textarea name="event_body" rows="3" value=""></textarea>
                        <label for="event_color">背景色</label>
                        <select name="event_color">
                            <option value="blue" selected>青</option>
                            <option value="green">緑</option>
                        </select>
                    </div>
                    <button type="button" aria-label="Close modal" data-micromodal-close>キャンセル</button>
                    <button type="submit">決定</button>
                </form>
            </div>
        </div>

        <!-- カレンダー詳細表示モーダル（event更新） -->
        <div id="modal-update" class="modal" aria-hidden="true" data-micromodal-close>
            <div class="overlay">
                <form class="elements" method="POST" action="{{ route('update') }}" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                    @csrf
                    @method('PUT')
                    <div id="modal-2-content">
                        <input type="hidden" id="id" name="id" value="" />
                        <label for="event_title">タイトル</label>
                        <input class="input-title" type="text" id="event_title" name="event_title" value="" />
                        <label for="start_date">開始日時</label>
                        <input class="input-date" type="date" id="start_date" name="start_date" value="" />
                        <label for="end_date">終了日時</label>
                        <input class="input-date" type="date" id="end_date" name="end_date" value="" />
                        <label for="event_body" style="display: block">内容</label>
                        <textarea id="event_body" name="event_body" rows="3" value=""></textarea>
                        <label for="event_color">背景色</label>
                        <select id="event_color" name="event_color">
                            <option value="blue">青</option>
                            <option value="green">緑</option>
                        </select>
                    </div>
                    <button type="button" aria-label="Close modal" data-micromodal-close>キャンセル</button>
                    <button type="submit">決定</button>
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
    display: none;
}
.modal.is-open {
    z-index: 10;
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
.overlay{
    z-index: 20;
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    background-color: rgba(0, 0, 0, 0.5);
    width: 100vw;
    height: 100vh;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
.elements{
    z-index: 30;
    width: 600px;
    height: 400px;
    margin: 20px;
    padding: 20px;
    background-color: white;
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
    margin: 0 5px 20px 0;
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