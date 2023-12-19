function getW() {
    var e = '{"setLeft":52,"passtime":485,"userresponse":53.69260133075313,"device_id":"56ed3a359092206b372141945315c84e","lot_number":"ee686b3bc2804c13a697a84e7114507d","pow_msg":"1|0|md5|2023-06-27T14:36:33.966197+08:00|4e3430675330dd9a9218e5e70000dfe8|ee686b3bc2804c13a697a84e7114507d||b5dc21dbf2f3d4b3","pow_sign":"c4aad6550c9c39f167b66d2e91042d26","geetest":"captcha","lang":"zh","ep":"123","wst1":"1868844699","t8d4":"rse4","em":{"ph":0,"cp":0,"ek":"11","wd":1,"nt":0,"si":0,"sc":0}}'
    return encodee(e)
}


function getS() {
    var s = get4Str() + get4Str() + get4Str() + get4Str();
    return s;
}


function get4Str() {
    return (65536 * (1 + Math['random']()) | 0)['toString'](16)['substring'](1);
}










