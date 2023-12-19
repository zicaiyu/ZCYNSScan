import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    if url[-1] != "/":
        url = url + "/"
    targetUrl = url + "sftp-config.json"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0"
    }
    response = requests.get(targetUrl, headers=headers, timeout=10, verify=False)  # verify=False不验证网站的ca证书
    status = response.status_code  # HTTP请求返回状态码

    text = response.text  # HTTP请求返回状态码
    # print(response.headers['Content-Type'])
    if status == 200 and "password" in text and "application/json" == response.headers['Content-Type']:
        return targetUrl
    return False


if __name__ == '__main__':
    print(doCheck("https://www.xianzhuanxia.com/"))
    # print(doCheck("https://dnakit.ibroadlink.com/"))
