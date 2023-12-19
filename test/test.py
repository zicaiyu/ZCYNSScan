import requests

headers = {
            "Host": "www.xianzhuanxia.com",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cache-Control ": "max-age=0",
            "Content-Length": "505",
            "Content-Type": "application/x-www-form-urlencoded",
            "Origin": "https://www.xianzhuanxia.com",
            "Referer": "https://www.xianzhuanxia.com/user/qq",
            "Sec-Fetch-Dest": "document",
            "Sec-Fetch-Mode": "navigate",
            "Sec-Fetch-Site": "same-origin",
            "Upgrade-Insecure-Requests": "1",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
            "dnt": "1",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
        }
r = requests.post(url="https://www.xianzhuanxia.com/api/reSet/sendCode",
              data='{}', headers=headers, timeout=5, verify=False)

print(r.text)