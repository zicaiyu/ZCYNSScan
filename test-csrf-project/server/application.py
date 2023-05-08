import requests
import urllib3
from flask import Flask, jsonify, request
from flask_cors import CORS

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

app = Flask(__name__)
CORS(app, supports_credentials=True, origins='*')


@app.route("/")
def hello():
    print("heihei!!")
    return "Hello, World!"


@app.route("/test/csrf", methods=["POST"])
def test_csrf():
    data = request.json["data"]
    cookie = data['cookie']
    headers = {
        "Cookie": cookie,
        "sec-gpc": "1",
        "dnt": "1",
        "Accept-Language": "zh-CN,zh;q=0.9",
        "Accept-Encoding": "gzip, deflate",
        "Referer": "https://freightsmart.oocl.com/user/address",
        "Sec-Fetch-Dest": "empty",
        "Sec-Fetch-Mode": "cors",
        "Sec-Fetch-Site": "same-origin",
        "Origin": "https://freightsmart.oocl.com",
        "sec-ch-ua-platform": '"Windows"',
        "X-Auth-Token": "20618971-5726-4190-956c-20c83d4b1024",
        "Accept": "application/json,text/plain,*/*",
        "Content-Type": "application/json;charset=UTF-8",
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36",
        "sec-ch-ua-mobile": "?0",
        "X-Client-Timestamp": "1681743993808",
        "sec-ch-ua": '"Google Chrome";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
        "Content-Length": "133",
        "Connection": "close",
        "Host": "freightsmart.oocl.com",

    }
    body = '''
    {
        "type": "PERSONAL",
        "name": "ddd",
        "address": "ddd", 
        "phone": "13888888888",
        "fax": "",
        "email": "ddd@qq.com",
        "identityNo": "",
        "taxpayerCode": ""
    }
    '''
    # proxies = {'http': 'http://127.0.0.1:8088', 'https': 'http://127.0.0.1:8088'}
    # r = requests.post(url="https://freightsmart.oocl.com/api/admin/address/party", data=body, verify=False,
    #                   proxies=proxies)
    # r = requests.post(url="https://freightsmart.oocl.com/api/admin/address/party", data=body, headers=headers,
    #                   timeout=20, verify=False)
    print(request.headers)
    return "Success"


if __name__ == "__main__":
    app.run(host='127.0.0.1', port=8082)
