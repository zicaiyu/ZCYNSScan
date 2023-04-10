import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def getDnslogResult(cookie):
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
        "Accept-Language": "zh-CN,zh;q=0.9",
        "Cookie": cookie
    }
    try:
        r = requests.get(url, headers=headers, timeout=10, verify=False)
        if "[]" != r.text:
            return True
        else:
            return False
    except Exception:
        print("[   !   ]    获取dnslog失败")
        return False


if __name__ == "__main__":
    print(getDnslogResult("PHPSESSID=r3b6t5vmc7nhhkcrkssupo0a96; path=/"))
