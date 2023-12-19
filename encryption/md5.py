import hashlib
import random
import time
import uuid

"""
删除文件指定内容
"""


def md5(str):
    md5 = hashlib.md5()
    md5.update(str.encode("utf-8"))
    result = md5.hexdigest()
    return result


def generate_timestamp_uuid():
    timestamp = str(int(time.time()*1000))
    random_num = str(random.randint(0, 100000))
    return timestamp + random_num


if __name__ == "__main__":
    # print(md5("1681816727578"))
    print(uuid.uuid4())
    print('6aaa501f-ef85-4a2b-a78a-b691b840977d')
    # print(md5(generate_timestamp_uuid()))