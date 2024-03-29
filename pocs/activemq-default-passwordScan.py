import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(baseurl):
    url = baseurl + "admin/"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(url, headers=headers, timeout=5, verify=False)
    if response.status_code == 401 and "Unauthorized" in response.text:
        r0 = True
    else:
        r0 = False
    url = baseurl + "admin/"
    headers = {'Authorization': 'Basic YWRtaW46YWRtaW4='}
    response = requests.get(url, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and "Welcome to the Apache ActiveMQ Console of" in response.text and "<h2>Broker</h2>" in response.text:
        r1 = True
    else:
        r1 = False
    if r0 and r1:
        return url
    else:
        return False
