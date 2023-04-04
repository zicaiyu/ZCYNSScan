import hashlib
"""
删除文件指定内容
"""


def md5(str):
    md5 = hashlib.md5()
    md5.update(str.encode("utf-8"))
    result = md5.hexdigest()
    return result


if __name__ == "__main__":
    print(md5("admin-token"))