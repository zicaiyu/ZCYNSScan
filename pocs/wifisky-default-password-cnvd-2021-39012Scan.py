import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"login.php?action=login&type=admin"
	body="username=admin&password=admin"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0","Content-Type": "application/x-www-form-urlencoded"}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "{\"success\":\"true\", \"data\":{\"id\":1}, \"alert\":\"您正在使用默认密码登录，为保证设备安全，请立即修改密码\"}" in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
