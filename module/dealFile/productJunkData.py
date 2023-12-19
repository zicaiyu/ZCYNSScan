import random

"""
对文件每行进行排序
"""


def fileContentSort(outFilePath):
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in range(500000):
        outFile.write("x")
    outFile.close()



if __name__ == "__main__":
    fileContentSort("./junkData.txt")
