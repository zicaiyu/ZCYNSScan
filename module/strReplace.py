import random
"""
删除文件指定内容
"""


def strReplace(filePath, outFilePath):
    lines = []
    delstr = [".dangdang.com"]
    flag = 0
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            flag = 0
            for dels in delstr:
                if dels in line:
                    flag = 1
                    break
            if flag == 1:
                continue
            lines.append(line)
    lines.sort()
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


if __name__ == "__main__":
    strReplace("../results/1.txt", "../results/gitScanResult.txt")