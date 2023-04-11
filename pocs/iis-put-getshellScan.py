import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def randomLowercase(n):
    key = ""
    zf = "qwertyuiopasdfghjklzxcvbnm"
    import random
    for _ in range(n):
        suiji1 = random.randint(0, len(zf) - 1)
        key += zf[suiji1]
    return key


filename = randomLowercase(6)


def randomLowercase(n):
    key = ""
    zf = "qwertyuiopasdfghjklzxcvbnm"
    import random
    for _ in range(n):
        suiji1 = random.randint(0, len(zf) - 1)
        key += zf[suiji1]
    return key


fileContent = randomLowercase(6)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + "" + filename + ".txt"
    body = "" + fileContent + ""
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.put(targetUrl, body, headers=headers, timeout=20, verify=False)
    if response.status_code == 201:
        r0 = True
    else:
        r0 = False

    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(targetUrl, headers=headers, timeout=20, verify=False)
    if response.status_code == 200 and fileContent in response.text:
        r1 = True
    else:
        r1 = False
    if r0 and r1:
        return url
    else:
        return False


if __name__ == '__main__':
    print(doCheck("https://autodiscover.equippool.com/"))
