import time

import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    time.sleep(1)
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + ".git/config"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0"
    }
    response = requests.get(targetUrl, headers=headers, timeout=5, verify=False)  # verify=False不验证网站的ca证书
    status = response.status_code  # HTTP请求返回状态码
    text = response.text  # HTTP请求返回状态码
    # print(text)
    if status == 200 and "[core]" in text:
        return targetUrl
    return False


if __name__ == '__main__':
    print(doCheck("https://www.xianzhuanxia.com/"))
    # print(doCheck("http://api-ddx.dmall.com/"))
