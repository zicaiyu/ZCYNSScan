import random
"""
对文件每行进行乱序重新排序
"""


def fileContentSort(outFilePath):
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in range(875025, 875040):
        outFile.write(str(line)+"\n")
    outFile.close()


if __name__ == "__main__":
    fileContentSort("../fuzz/qrcode6num.txt")