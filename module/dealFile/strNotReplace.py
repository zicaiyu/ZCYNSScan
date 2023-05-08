import random

"""
删除文件指定内容
"""


def strReplace(filePath, outFilePath):
    lines = []
    savestr = [".oocl.com", ".cargosmart.com", ".scheduelingsmart.com", ".bigschedules.com", ".equippool.com"]
    flag = 0
    with open(filePath, 'r', encoding="utf-8") as f:
        for line in f:
            flag = 0
            for save in savestr:
                if save in line:
                    flag = 1
                    break
            if flag == 0:
                continue
            lines.append(line)

    outFile = open(outFilePath, 'w', encoding="utf-8")
    for line in lines:
        outFile.write(line)
    outFile.close()


if __name__ == "__main__":
    strReplace("../../data/1.txt", "../../data/huoxuntongkeji.txt")
