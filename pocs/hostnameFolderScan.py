import random
import time

import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    host = urllib3.get_host(url)[1]
    hostSplit = str(host).split(".")
    fileNames = []
    fileNames.append(host)
    fileNames.append(hostSplit[0])
    extensions = ['.zip', '.rar', '.tar.gz', '.tgz']
    if url[-1] != "/":
        url = url + "/"
    userAgentList = [
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0",
        "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 "
        "Safari/534.50",
        "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20220101 Firefox/4.0.1",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) Gecko/20100101 Firefox/61.0",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36"
    ]

    for fileName in fileNames:
        for extension in extensions:
            headers = {
                "User-Agent": random.choice(userAgentList)
            }
            targetUrl = url + fileName + extension
            response = requests.get(targetUrl, headers=headers, timeout=20, verify=False)  # verify=False不验证网站的ca证书
            status = response.status_code  # HTTP请求返回状态码
            headers = response.headers
            if (status == 200 or status == 206) and "application/" in headers['Content-Type']:
                return targetUrl
            time.sleep(1)

    return False


if __name__ == '__main__':
    # print(doCheck("http://www.dituhui.com/"))
    # print(doCheck("http://api.developer.rongcloud.cn/"))
    # print(doCheck("http://open.benlai.com/"))
    print(doCheck("https://passport.tianjihr.com/"))
    # print(doCheck("http://www.cgfreight.cn/config.rar"))
