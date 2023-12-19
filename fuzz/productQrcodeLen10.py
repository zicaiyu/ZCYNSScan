import random
import string

"""
对文件每行进行乱序重新排序
"""


def fileContentSort(outFilePath):
    outFile = open(outFilePath, 'w', encoding="utf-8")
    strings = []
    characters = string.ascii_letters + string.digits

    for _ in range(1000):
        string_value = ''.join(random.choice(characters) for _ in range(10))
        strings.append(string_value)

    for line in strings:
        outFile.write(str(line) + "\n")
    outFile.close()


if __name__ == "__main__":
    fileContentSort("../fuzz/qrcodeLen10.txt")
