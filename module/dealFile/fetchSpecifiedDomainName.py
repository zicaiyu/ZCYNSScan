import random
"""
对文件内容进行去重后乱序排序
"""
def fetchSpecifiedDomainName(filePath, outFilePath):
    lines = []
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            if "weikayun" in line:
                lines.append(line)
    lines = list(set(lines))
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


if __name__ == "__main__":
    fetchSpecifiedDomainName("../../data/huoxuntong.txt", "../../data/huoxuntongByweikayun.txt")
