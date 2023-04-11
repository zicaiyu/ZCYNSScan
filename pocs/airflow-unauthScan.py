import requests, re, urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def doCheck(baseurl):
    url = baseurl + "admin/"
    headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
    response = requests.get(url, headers=headers, timeout=5, verify=False)
    if response.status_code == 200 and "<title>Airflow - DAGs</title>" in response.text and "<h2>DAGs</h2>" in response.text:
        return url
    else:
        return False
