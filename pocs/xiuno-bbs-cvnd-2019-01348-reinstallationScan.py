import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"install/"
	headers={'Accept-Encoding': 'deflate'}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and str("/view/js/xiuno.js") in response.text and "Choose Language (选择语言)" in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
