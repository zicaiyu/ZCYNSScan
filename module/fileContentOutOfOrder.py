import random
"""
对文件每行进行乱序重新排序
"""
def ContentOutOfOrder(filePath, outFilePath):
    lines = []
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            lines.append(line)
    random.shuffle(lines)
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


if __name__ == "__main__":
    ContentOutOfOrder("../data/butianIpWeightOne2Three.txt", "../data/orderButianIpWeightOne2Three.txt")