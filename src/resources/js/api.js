import axios from 'axios';
// console.log("This is api.js.")

// 東京(130000)の予報を取得
// response.data[0]の方に3日間、response.data[0]の方に7日間の天気予報が入っている。
const url = axios.get("https://www.jma.go.jp/bosai/forecast/data/forecast/130000.json")
            .then((response) => {
                console.log(response.data);
                // 発表者と報告日時の情報を画面に書き出す
                document.getElementById("publishingOffice").lastElementChild.textContent = response.data[0].publishingOffice;
                document.getElementById("reportDatetime").lastElementChild.textContent = response.data[0].reportDatetime;
                // 特定地域の情報を画面に書き出す
                document.getElementById("targetArea").lastElementChild.textContent = response.data[0].timeSeries[0].areas[0].area.name;
                document.getElementById("today").lastElementChild.textContent = response.data[0].timeSeries[0].areas[0].weatherCodes[0];
                document.getElementById("tomorrow").lastElementChild.textContent = response.data[0].timeSeries[0].areas[0].weatherCodes[1];
                document.getElementById("dayAfterTomorrow").lastElementChild.textContent = response.data[0].timeSeries[0].areas[0].weatherCodes[2];
            })
            .catch((error)=>{
                console.log("error: ", error);
            })