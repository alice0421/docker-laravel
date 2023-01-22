# **Laravel9×FullCalendar(ver.6)**
# 概要
Laravel9とFullCalendar(ver.6)を使った簡易的なカレンダーウェブアプリを作成する。
本記事では、モーダルを用いてカレンダーの追加・作成・削除を行う。
- 参考:
    - [FullCalender公式サイト](https://fullcalendar.io/)
    - [【FullCalendar】導入とスケジュールの登録【実装】](https://chigusa-web.com/blog/fullcalendar/)
    - [【Laravel】FullCalendarでスケジュールのDB登録・表示【実践向け】](https://chigusa-web.com/blog/laravel-fullcalendar/)
    - [【JavaScriptの実践】モーダルウィンドウの作り方](https://tcd-theme.com/2021/08/javascript-modalwindow.html)

### 学べること
- FullCalendarのデモ
- モーダルの作り方
- viteの癖

## FullCalendarとは
公式サイト：https://fullcalendar.io/
JavaScriptのライブラリであるjQueryのオープンソースプラグインであり、カレンダーを簡単に作成・表示することができる。
提供されている機能が豊富なため、比較的拡張しやすい。

# 各バージョン
|ツール・アプリケーション|バージョン|
|--|--|
|Laravel9|9.46.0|
|axios|1.2.2|
|FullCalendar|6.0.3|

# 準備
## FullCalendarのインストール
[FullCalendar公式](https://fullcalendar.io/docs/initialize-globals)を参考にnpmでインストールする。
```ターミナル
$ npm install fullcalendar
```
すると、以下の最新バージョン(ver.6.*)のバンドルが一括でインストールされる（`npm list`には`fullcalendar@6.*`のように登録される）。
> @fullcalendar/core
> @fullcalendar/interaction (for date selecting, event dragging & resizing)
> @fullcalendar/daygrid (for month and dayGrid views)
> @fullcalendar/timegrid (for timeGrid views)
> @fullcalendar/list (for list views)

別のインストール方法として、今回使用するのは以下だけになるので、それだけを個別でインストールしても構わない。
> @fullcalendar/core
> @fullcalendar/interaction
> @fullcalendar/daygrid
> @fullcalendar/timegrid
```ターミナル
$ npm install @fullcalendar/core @fullcalendar/interaction @fullcalendar/daygrid @fullcalendar/timegrid
```

## axiosのインストール
axiosが入ってなければ、以下のコマンドでインストールする。
```console
$ npm install axios
```

## 動作確認
### モデルの追加
以下のコマンドを打ち、`app/Http/Models/Event.php`と`events`テーブルのマイグレーションファイル（`20**_**_**_******_create_events_table.php`）を作成する。
```ターミナル
$ php artisan make:model Event --migration
```
モデルには現時点では特に何も記入しない。
作成したマイグレーションファイルには、以下を追記する（後にDBにカレンダーのイベントを保存するため）。
```php
// 20**_**_**_******_create_events_table.php

public function up()
{
    // 以下のようなカラム構成にする
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('event_title')->comment('イベント名');
        $table->string('event_body')->nullable()->comment('イベント内容');
        $table->date('start_date')->comment('開始日');
        $table->date('end_date')->comment('終了日');
        $table->string('event_color')->comment('背景色');
        $table->string('event_border_color')->comment('枠線色');
        $table->timestamps();
    });
    }
```

### ルーティングの追加
`routes/web.php`にカレンダーを表示するページへのURLを記述する。
```php
// web.php

use App\Http\Controllers\EventController; // 追加忘れずに

Route::get('/calendar', [EventController::class, 'show'])->name("show"); // カレンダー表示
```
### コントローラーの追加
以下のコマンドを打ち、`app/Http/Controllers/EventController.php`を作成する。
```ターミナル
$ php artisan make:controller EventController
```
作成したコントローラーに、カレンダーを表示するページを返す`show`メソッドを追記する。
```php
// EventController.php

use App\Models\Event; // Model追加忘れずに

class EventController extends Controller
{   
    // カレンダー表示
    public function show(){
        return view("calendars/calendar");
    }
}
```

### ビューの追加
カレンダーを表示するページをcalendarsフォルダ下（`resources/views/calendars/calendar.blade.php`）に作成する。
```html
<!-- calendar.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FullCalendar</title>
        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- vite用の記述忘れずに -->
    </head>
    <body>
        <!-- 以下のdivタグ内にカレンダーを表示 -->
        <div id='calendar'></div>
    </body>
</html>
```

### JavaScriptファイルの追加
カレンダーの設定・表示のために、`resources/js/calendar.js`に以下の記述を行う。
```javascript
// calendar.js

import '@fullcalendar/core/vdom'; // （for Vite）ver6には不要なので、エラーが出たらここを消す。
import axios from "axios";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from '@fullcalendar/timegrid';

// カレンダーを表示させたいタグのidを取得
var calendarEl = document.getElementById("calendar");

// new Calender(カレンダーを表示させたいタグのid, {各種カレンダーの設定});
let calendar = new Calendar(calendarEl, {
    // プラグインの導入(import忘れずに)
    plugins: [dayGridPlugin, timeGridPlugin],

    // カレンダー表示
    initialView: "dayGridMonth", // 最初に表示させるページの形式
    headerToolbar: { // ヘッダーの設定
        // コンマのみで区切るとページ表示時に間が空かず、半角スペースで区切ると間が空く（半角があるかないかで表示が変わることに注意）
        start: "prev,next today", // ヘッダー左（前月、次月、今日の順番で左から配置）
        center: "title", // ヘッダー中央（今表示している月、年）
        end: "dayGridMonth,timeGridWeek", // ヘッダー右（月形式、時間形式）
    },
    height: "auto", // 高さをウィンドウサイズに揃える
});

// カレンダーのレンダリング
calendar.render();
```

## viteの設定
viteでjsをコンパイルさせるために、`resources/js/app.js`に`calendar.js`を読み込む。
```javascript
// app.js

import './bootstrap';
// resources/js下のファイルはここに追記すれば、全てviteでコンパイルされる
import './calendar'; // これを追記
```

以上の準備を完了し、最後にコンソールで`npm run build`する。
以下のようなページが表示されたらOK。

<img width="1439" alt="calendar_01" src="https://user-images.githubusercontent.com/86033630/213919796-ed810327-cff5-4127-b886-bedc92103ada.png">

# 基礎機能実装
カレンダーの表示ができたので、カレンダーの各機能を実装していく。

## 予定の追加（ボタン）
予定追加ボタンを押すと、予定が入力できるモーダルが表示され、カレンダーに新たに予定が追加されるような挙動を実装する。
> ※ 以下のデモは、[予定の追加（ボタン）](#予定の追加（ボタン）)と[予定の表示](#予定の表示)の実装が完了した段階のものである。

https://user-images.githubusercontent.com/86033630/213919495-9f41d886-fb7f-49c6-bf99-0fab65ef033c.mov

### ルーティングの追加
`web.php`にカレンダーに新しく予定を追加し、DBに保存するためのPOSTリクエストを記述する。
```php
// web.php

// 以下を追記
Route::post('/calendar/create', [EventController::class, 'create'])->name("create"); // 予定の新規追加
```

### コントローラーの追加
`EventController.php`の`creat`メソッドに、カレンダーに新しく追加された予定をDBに保存する処理を追記する。
```php
// EventController.php

...
class EventController extends Controller
{
...
//（ここから）追記
    // 新規予定追加
    public function create(Request $request, Event $event){
        // バリデーション（eventsテーブルの中でNULLを許容していないものをrequired）
        $request->validate([
            'event_title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'event_color' => 'required',
        ]);

        // 登録処理
        $event->event_title = $request->input('event_title');
        $event->event_body = $request->input('event_body');
        $event->start_date = $request->input('start_date');
        $event->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day")); // FullCalendarが登録する終了日は仕様で1日ずれるので、その修正を行っている
        $event->event_color = $request->input('event_color');
        $event->event_border_color = $request->input('event_color');
        $event->save();

        // カレンダー表示画面にリダイレクトする
        return redirect(route("show"));
    }
//（ここまで）
}
```

### ビューの改良（モーダルの作成）
予定が入力できるモーダルをビューファイルに追記する。

`calendar.blade.php`に、以下のようにカレンダー新規追加モーダルを追記する。モーダルを表示するためのCSSも`style`タブの間に記述する。
```html
<!-- calendar.blade.php -->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body>
...
        <!-- カレンダー表示 -->
        <div id='calendar'></div>

<!-- （ここから）追記1 -->
        <!-- カレンダー新規追加モーダル -->
        <div id="modal-add" class="modal">
            <div class="modal-contents">
                <form method="POST" action="{{ route('create') }}">
                    @csrf
                    <input id="new-id" type="hidden" name="id" value="" />
                    <label for="event_title">タイトル</label>
                    <input id="new-event_title" class="input-title" type="text" name="event_title" value="" />
                    <label for="start_date">開始日時</label>
                    <input id="new-start_date" class="input-date" type="date" name="start_date" value="" />
                    <label for="end_date">終了日時</label>
                    <input id="new-end_date" class="input-date" type="date" name="end_date" value="" />
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
<!-- （ここまで） -->
    </body>
</html>

<!-- （ここから）追記2 -->
<style scoped>
/* モーダルのオーバーレイ */
.modal{
    display: none; /* モーダル開くとflexに変更（ここの切り替えでモーダルの表示非表示をコントロール） */
    justify-content: center;
    align-items: center;
    position: absolute;
    z-index: 10; /* カレンダーの曜日表示がz-index=2のため、それ以上にする必要あり */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    width: 100%;
    background-color: rgba(0,0,0,0.5);
}
/* モーダル */
.modal-contents{
    background-color: white;
    height: 400px;
    width: 600px;
    padding: 20px;
}

/* 以下モーダル内要素のデザイン調整 */
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
<!-- （ここまで） -->
```

### JavaScriptファイルの改良
#### 予定追加ボタン（モーダルを開く挙動）の作成
まずは予定追加ボタンを実装する。

FullCalendarでは、[カスタムボタン機能](https://fullcalendar.io/docs/customButtons)があるのでそちらを用いる。
新規予定追加のカスタムボタンを作成し、それを`headerToolbar`でページに表示するように`calendar.js`を改良する。

`calendar.js`に以下を追記する。
```javascript
// calendar.js

...
let calendar = new Calendar(calendarEl, {
...
    // カレンダー表示
    initialView: "dayGridMonth",
// （ここから）追記1
    customButtons: { // カスタムボタン
        eventAddButton: { // 新規予定追加ボタン
            text: '予定を追加',
            click: function() {
                // 初期化（以前入力した値をクリアする）
                document.getElementById("new-id").value = "";
                document.getElementById("new-event_title").value = "";
                document.getElementById("new-start_date").value = "";
                document.getElementById("new-end_date").value = "";
                document.getElementById("new-event_body").value = "";
                document.getElementById("new-event_color").value = "blue";

                // 新規予定追加モーダルを開く
                document.getElementById('modal-add').style.display = 'flex';
            }
        }
    },
//（ここまで）
    headerToolbar: {
        start: "prev,next today",
        center: "title",
        end: "eventAddButton dayGridMonth,timeGridWeek", // 追記2（半角スペースは必要）
    },
    height: "auto",
});
...
```

#### モーダルを閉じる挙動の作成
このままではモーダルが開いたままなので、モーダルを閉じる挙動を追加する。

モーダル内要素のキャンセルボタンをクリックするとモーダルが閉じるように`calendar.js`を改良する。

`calendar.js`に以下を追記する。
```javascript
// calendar.js

...
let calendar = new Calendar(calendarEl, {
...
});

calendar.render();

//（ここから）追記
// 新規予定追加モーダルを閉じる
window.closeAddModal = function(){
    document.getElementById('modal-add').style.display = 'none';
}
// （ここまで）
```
> ※ viteでコンパイルする都合上、BladeファイルからJavaScripに書かれた関数を呼び出す際、windowオブジェクトのプロパティに代入しないと未定義（undefine）となって呼び出せないので要注意。
> - 参考：[Laravel Mix使用時にJavascriptの関数が未定義(not found)になるときの対処法](https://alaki.co.jp/blog/?p=4040)

## 予定の表示
[予定の追加（ボタン）](#予定の追加（ボタン）)だけでは、カレンダーにDBに登録した予定を表示することはできない。そのため、DBに登録した予定をカレンダーに表示させる挙動を別途実装する必要がある。

### JavaScriptファイルの改良
FullCalendarには、[ビューを切り替えたり再読み込みする度に実行する関数](https://fullcalendar.io/docs/events-function)を作成することができる。
今回はこの関数内で、axiosを用いて、DBに登録した予定をカレンダーに表示させる挙動を実装する。

`calendar.js`に以下を追記する。
```javascript
// calendar.js

...
let calendar = new Calendar(calendarEl, {
...
    headerToolbar: {
        start: "prev,next today",
        center: "title", 
        end: "eventAddButton dayGridMonth,timeGridWeek",
    },
    height: "auto",

//（ここから）追記
    // DBに登録した予定を表示する
    events: function (info, successCallback, failureCallback) { // eventsはページが切り替わるたびに実行される
        // axiosでLaravelの予定取得処理を呼び出す
        axios
            .post("/calendar/get", {
                // 現在カレンダーが表示している日付の期間(1月ならば、start_date=1月1日、end_date=1月31日となる)
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // 既に表示されているイベントを削除（重複防止）
                calendar.removeAllEvents(); // ver.6でもどうやら使える（ドキュメントにはない？）
                // カレンダーに読み込み
                successCallback(response.data); // successCallbackに予定をオブジェクト型で入れるとカレンダーに表示できる
            })
            .catch((error) => {
                // バリデーションエラーなど
                alert("登録に失敗しました。");
            });
    },
// （ここまで）
});
...
```

### ルーティングの追加
`web.php`に、DBに登録した予定を取得するPOSTリクエストを記述する。
axiosでは、このURL（WebAPI）から予定を取得することができる。
```php
// web.php

// 以下を追記
Route::post('/calendar/get',  [EventController::class, 'get'])->name("get"); // DBに登録した予定を取得
```

### コントローラーの追加
`EventController.php`の`get`メソッドに、DBに登録した予定を取得する処理を追記する。
```php
// EventController.php

...
class EventController extends Controller
{
...
//（ここから）追記
    // DBから予定取得
    public function get(Request $request, Event $event){
        // バリデーション
        $request->validate([
            'start_date' => 'required|integer',
            'end_date' => 'required|integer'
        ]);

        // 現在カレンダーが表示している日付の期間
        $start_date = date('Y-m-d', $request->input('start_date') / 1000); // 日付変換（JSのタイムスタンプはミリ秒なので秒に変換）
        $end_date = date('Y-m-d', $request->input('end_date') / 1000);

        // 予定取得処理（これがaxiosのresponse.dataに入る）
        return $event->query()
            // DBから取得する際にFullCalendarの形式にカラム名を変更する
            ->select(
                'id',
                'event_title as title',
                'event_body as description',
                'start_date as start',
                'end_date as end',
                'event_color as backgroundColor',
                'event_border_color as borderColor'
            )
            // 表示されているカレンダーのeventのみをDBから検索して表示
            ->where('end_date', '>', $start_date)
            ->where('start_date', '<', $end_date) // AND条件
            ->get();
    }
//（ここまで）
}
```

以上を完了し、最後にコンソールで`npm run build`する。
[予定の追加（ボタン）](#予定の追加（ボタン）)で示したような挙動が全てできたらOK。

## 予定の編集
カレンダーに表示されている予定を押すと、予定が編集できるモーダルが表示され、予定を編集できる挙動を実装する。

https://user-images.githubusercontent.com/86033630/213919620-baa377d4-a138-4c2f-812f-595ead4d4ee3.mov

### ルーティングの追加
`web.php`に、DBに登録した予定を取得するPUTリクエストを記述する。
axiosでは、このURL（WebAPI）から予定を取得することができる。
```php
// web.php

// 以下を追記
Route::put('/calendar/update', [EventController::class, 'update'])->name("update"); // 予定の更新
```

### コントローラーの追加
`EventController.php`の`update`メソッドに、編集した予定をDBに更新する処理を追記する。
```javascript
// EventController.php

...
class EventController extends Controller
{
...
//（ここから）追記
    // 予定の更新
    public function update(Request $request, Event $event){
        $input = new Event();

        $input->event_title = $request->input('event_title');
        $input->event_body = $request->input('event_body');
        $input->start_date = $request->input('start_date');
        $input->end_date = date("Y-m-d", strtotime("{$request->input('end_date')} +1 day"));
        $input->event_color = $request->input('event_color');
        $input->event_border_color = $request->input('event_color');

        // 更新する予定をDBから探し（find）、内容が変更していたらupdated_timeを変更（fill）して、DBに保存する（save）
        $event->find($request->input('id'))->fill($input->attributesToArray())->save(); // fill()の中身はArray型が必要だが、$inputのままではコレクションが返ってきてしまうため、Array型に変換

        // カレンダー表示画面にリダイレクトする
        return redirect(route("show"));
    }
//（ここまで）
}
```

### モデルの追加
コントローラで`fill`を用いたため、モデルで`fillable`を宣言する。

`app/Models/Event.php`に以下を追記する。
```php
// Event.php

...
class Event extends Model
{
    use HasFactory;

// （ここから）追記
    // Controllerのfill用
    protected $fillable = [
        'event_title',
        'event_body',
        'start_date',
        'end_date',
        'event_color',
        'event_border_color',
    ];
// （ここまで）
}
```

### ビューの改良（モーダルの作成）
予定が編集できるモーダルをビューファイルに追記する。

`calendar.blade.php`に、以下のようにカレンダー予定編集モーダルを追記する。また、カレンダーに表示されている予定の上にカーソルを持っていった際に、カーソルがポインターになるようなCSSも追記する。
```html
<!-- calendar.blade.php -->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body>
...
        <!-- カレンダー表示 -->
        <div id='calendar'></div>

        <!-- カレンダー新規追加モーダル -->
        <div id="modal-add" class="modal">
        ...
        </div>

<!-- （ここから）追記1 -->
        <!-- カレンダー編集モーダル -->
        <div id="modal-update" class="modal">
            <div class="modal-contents">
                <form method="POST" action="{{ route('update') }}" >
                    @csrf
                    @method('PUT')
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
                    <button type="button" onclick="closeUpdateModal()">キャンセル</button>
                    <button type="submit">決定</button>
                </form>
            </div>
        </div>
<!-- （ここまで） -->
    </body>
</html>

<style scoped>
/* （ここから）追記2 */
/* 予定の上ではカーソルがポインターになる */
.fc-event-title-container{
    cursor: pointer;
}
/* （ここまで） */
...
</style>
```

### JavaScriptファイルの改良
#### モーダルを開く挙動の作成
FullClandarには、[予定をクリックすると実行できる関数](https://fullcalendar.io/docs/eventClick)を作成することができる。
これを用いて、予定をクリックするとモーダルが開く挙動を実装する。

`calendar.js`に以下を記述する。
```javascript
// calendar.js

...
// （ここから）追記1
// 日付を-1してYYYY-MM-DDの書式で返すメソッド
function formatDate(date, pos) {
    var dt = new Date(date);
    if(pos==="end"){
        dt.setDate(dt.getDate() - 1);
    }
    return dt.getFullYear() + '-' +('0' + (dt.getMonth()+1)).slice(-2)+ '-' +  ('0' + dt.getDate()).slice(-2);
}
// （ここまで）

var calendarEl = document.getElementById("calendar");

let calendar = new Calendar(calendarEl, {
...
    events: function (info, successCallback, failureCallback) { 
    ...
    },

// （ここから）追記2
    // 予定をクリックすると予定編集モーダルが表示される
    eventClick: function(info) {
        // console.log(info.event); // info.event内に予定の全情報が入っているので、必要に応じて参照すること
        document.getElementById("id").value = info.event.id;
        document.getElementById("event_title").value = info.event.title;
        document.getElementById("start_date").value = formatDate(info.event.start);
        document.getElementById("end_date").value = formatDate(info.event.end, "end");
        document.getElementById("event_body").value = info.event.extendedProps.description;
        document.getElementById("event_color").value = info.event.backgroundColor;

        // 予定編集モーダルを開く
        document.getElementById('modal-update').style.display = 'flex';
    },
// （ここまで）
});
...
```

#### モーダルを閉じる挙動の作成
同様に、モーダルを閉じる挙動を実装する。

`calendar.js`に以下を記述する。
```javascript
// calendar.js

...
let calendar = new Calendar(calendarEl, {
...
});

calendar.render();

window.closeAddModal = function(){
    document.getElementById('modal-add').style.display = 'none';
}

//（ここから）追記
// 予定編集モーダルを閉じる
window.closeUpdateModal = function(){
    document.getElementById('modal-update').style.display = 'none';
}
// （ここまで）
```

以上を完了し、最後にコンソールで`npm run build`する。
[予定の編集](#予定の編集)で示したような挙動が全てできたらOK。

## 予定の削除
最後に、予定を削除する機能を実装する。
予定が編集できるモーダルに削除ボタンを追加し、削除ボタンを押すと予定を削除する挙動を実装する。

https://user-images.githubusercontent.com/86033630/213919653-3f6a9503-06b6-40ce-b037-238dfc0caa2e.mov

### ルーティングの追加
`web.php`に、予定を削除するDELETEリクエストを記述する。
```php
// web.php

// 以下を追記
Route::delete('/calendar/delete', [EventController::class, 'delete'])->name("delete"); // 予定の削除
```

### コントローラーの追加
`EventController.php`の`delete`メソッドに、予定を削除する処理を追記する。
```javascript
// EventController.php

...
class EventController extends Controller
{
...
//（ここから）追記
    // 予定の削除
    public function delete(Request $request, Event $event){
        // 削除する予定をDBから探し（find）、DBから物理削除する（delete）
        $event->find($request->input('id'))->delete();

        // カレンダー表示画面にリダイレクトする
        return redirect(route("show"));
    }
//（ここまで）
}
```

### ビューの改良
予定が編集できるモーダルに、削除ボタンを追記する。

`calendar.blade.php`に、以下のように削除ボタンを追記する。
```html
<!-- calendar.blade.php -->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <body>
...
        <!-- カレンダー表示 -->
        <div id='calendar'></div>

        <!-- カレンダー新規追加モーダル -->
        <div id="modal-add" class="modal">
        ...
        </div>

        <!-- カレンダー編集モーダル -->
        <div id="modal-update" class="modal">
            <div class="modal-contents">
                <form method="POST" action="{{ route('update') }}" >
                ...
                </form>
<!-- （ここから）追記 -->
                <form id="delete-form" method="post" action="{{ route('delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete-id" name="id" value="" />
                    <button class="delete" type="button" onclick="deleteEvent()">削除</button>
                </form>
<!-- （ここまで） -->
            </div>
        </div>
    </body>
</html>
...
```

### JavaScriptファイルの改良
予定をクリックした際に、削除に必要な情報（削除する予定のid）を持ってくるように改良する。

`calendar.js`に以下を記述する。
```javascript
// calendar.js

...
let calendar = new Calendar(calendarEl, {
...
    events: function (info, successCallback, failureCallback) { 
    ...
    },
    eventClick: function(info) {
        document.getElementById("id").value = info.event.id;
        document.getElementById("delete-id").value = info.event.id; // ここを追記
        document.getElementById("event_title").value = info.event.title;
        document.getElementById("start_date").value = formatDate(info.event.start);
        document.getElementById("end_date").value = formatDate(info.event.end, "end");
        document.getElementById("event_body").value = info.event.extendedProps.description;
        document.getElementById("event_color").value = info.event.backgroundColor;

        document.getElementById('modal-update').style.display = 'flex';
    },
});
...
```

#### 削除確認ポップアップの作成
削除ボタンを押した際、削除確認ポップアップが表示され、そこでOKを押したら実際に予定が削除されるように実装する。

`calendar.js`に以下を記述する。
```javascript
// calendar.js

...
let calendar = new Calendar(calendarEl, {
...
});

calendar.render();

window.closeAddModal = function(){
    document.getElementById('modal-add').style.display = 'none';
}

window.closeUpdateModal = function(){
    document.getElementById('modal-update').style.display = 'none';
}

//（ここから）追記
window.deleteEvent = function(){
    'use strict'

    if (confirm('削除すると復元できません。\n本当に削除しますか？')) {
        document.getElementById('delete-form').submit();
    }
}
// （ここまで）
```

以上を完了し、最後にコンソールで`npm run build`する。
[予定の削除](#予定の削除)で示したような挙動が全てできたらOK。

# 発展機能実装
## 予定の追加（日程選択）
カレンダー内の日程を1つもしくは複数選択すると、予定が入力できるモーダルが表示され、カレンダーに新たに予定が追加されるような挙動を実装する。

https://user-images.githubusercontent.com/86033630/213919557-d9e3dc2d-7597-4b35-b6d3-86b8f1d44e9f.mov

### JavaScriptファイルの改良
FullCalendarには、[日程を選択してカレンダーに予定を追加できる機能](https://fullcalendar.io/docs/select-callback)があるのでそちらを用いる。
`interactionPlugin`が新たに必要なので、importとプラグインの導入を忘れずに。

`calendar.js`に以下を追記する。
```javascript
// calendar.js

...
import interactionPlugin from '@fullcalendar/interaction'; // 追記1（interactionPluginの導入）
...
let calendar = new Calendar(calendarEl, {
plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin], // 追記2（interactionPluginの導入）
...
    headerToolbar: {
        start: "prev,next today",
        center: "title",
        end: "eventAddButton dayGridMonth,timeGridWeek",
    },
    height: "auto",

// （ここから）追記3
    // カレンダーで日程を指定して新規予定追加
    selectable: true, // 日程の選択を可能にする
    select: function (info) { // 日程を選択した後に行う処理を記述
        // 選択した日程を反映（のこりは初期化）
        document.getElementById("new-id").value = "";
        document.getElementById("new-event_title").value = "";
        document.getElementById("new-start_date").value = formatDate(info.start); // 選択した開始日を反映
        document.getElementById("new-end_date").value = formatDate(info.end, "end"); // 選択した終了日を反映
        document.getElementById("new-event_body").value = "";
        document.getElementById("new-event_color").value = "blue";

        // 新規予定追加モーダルを開く
        document.getElementById('modal-add').style.display = 'flex';
    },
//（ここまで）
...
});
...
```

## 日程のバリデーション
現在、開始日が終了日よりも後ろの予定（例) 1月8日開始、1月5日終了）は追加できないようになっている。しかし、その際のエラーがフロント側で表示されないため、改良を行う。
> ※ 準備中…

## 時間を追加
現在、追加できる予定は終日の予定のみとなっている。そのため、時間を含めた予定が追加できるように改良する。
> ※ 準備中…
