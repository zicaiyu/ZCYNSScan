import random
"""
对文件每行进行排序
"""


def fileContentSort(filePath, outFilePath):
    lines = []
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            lines.append(line)
    lines.sort()

    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


if __name__ == "__main__":
    fileContentSort("../../data/tmp.txt", "../../data/result.txt")
