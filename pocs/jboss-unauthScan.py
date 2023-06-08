import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    target_url = url + "jmx-console/"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(target_url, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and "jboss.management.local" in response.text and "jboss.web" in response.text:
        return target_url
    else:
        return False


if __name__ == '__main__':
    print(doCheck("https://bnjy.aeonlife.com.cn/"))

