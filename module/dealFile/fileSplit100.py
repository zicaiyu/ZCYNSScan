import os
"""
对文件进行分割
"""


def split(filePath, sub, buf):
    [desFileNmae, extname] = os.path.splitext(filePath)
    newFilename = desFileNmae + "/butianIpWeightGreaterThenThree" + str(sub) + extname
    with open(newFilename, 'wb') as fout:
        fout.write(buf)
        return sub + 1


def fileSplit100(filePath, size):
    with open(filePath, 'rb') as fin:
        buf = fin.read(size)
        sub = 1
        while len(buf) > 0:
            sub = split(filePath, sub, buf)
            buf = fin.read(size)
    print("split ok!")



if __name__ == "__main__":
    fileSplit100("../../data/butianIpWeightGreaterThenThree.txt", 80000)