import requests
import base64
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + "manager/html"
    username = "tomcat"
    password = "tomcat"
    key = username + ':' + password
    bkey = base64.b64encode(key.encode()).decode()
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
        'Upgrade-Insecure-Requests': '1',
        'Authorization': 'Basic ' + bkey,
    }
    r = requests.get(targetUrl, headers=headers, verify=False, timeout=20)
    if r.status_code == 200 and "Tomcat" in r.text:
        return targetUrl
    else:
        return False


if __name__ == '__main__':
    print(doCheck("https://srm.sunac.com.cn/"))
