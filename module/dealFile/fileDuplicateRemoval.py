import random
"""
对文件内容进行去重后乱序排序 
"""
def fileDuplicateRemoval(filePath, outFilePath):
    lines = []
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            lines.append(line)
    lines = list(set(lines))
    random.shuffle(lines)
    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


if __name__ == "__main__":
    fileDuplicateRemoval("../../data/fenbi.txt", "../../data/fenbi.txt")
