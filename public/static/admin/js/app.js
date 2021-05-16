/**
 * 获取 blob
 * @param  {String} url 目标文件地址
 * @return {Promise}
 */
function getBlob(url) {
    return new Promise(resolve => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'blob';
        xhr.onload = () => {
            if (xhr.status === 200) {
                resolve(xhr.response);
            }
        };
        xhr.send();
    });
}
/**
 * 保存
 * @param  {Blob} blob
 * @param  {String} filename 想要保存的文件名称
 */
function saveAs(blob, filename) {
    if (window.navigator.msSaveOrOpenBlob) {
        navigator.msSaveBlob(blob, filename);
        return true;
    }
    const link = document.createElement('a');
    const body = document.querySelector('body');
    link.href = window.URL.createObjectURL(blob);
    link.download = filename;
    // fix Firefox
    link.style.display = 'none';
    body.appendChild(link);
    link.click();
    body.removeChild(link);
    window.URL.revokeObjectURL(link.href);
    return true;
}
/**
 * 下载
 * @param  {String} url 目标文件地址
 * @param  {String} filename 想要保存的文件名称
 */
function download(url, filename) {
    return getBlob(url).then(blob => {
        saveAs(blob, filename);
    });
}

function GetFileSize(size= 0) {
    if (!size) { return "";}
    let num = 1024;
    if (size < num) {return size + "B";}
    if (size < Math.pow(num, 2)) {return (size / num).toFixed(2) + "K";}
    if (size < Math.pow(num, 3)) {return (size / Math.pow(num, 2)).toFixed(2) + "M";}
    if (size < Math.pow(num, 4)) {return (size / Math.pow(num, 3)).toFixed(2) + "G";}
    return (size / Math.pow(num, 4)).toFixed(2) + "T";
}

function Trim(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}

function del_html_tags(str) {
    return Trim(str.replace(/<i\s*[^>]*>(.*?)<\/i>/ig,""));
}

function getRouteUrl(url = '') {
    let baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content') || '';
    if (!url || !baseUrl) { return url; }
    url.replace('.','/');
    return baseUrl +'/'+ url;
}

function get_responseText(XMLHttpRequest) {
    let status = XMLHttpRequest.status, responseText = XMLHttpRequest.responseText, msg = '不好，有错误';
    switch (status) {
        case 400:
            msg = responseText !== '' ? responseText : '失败了';
            break;
        case 401:
            msg = responseText !== '' ? responseText : '你没有权限';
            break;
        case 403:
            msg =  '你没有权限执行此操作!';
            break;
        case 404:
            msg = '你访问的操作不存在';
            break;
        case 406:
            msg = '请求格式不正确';
            break;
        case 410:
            msg = '你访问的资源已被删除';
            break;
        case 423:
        case 422:
            let responseJSON = XMLHttpRequest.responseJSON;
            if (responseJSON) {
                msg = responseJSON.message;
                break;
            }
            let errors = JSON.parse(responseText);
            if (errors instanceof Object) {
                let m = '';
                for(let index in errors){
                    let item = errors[index];
                    if (item instanceof Object) {
                        for(let i in item){
                            m = m + item[i] + '<br>';
                        }
                        break;
                    }
                    m = m + item + '<br>';
                }
                msg = m;
            }
            break;
        case 429:
            msg = '超出访问频率限制';
            break;
        case 500:
            msg = '500 INTERNAL SERVER ERROR';
            break;
    }
    return msg;
}

function getLangDate() {
    function dateFilter(date) { if (date < 10) { return "0" + date; } return date; }
    let dateObj = new Date()
        ,year = dateObj.getFullYear()
        ,month = dateObj.getMonth() + 1
        ,date = dateObj.getDate()
        ,day = dateObj.getDay()
        ,weeks = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"]
        ,week = weeks[day]
        ,hour = dateObj.getHours()
        ,minute = dateObj.getMinutes()
        ,second = dateObj.getSeconds()
        ,timeValue = "" + ((hour >= 12) ? (hour >= 18) ? "晚上" : "下午" : "上午");
    let newDate = dateFilter(year) + "年" + dateFilter(month) + "月" + dateFilter(date) + "日 " + " " + dateFilter(hour) + ":" + dateFilter(minute) + ":" + dateFilter(second);
    document.getElementById("nowTime").innerHTML = "亲爱的"+ document.getElementById("welcome-span").innerText +"，" + timeValue + "好！<br/> " + newDate + "　" + week;
    setTimeout(function () {
        getLangDate();
    }, 1000);
}
