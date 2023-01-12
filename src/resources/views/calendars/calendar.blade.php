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

        <!-- カレンダー詳細表示モーダル -->
        <div id="modal-1" class="modal" aria-hidden="true" data-micromodal-close>
            <div class="overlay">
                <div class="elements" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                    <div id="modal-1-content">
                        <input type="hidden" id="id" name="id" value="" />
                        <label for="title">タイトル</label>
                        <input type="text" id="edit_title" name="title" value="" />
                        <label for="start">開始日時</label>
                        <input type="date" id="edit_start" name="start" value="" />
                        <label for="end">終了日時</label>
                        <input type="date" id="edit_end" name="end" value="" />
                        <label for="body">内容</label>
                        <textarea id="edit_body" name="body" rows="3" value=""></textarea>
                    </div>
                    <button type="button" aria-label="Close modal" data-micromodal-close>キャンセル</button>
                    <button type="submit">決定</button>
                </div>
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

input{
    display: block;
    width: 50%;
    margin: 0 0 10px;
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
}
textarea{
    display: block;
    width: 80%;
    margin: 0 0 10px;
    padding: 2px;
    border: 1px solid black;
    border-radius: 5px;
    resize: none;
}
</style>