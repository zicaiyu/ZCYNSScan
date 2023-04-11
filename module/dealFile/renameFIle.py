import os
import re
import sys


def renameall():
    fileList = os.listdir(r"../../pocs")
    currentpath = os.getcwd()
    os.chdir(r"../../pocs")
    for fileName in fileList:
        pat = ".+\.(py)"
        pattern = re.findall(pat, fileName)
        name = os.path.splitext(fileName)[0]
        if "poc-yaml-" in name:
            name = name[9:]
        if "Scan" in name:
            os.rename(fileName, (str(name) + '.' + pattern[0]))
        else:
            os.rename(fileName, (str(name) + 'Scan.' + pattern[0]))
    os.chdir(currentpath)
    sys.stdin.flush()


if __name__ == '__main__':
    renameall()
