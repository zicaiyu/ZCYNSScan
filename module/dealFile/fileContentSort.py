import random
"""
对文件每行进行排序
"""


def fileContentSort(filePath, outFilePath):
    lines = []
    linesTmp = []
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            linesTmp.append(reverseString(line))
    linesTmp.sort()
    for l in linesTmp:
        lines.append(reverseString(l))

    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


def reverseString(str):
    return str[::-1]


if __name__ == "__main__":
    fileContentSort("../../results/1.txt", "../../results/result.txt")