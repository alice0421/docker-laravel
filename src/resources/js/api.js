import axios from 'axios';
// console.log("This is api.js.")

// 東京(130000)の予報を取得（気象庁のjsonデータ）
// response.data[0]の方に3日間、response.data[0]の方に7日間の天気予報が入っている。
const url = axios.get("https://www.jma.go.jp/bosai/forecast/data/forecast/130000.json")
            .then((response) => {
                console.log(response.data);
                // 発表者と報告日時の情報を画面に書き出す
                document.getElementById("publishingOffice").lastElementChild.textContent = response.data[0].publishingOffice;
                document.getElementById("reportDatetime").lastElementChild.textContent = response.data[0].reportDatetime;
                // 特定地域の情報を画面に書き出す
                document.getElementById("targetArea").lastElementChild.textContent = response.data[0].timeSeries[0].areas[0].area.name;
                weatherShow("today", response.data[0].timeSeries[0].areas[0].weatherCodes[0]);
                weatherShow("tomorrow", response.data[0].timeSeries[0].areas[0].weatherCodes[1]);
                weatherShow("dayAfterTomorrow", response.data[0].timeSeries[0].areas[0].weatherCodes[2]);
            })
            .catch((error)=>{
                console.log("error: ", error);
            });

// jsonを介してweatherCodeとweatherの対応を検索（毎回リクエストを投げるのはまずいか？）
function weatherShow(id, code){
    axios.get("/data/weather.json")
        .then((response) => {
            document.getElementById(id).lastElementChild.textContent = response.data.find((w) => w.weatherCode == code).weather;
        })
        .catch((error)=>{
            console.log("error: ", error);
        });

    // switch直書きでも良いが、api.jsが長くなる。
    // switch(code){
    //     case "100":
    //         return "晴れ";
    //     case "101":
    //         return "晴れ時々曇り";
    //     default:
    //         return "その他";
    // }
} 