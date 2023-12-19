import random
"""
对文件每行进行乱序重新排序
"""


def fileContentSort(outFilePath):
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for d1 in range(10):
        for d2 in range(10):
            for d3 in range(10):
                for d4 in range(10):
                    four_digit_string = str(d1) + str(d2) + str(d3) + str(d4)
                    outFile.write(str(four_digit_string)+"\n")
    outFile.close()


if __name__ == "__main__":
    fileContentSort("../fuzz/qrcode4num.txt")