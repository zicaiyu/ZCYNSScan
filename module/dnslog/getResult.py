import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def getResult():
    url = "http://dnslog.cn/getrecords.php"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) "
                      "Chrome/111.0.0.0 Safari/537.36",
        "Upgrade-Insecure-Requests": "1",
        "sec-gpc": "1",
        "Host": "dnslog.cn",
        "dnt": "1",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,"
                  "application/signed-exchange;v=b3;q=0.7",
        "Accept-Encoding": "gzip, deflate",
        "Accept-Language": "zh-CN,zh;q=0.9"
    }
    domain = ""
    cookie = ""
    result = (domain, cookie)
    try:
        r = requests.get(url, headers=headers, timeout=10, verify=False)
        domain = r.text
        cookie = r.headers["Set-Cookie"]
        result = (domain, cookie)
        return result
    except Exception:
        print("[   !   ]    获取dnslog失败")
        return result


if __name__ == "__main__":
    getDomain()
